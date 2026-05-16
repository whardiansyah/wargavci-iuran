<?php

namespace App\Http\Controllers;

use App\Models\MasterPenghuni;
use App\Models\PencatatanAir;
use App\Models\MasterConfig;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PencatatanAirController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', PencatatanAir::class);

        $periode = $request->query->has('periode')
            ? $request->get('periode')
            : now()->format('Y-m');
        $nomorRumah = $request->get('nomor_rumah');
        $query = PencatatanAir::with('masterPenghuni')
            ->orderBy('periode_tahun', 'desc')
            ->orderBy('periode_bulan', 'desc');

        if ($periode && preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $periode)) {
            [$year, $month] = explode('-', $periode);
            $query->where('periode_tahun', $year)->where('periode_bulan', $month);
        }

        if ($nomorRumah) {
            $query->whereHas('masterPenghuni', function ($q) use ($nomorRumah) {
                $q->where('nomor_rumah', $nomorRumah);
            });
        }

        $masterPenghunis = MasterPenghuni::orderBy('nomor_rumah')->get();
        $pencatatanAirs = $query->paginate(10)->withQueryString();

        return view('pencatatan_air.index', compact('pencatatanAirs', 'periode', 'nomorRumah', 'masterPenghunis'));
    }

    public function create()
    {
        $this->authorize('create', PencatatanAir::class);

        $masterPenghunis = MasterPenghuni::orderBy('nomor_rumah')->get();
        $hargakubik = MasterConfig::where('code', 'harga-air')->first();
        $hargaabodemen = MasterConfig::where('code', 'abodemen-air')->first();

        return view('pencatatan_air.create', compact('masterPenghunis', 'hargakubik', 'hargaabodemen'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', PencatatanAir::class);

        $validated = $request->validate([
            'master_penghuni_id' => 'required|exists:master_penghuni,id',
            'periode' => ['required', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
            'meter_lalu' => 'required|numeric|min:0',
            'meter_kini' => 'required|numeric|gte:meter_lalu',
        ], [
            'master_penghuni_id.required' => 'Pilih rumah terlebih dahulu',
            'master_penghuni_id.exists' => 'Rumah tidak valid',
            'periode.required' => 'Periode harus diisi',
            'periode.regex' => 'Format periode harus YYYY-MM',
            'meter_lalu.required' => 'Meter lalu harus diisi',
            'meter_kini.required' => 'Meter kini harus diisi',
            'meter_kini.gte' => 'Meter kini harus lebih besar atau sama dengan meter lalu',
        ]);

        [$year, $month] = explode('-', $validated['periode']);

        $request->validate([
            'master_penghuni_id' => [
                Rule::unique('pencatatan_air')->where(function ($query) use ($validated, $year, $month) {
                    return $query->where('master_penghuni_id', $validated['master_penghuni_id'])
                        ->where('periode_bulan', $month)
                        ->where('periode_tahun', $year);
                }),
            ],
        ], [
            'master_penghuni_id.unique' => 'Pencatatan air untuk periode dan rumah ini sudah ada',
        ]);

        PencatatanAir::create([
            'master_penghuni_id' => $validated['master_penghuni_id'],
            'periode_bulan' => (int) $month,
            'periode_tahun' => (int) $year,
            'meter_lalu' => $validated['meter_lalu'],
            'meter_kini' => $validated['meter_kini'],
            'total_tagihan' => $validated['total_tagihan'] ?? $this->getTotal($validated['meter_lalu'], $validated['meter_kini']),
        ]);

        return redirect()->route('pencatatan_air.index')->with('success', 'Data pencatatan air berhasil ditambahkan');
    }

    public function import(Request $request)
    {
        $this->authorize('create', PencatatanAir::class);

        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx',
        ], [
            'file.required' => 'File XLSX harus dipilih',
            'file.mimes' => 'File harus berformat XLSX atau XLS',
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();

        $errors = [];
        $imported = 0;

        foreach ($sheet->getRowIterator(2) as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $cells = [];
            foreach ($cellIterator as $cell) {
                $cells[] = trim((string)$cell->getValue());
            }

            $nomorRumah = $cells[0] ?? '';
            $periode = $cells[1] ?? '';
            $meterLalu = $cells[2] ?? '';
            $meterKini = $cells[3] ?? '';

            if ($nomorRumah === '' && $periode === '' && $meterLalu === '' && $meterKini === '') {
                continue;
            }

            $rowNumber = $row->getRowIndex();

            if ($nomorRumah === '') {
                $errors[] = "Baris {$rowNumber}: nomor rumah kosong.";
                continue;
            }

            $penghuni = MasterPenghuni::where('nomor_rumah', $nomorRumah)->first();
            if (!$penghuni) {
                $errors[] = "Baris {$rowNumber}: nomor rumah '{$nomorRumah}' tidak ditemukan.";
                continue;
            }

            $periode = trim(str_replace([' ', '\\', '/'], ['','-','-'], $periode));
            $year = null;
            $month = null;
            if (preg_match('/^(\d{4})-(0[1-9]|1[0-2])$/', $periode, $matches)) {
                $year = $matches[1];
                $month = $matches[2];
            } elseif (preg_match('/^(0[1-9]|1[0-2])-(\d{4})$/', $periode, $matches)) {
                $month = $matches[1];
                $year = $matches[2];
            }

            if (!$year || !$month) {
                $errors[] = "Baris {$rowNumber}: format periode '{$periode}' tidak valid. Gunakan YYYY-MM atau MM-YYYY.";
                continue;
            }

            $meterLalu = str_replace(',', '.', $meterLalu);
            $meterKini = str_replace(',', '.', $meterKini);

            if (!is_numeric($meterLalu) || !is_numeric($meterKini)) {
                $errors[] = "Baris {$rowNumber}: meter lalu/metro kini harus berupa angka.";
                continue;
            }

            $meterLalu = (float)$meterLalu;
            $meterKini = (float)$meterKini;

            if ($meterKini < $meterLalu) {
                $errors[] = "Baris {$rowNumber}: meter kini tidak boleh lebih kecil dari meter lalu.";
                continue;
            }

            $exists = PencatatanAir::where('master_penghuni_id', $penghuni->id)
                ->where('periode_bulan', (int)$month)
                ->where('periode_tahun', (int)$year)
                ->exists();

            if ($exists) {
                $errors[] = "Baris {$rowNumber}: data untuk rumah {$nomorRumah} pada periode {$year}-{$month} sudah ada.";
                continue;
            }

            PencatatanAir::create([
                'master_penghuni_id' => $penghuni->id,
                'periode_bulan' => (int)$month,
                'periode_tahun' => (int)$year,
                'meter_lalu' => $meterLalu,
                'meter_kini' => $meterKini,
                'total_tagihan' => $this->getTotal($meterLalu, $meterKini),
            ]);

            $imported++;
        }

        $message = "Import selesai. {$imported} baris berhasil ditambahkan.";
        if (count($errors) > 0) {
            return redirect()->route('pencatatan_air.index')
                ->with('success', $message)
                ->with('import_errors', $errors);
        }

        return redirect()->route('pencatatan_air.index')->with('success', $message);
    }

    public function downloadTemplate(Request $request)
    {
        $this->authorize('create', PencatatanAir::class);

        $periode = $request->query->has('periode')
            ? $request->get('periode')
            : now()->format('Y-m');

        $basePeriod = preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', (string) $periode)
            ? Carbon::createFromFormat('Y-m', $periode)->startOfMonth()
            : now()->startOfMonth();
        $targetPeriod = $basePeriod->copy()->addMonth();
        $previousPeriod = $targetPeriod->copy()->subMonth();

        $previousReadings = PencatatanAir::query()
            ->where('periode_bulan', $previousPeriod->month)
            ->where('periode_tahun', $previousPeriod->year)
            ->pluck('meter_kini', 'master_penghuni_id');

        $masterPenghunis = MasterPenghuni::query()
            ->where('status', 'aktif')
            ->orderBy('nomor_rumah')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import Air');

        $headers = ['nomor rumah', 'periode', 'meter lalu', 'meter kini'];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        $sheet->getStyle('A1:D1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FFD9EAD3');

        $row = 2;
        foreach ($masterPenghunis as $penghuni) {
            $sheet->setCellValueExplicit('A' . $row, (string) $penghuni->nomor_rumah, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('B' . $row, $targetPeriod->format('Y-m'));
            $sheet->setCellValue('C' . $row, (float) ($previousReadings[$penghuni->id] ?? 0));
            $sheet->setCellValue('D' . $row, '');
            $row++;
        }

        foreach (range('A', 'D') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'template_import_pencatatan_air_' . $targetPeriod->format('Ym') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function show(PencatatanAir $pencatatanAir)
    {
        $this->authorize('view', $pencatatanAir);

        return view('pencatatan_air.show', compact('pencatatanAir'));
    }

    public function edit(PencatatanAir $pencatatanAir)
    {
        $this->authorize('update', $pencatatanAir);

        $masterPenghunis = MasterPenghuni::orderBy('nomor_rumah')->get();
        $hargakubik = MasterConfig::where('code', 'harga-air')->first();
        $hargaabodemen = MasterConfig::where('code', 'abodemen-air')->first();

        return view('pencatatan_air.edit', compact('pencatatanAir', 'masterPenghunis', 'hargakubik', 'hargaabodemen'));
    }

    public function update(Request $request, PencatatanAir $pencatatanAir)
    {
        $this->authorize('update', $pencatatanAir);

        $validated = $request->validate([
            'master_penghuni_id' => 'required|exists:master_penghuni,id',
            'periode' => ['required', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
            'meter_lalu' => 'required|numeric|min:0',
            'meter_kini' => 'required|numeric|gte:meter_lalu',
        ], [
            'master_penghuni_id.required' => 'Pilih rumah terlebih dahulu',
            'master_penghuni_id.exists' => 'Rumah tidak valid',
            'periode.required' => 'Periode harus diisi',
            'periode.regex' => 'Format periode harus YYYY-MM',
            'meter_lalu.required' => 'Meter lalu harus diisi',
            'meter_kini.required' => 'Meter kini harus diisi',
            'meter_kini.gte' => 'Meter kini harus lebih besar atau sama dengan meter lalu',
        ]);

        [$year, $month] = explode('-', $validated['periode']);

        $request->validate([
            'master_penghuni_id' => [
                Rule::unique('pencatatan_air')->where(function ($query) use ($validated, $year, $month, $pencatatanAir) {
                    return $query->where('master_penghuni_id', $validated['master_penghuni_id'])
                        ->where('periode_bulan', $month)
                        ->where('periode_tahun', $year)
                        ->where('id', '<>', $pencatatanAir->id);
                }),
            ],
        ], [
            'master_penghuni_id.unique' => 'Pencatatan air untuk periode dan rumah ini sudah ada',
        ]);

        $pencatatanAir->update([
            'master_penghuni_id' => $validated['master_penghuni_id'],
            'periode_bulan' => (int) $month,
            'periode_tahun' => (int) $year,
            'meter_lalu' => $validated['meter_lalu'],
            'meter_kini' => $validated['meter_kini'],
            'total_tagihan' => $validated['total_tagihan'] ?? $this->getTotal($validated['meter_lalu'], $validated['meter_kini']),
        ]);

        return redirect()->route('pencatatan_air.index')->with('success', 'Data pencatatan air berhasil diperbarui');
    }

    public function destroy(PencatatanAir $pencatatanAir)
    {
        $this->authorize('delete', $pencatatanAir);

        $pencatatanAir->delete();

        return redirect()->route('pencatatan_air.index')->with('success', 'Data pencatatan air berhasil dihapus');
    }

    public function exportExcel(Request $request)
    {
        $this->authorize('export', PencatatanAir::class);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'ID Pencatatan Air');
        $sheet->setCellValue('B1', 'Nomor Rumah');
        $sheet->setCellValue('C1', 'Nama Penghuni');
        $sheet->setCellValue('D1', 'Periode Bulan');
        $sheet->setCellValue('E1', 'Periode Tahun');
        $sheet->setCellValue('F1', 'Meter Lalu');
        $sheet->setCellValue('G1', 'Meter Kini');
        $sheet->setCellValue('H1', 'Total Tagihan');

        $query = PencatatanAir::with('masterPenghuni')
            ->orderBy('periode_tahun', 'desc')
            ->orderBy('periode_bulan', 'desc');

        $periode = $request->query->has('periode')
            ? $request->get('periode')
            : now()->format('Y-m');
        if ($periode && preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $periode)) {
            [$year, $month] = explode('-', $periode);
            $query->where('periode_tahun', $year)->where('periode_bulan', $month);
        }

        $nomorRumah = $request->get('nomor_rumah');
        if ($nomorRumah) {
            $query->whereHas('masterPenghuni', function ($q) use ($nomorRumah) {
                $q->where('nomor_rumah', $nomorRumah);
            });
        }

        $pencatatanAirs = $query->get();

        $row = 2;
        foreach ($pencatatanAirs as $pencatatanAir) {
            $sheet->setCellValue('A' . $row, $pencatatanAir->id);
            $sheet->setCellValue('B' . $row, $pencatatanAir->masterPenghuni->nomor_rumah);
            $sheet->setCellValue('C' . $row, $pencatatanAir->masterPenghuni->nama_depan . ' ' . $pencatatanAir->masterPenghuni->nama_belakang);
            $sheet->setCellValue('D' . $row, $pencatatanAir->periode_bulan);
            $sheet->setCellValue('E' . $row, $pencatatanAir->periode_tahun);
            $sheet->setCellValue('F' . $row, $pencatatanAir->meter_lalu);
            $sheet->setCellValue('G' . $row, $pencatatanAir->meter_kini);
            $sheet->setCellValue('H' . $row, $pencatatanAir->total_tagihan);
            $row++;
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'pencatatan_air_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function getTotal($meterlalu, $meterkini)
    {
        // Ambil harga per kubik
        $hargakubik = MasterConfig::where('code', 'harga-air')->first();
        $hargaabodemen = MasterConfig::where('code', 'abodemen-air')->first();

        return ceil((($meterkini - $meterlalu) * $hargakubik->value + $hargaabodemen->value)/1000)*1000;
    }
}
