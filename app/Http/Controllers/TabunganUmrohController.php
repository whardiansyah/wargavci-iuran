<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\TabunganUmroh;
use Illuminate\Http\Request;

class TabunganUmrohController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', TabunganUmroh::class);

        $namaAnggota = $request->get('nama_anggota');

        $query = TabunganUmroh::query()
            ->with('anggota')
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc');

        if ($namaAnggota !== null && trim($namaAnggota) !== '') {
            $query->whereHas('anggota', function ($q) use ($namaAnggota) {
                $q->where('nama', 'like', '%' . trim($namaAnggota) . '%');
            });
        }

        $tabunganUmroh = $query->paginate(10)->withQueryString();

        return view('tabungan_umroh.index', compact('tabunganUmroh', 'namaAnggota'));
    }

    public function create()
    {
        $this->authorize('create', TabunganUmroh::class);

        $anggotaList = Anggota::orderBy('nama')->get();

        return view('tabungan_umroh.create', compact('anggotaList'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', TabunganUmroh::class);

        TabunganUmroh::create($this->validatedData($request));

        return redirect()->route('tabungan_umroh.index')->with('success', 'Tabungan umroh berhasil ditambahkan');
    }

    public function show(TabunganUmroh $tabunganUmroh)
    {
        $this->authorize('view', $tabunganUmroh);

        $tabunganUmroh->load('anggota');

        return view('tabungan_umroh.show', compact('tabunganUmroh'));
    }

    public function edit(TabunganUmroh $tabunganUmroh)
    {
        $this->authorize('update', $tabunganUmroh);

        $anggotaList = Anggota::orderBy('nama')->get();

        return view('tabungan_umroh.edit', compact('tabunganUmroh', 'anggotaList'));
    }

    public function update(Request $request, TabunganUmroh $tabunganUmroh)
    {
        $this->authorize('update', $tabunganUmroh);

        $tabunganUmroh->update($this->validatedData($request));

        return redirect()->route('tabungan_umroh.index')->with('success', 'Tabungan umroh berhasil diperbarui');
    }

    public function destroy(TabunganUmroh $tabunganUmroh)
    {
        $this->authorize('delete', $tabunganUmroh);

        $tabunganUmroh->delete();

        return redirect()->route('tabungan_umroh.index')->with('success', 'Tabungan umroh berhasil dihapus');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'anggota_id' => 'required|exists:anggota,id',
            'tanggal' => 'required|date',
            'nominal' => 'required|integer|min:1',
            'cara_setor' => 'required|string|max:50',
            'keterangan' => 'nullable|string',
        ], [
            'anggota_id.required' => 'Anggota harus dipilih',
            'anggota_id.exists' => 'Anggota tidak ditemukan',
            'tanggal.required' => 'Tanggal harus diisi',
            'nominal.required' => 'Nominal harus diisi',
            'nominal.min' => 'Nominal harus lebih dari 0',
            'cara_setor.required' => 'Cara setor harus diisi',
        ]);
    }
}
