<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AnggotaController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Anggota::class);

        $search = $request->get('search');
        $status = $request->get('status');
        $jenisKelamin = $request->get('jenis_kelamin');
        $programId = $request->get('program_id');

        $query = Anggota::query()->with('program');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('no_hp', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($jenisKelamin) {
            $query->where('jenis_kelamin', $jenisKelamin);
        }

        if ($programId) {
            $query->where('program_id', $programId);
        }

        $anggota = $query->latest()->paginate(10)->withQueryString();
        $programList = Program::orderBy('nama')->get();
        $selectedProgram = $programId ? $programList->firstWhere('id', (int) $programId) : null;

        return view('anggota.index', compact('anggota', 'search', 'status', 'jenisKelamin', 'programId', 'programList', 'selectedProgram'));
    }

    public function create()
    {
        $this->authorize('create', Anggota::class);

        return view('anggota.create', [
            'anggota' => new Anggota(['status' => 'aktif']),
            'programList' => Program::where('status', 'aktif')->orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Anggota::class);

        Anggota::create($this->validatedData($request));

        return redirect()->route('anggota.index')->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function show(Anggota $anggota)
    {
        $this->authorize('view', $anggota);

        return view('anggota.show', compact('anggota'));
    }

    public function edit(Anggota $anggota)
    {
        $this->authorize('update', $anggota);

        return view('anggota.edit', [
            'anggota' => $anggota,
            'programList' => Program::orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, Anggota $anggota)
    {
        $this->authorize('update', $anggota);

        $anggota->update($this->validatedData($request, $anggota));

        return redirect()->route('anggota.index')->with('success', 'Anggota berhasil diperbarui.');
    }

    public function destroy(Anggota $anggota)
    {
        $this->authorize('delete', $anggota);

        $anggota->delete();

        return redirect()->route('anggota.index')->with('success', 'Anggota berhasil dihapus.');
    }

    private function validatedData(Request $request, ?Anggota $anggota = null): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'program_id' => ['nullable', 'exists:program,id'],
            'nik' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('anggota', 'nik')->ignore($anggota?->id),
            ],
            'jenis_kelamin' => ['required', Rule::in(['L', 'P'])],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'alamat' => ['nullable', 'string'],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'status' => ['required', Rule::in(['aktif', 'tidak aktif'])],
            'keterangan' => ['nullable', 'string'],
        ], [
            'nama.required' => 'Nama anggota harus diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin harus dipilih.',
            'status.required' => 'Status harus dipilih.',
            'nik.unique' => 'NIK sudah digunakan anggota lain.',
        ]);
    }
}
