<?php

namespace App\Http\Controllers;

use App\Models\MasterPenghuni;
use Illuminate\Http\Request;

class MasterPenghuniController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', MasterPenghuni::class);
        $search = $request->get('search');
        $status = $request->get('status');
        if (! in_array($status, ['aktif', 'tidak aktif'], true)) {
            $status = null;
        }

        $query = MasterPenghuni::query();
        
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kepala_keluarga', 'like', '%' . $search . '%')
                    ->orWhere('kontak_person', 'like', '%' . $search . '%')
                    ->orWhere('nomor_rumah', 'like', '%' . $search . '%');
            });
        }

        if ($status) {
            $query->where('status', $status);
        }
        
        $masterPenghunis = $query->paginate(10)->withQueryString();
        return view('master_penghunis.index', compact('masterPenghunis', 'search', 'status'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', MasterPenghuni::class);
        return view('master_penghunis.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', MasterPenghuni::class);
        $validated = $request->validate([
            'kepala_keluarga' => 'required|string|max:255|unique:master_penghuni',
            'kontak_person' => 'nullable|string|max:255',
            'nomor_rumah' => 'required|string|max:255|unique:master_penghuni',
            'status_rumah' => 'required|in:pribadi,sewa,kosong',
            'status' => 'required|in:aktif,tidak aktif',
        ], [
            'kepala_keluarga.required' => 'Nama kepala keluarga harus diisi',
            'kepala_keluarga.unique' => 'Nama kepala keluarga sudah ada',
            'nomor_rumah.required' => 'Nomor rumah harus diisi',
            'nomor_rumah.unique' => 'Nomor rumah sudah ada',
            'status_rumah.required' => 'Status rumah harus dipilih',
            'status.required' => 'Status aktif harus dipilih',
        ]);

        MasterPenghuni::create($validated);
        return redirect()->route('master_penghunis.index')->with('success', 'Data rumah berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(MasterPenghuni $masterPenghuni)
    {
        $this->authorize('view', $masterPenghuni);
        return view('master_penghunis.show', compact('masterPenghuni'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterPenghuni $masterPenghuni)
    {
        $this->authorize('update', $masterPenghuni);
        return view('master_penghunis.edit', compact('masterPenghuni'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterPenghuni $masterPenghuni)
    {
        $this->authorize('update', $masterPenghuni);
        $validated = $request->validate([
            'kepala_keluarga' => 'required|string|max:255|unique:master_penghuni,kepala_keluarga,' . $masterPenghuni->id,
            'kontak_person' => 'nullable|string|max:255',
            'nomor_rumah' => 'required|string|max:255|unique:master_penghuni,nomor_rumah,' . $masterPenghuni->id,
            'status_rumah' => 'required|in:pribadi,sewa,kosong',
            'status' => 'required|in:aktif,tidak aktif',
        ], [
            'kepala_keluarga.required' => 'Nama kepala keluarga harus diisi',
            'kepala_keluarga.unique' => 'Nama kepala keluarga sudah ada',
            'nomor_rumah.required' => 'Nomor rumah harus diisi',
            'nomor_rumah.unique' => 'Nomor rumah sudah ada',
            'status_rumah.required' => 'Status rumah harus dipilih',
            'status.required' => 'Status aktif harus dipilih',
        ]);

        $masterPenghuni->update($validated);
        return redirect()->route('master_penghunis.index')->with('success', 'Data rumah berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterPenghuni $masterPenghuni)
    {
        $this->authorize('delete', $masterPenghuni);
        $masterPenghuni->delete();
        return redirect()->route('master_penghunis.index')->with('success', 'Data rumah berhasil dihapus');
    }
}
