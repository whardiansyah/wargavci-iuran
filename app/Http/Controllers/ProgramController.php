<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProgramController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Program::class);

        $search = $request->get('search');
        $status = $request->get('status');

        $query = Program::query()->withCount('anggota');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('kode', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $program = $query->latest()->paginate(10)->withQueryString();

        return view('program.index', compact('program', 'search', 'status'));
    }

    public function create()
    {
        $this->authorize('create', Program::class);

        return view('program.create', [
            'program' => new Program(['status' => 'aktif']),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Program::class);

        Program::create($this->validatedData($request));

        return redirect()->route('program.index')->with('success', 'Program berhasil ditambahkan.');
    }

    public function show(Program $program)
    {
        $this->authorize('view', $program);

        return redirect()->route('anggota.index', ['program_id' => $program->id]);
    }

    public function edit(Program $program)
    {
        $this->authorize('update', $program);

        return view('program.edit', compact('program'));
    }

    public function update(Request $request, Program $program)
    {
        $this->authorize('update', $program);

        $program->update($this->validatedData($request, $program));

        return redirect()->route('program.index')->with('success', 'Program berhasil diperbarui.');
    }

    public function destroy(Program $program)
    {
        $this->authorize('delete', $program);

        $program->delete();

        return redirect()->route('program.index')->with('success', 'Program berhasil dihapus.');
    }

    private function validatedData(Request $request, ?Program $program = null): array
    {
        return $request->validate([
            'kode' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('program', 'kode')->ignore($program?->id),
            ],
            'nama' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['aktif', 'tidak aktif'])],
        ], [
            'nama.required' => 'Nama program harus diisi.',
            'status.required' => 'Status harus dipilih.',
            'kode.unique' => 'Kode program sudah digunakan.',
        ]);
    }
}
