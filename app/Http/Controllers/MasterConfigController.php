<?php

namespace App\Http\Controllers;

use App\Models\MasterConfig;
use Illuminate\Http\Request;

class MasterConfigController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', MasterConfig::class);

        $search = $request->get('search');
        $query = MasterConfig::query();

        if ($search) {
            $query->where('code', 'like', "%{$search}%")
                  ->orWhere('value', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
        }

        $masterConfigs = $query->latest()->paginate(10);
        return view('master_configs.index', compact('masterConfigs', 'search'));
    }

    public function create()
    {
        $this->authorize('create', MasterConfig::class);
        return view('master_configs.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', MasterConfig::class);

        $validated = $request->validate([
            'code' => 'required|string|max:100|unique:master_configs,code',
            'value' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'deskripsi' => 'nullable|string|max:500',
        ]);

        MasterConfig::create($validated);

        return redirect()->route('master_configs.index')->with('success', 'Konfigurasi berhasil ditambahkan.');
    }

    public function show(MasterConfig $masterConfig)
    {
        $this->authorize('view', $masterConfig);
        return view('master_configs.show', compact('masterConfig'));
    }

    public function edit(MasterConfig $masterConfig)
    {
        $this->authorize('update', $masterConfig);
        return view('master_configs.edit', compact('masterConfig'));
    }

    public function update(Request $request, MasterConfig $masterConfig)
    {
        $this->authorize('update', $masterConfig);

        $validated = $request->validate([
            'code' => 'required|string|max:100|unique:master_configs,code,' . $masterConfig->id,
            'value' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'deskripsi' => 'nullable|string|max:500',
        ]);

        $masterConfig->update($validated);

        return redirect()->route('master_configs.index')->with('success', 'Konfigurasi berhasil diperbarui.');
    }

    public function destroy(MasterConfig $masterConfig)
    {
        $this->authorize('delete', $masterConfig);

        $masterConfig->delete();

        return redirect()->route('master_configs.index')->with('success', 'Konfigurasi berhasil dihapus.');
    }
}
