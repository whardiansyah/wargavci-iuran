<?php

namespace App\Http\Controllers;

use App\Models\MasterPenghuni;
use App\Models\Penyewa;
use Illuminate\Http\Request;

class PenyewaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Penyewa::class);
        $penyewas = Penyewa::with('masterPenghuni');

        if ($request->has('search') && $request->search != '') {
            $penyewas->where('nama_penyewa', 'like', '%' . $request->search . '%')
                     ->orWhereHas('masterPenghuni', function ($query) use ($request) {
                         $query->where('nomor_rumah', 'like', '%' . $request->search . '%');
                     });
        }

        if ($request->has('status') && $request->status != '') {
            $penyewas->where('status', $request->status);
        }

        $penyewas = $penyewas->latest()->paginate(10);

        return view('penyewa.index', compact('penyewas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Penyewa::class);
        $masterPenghunis = MasterPenghuni::all();
        return view('penyewa.create', compact('masterPenghunis'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'master_penghuni_id' => 'required|exists:master_penghuni,id',
            'nama_penyewa' => 'required|string|max:255',
            'tgl_mulai_sewa' => 'required|date',
            'tgl_selesai_sewa' => 'required|date|after_or_equal:tgl_mulai_sewa',
            'jml_anggota' => 'required|integer|min:1',
            'status' => 'required|in:aktif,tidak aktif',
        ]);

        Penyewa::create($request->all());

        return redirect()->route('penyewa.index')->with('success', 'Penyewa berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Penyewa $penyewa)
    {
        return view('penyewa.show', compact('penyewa'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Penyewa $penyewa)
    {
        $this->authorize('update', $penyewa);
        $masterPenghunis = MasterPenghuni::all();
        return view('penyewa.edit', compact('penyewa', 'masterPenghunis'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Penyewa $penyewa)
    {
        $this->authorize('update', $penyewa);
        $request->validate([
            'master_penghuni_id' => 'required|exists:master_penghuni,id',
            'nama_penyewa' => 'required|string|max:255',
            'tgl_mulai_sewa' => 'required|date',
            'tgl_selesai_sewa' => 'required|date|after_or_equal:tgl_mulai_sewa',
            'jml_anggota' => 'required|integer|min:1',
            'status' => 'required|in:aktif,tidak aktif',
        ]);

        $penyewa->update($request->all());

        return redirect()->route('penyewa.index')->with('success', 'Penyewa berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Penyewa $penyewa)
    {
        $penyewa->delete();

        return redirect()->route('penyewa.index')->with('success', 'Penyewa berhasil dihapus.');
    }
}
