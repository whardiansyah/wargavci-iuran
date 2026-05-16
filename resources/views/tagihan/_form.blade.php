<div class="mb-3">
    <label for="master_penghuni_id" class="form-label">Rumah <span class="text-danger">*</span></label>
    <select id="master_penghuni_id" name="master_penghuni_id" class="form-select @error('master_penghuni_id') is-invalid @enderror" required>
        <option value="">-- Pilih Rumah --</option>
        @foreach ($masterPenghunis as $penghuni)
            <option value="{{ $penghuni->id }}" {{ old('master_penghuni_id', $tagihan->master_penghuni_id) == $penghuni->id ? 'selected' : '' }}>
                {{ $penghuni->nomor_rumah }} - {{ $penghuni->kepala_keluarga }}
            </option>
        @endforeach
    </select>
    @error('master_penghuni_id')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="periode" class="form-label">Periode <span class="text-danger">*</span></label>
    <input type="month" id="periode" name="periode" class="form-control @error('periode') is-invalid @enderror" value="{{ old('periode', $tagihan->periode) }}" required>
    @error('periode')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="code" class="form-label">Code <span class="text-danger">*</span></label>
    <select id="code" name="code" class="form-select @error('code') is-invalid @enderror" required>
        <option value="">-- Pilih Code --</option>
        @foreach ($iuranConfigs as $config)
            <option value="{{ $config->code }}" data-nilai="{{ (int) $config->value }}" {{ old('code', $tagihan->code) == $config->code ? 'selected' : '' }}>
                {{ $config->code }} - Rp {{ number_format((int) $config->value, 0, ',', '.') }}
            </option>
        @endforeach
    </select>
    @error('code')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="nilai" class="form-label">Nilai <span class="text-danger">*</span></label>
    <input type="number" min="0" id="nilai" name="nilai" class="form-control @error('nilai') is-invalid @enderror" value="{{ old('nilai', $tagihan->nilai) }}" required>
    @error('nilai')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="status_bayar" class="form-label">Status Bayar <span class="text-danger">*</span></label>
    <select id="status_bayar" name="status_bayar" class="form-select @error('status_bayar') is-invalid @enderror" required>
        <option value="belum" {{ old('status_bayar', $tagihan->status_bayar) === 'belum' ? 'selected' : '' }}>Belum</option>
        <option value="sudah" {{ old('status_bayar', $tagihan->status_bayar) === 'sudah' ? 'selected' : '' }}>Sudah</option>
    </select>
    @error('status_bayar')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> Simpan
    </button>
    <a href="{{ route('tagihan.index') }}" class="btn btn-secondary">
        <i class="fas fa-times"></i> Batal
    </a>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var codeInput = document.getElementById('code');
        var nilaiInput = document.getElementById('nilai');

        codeInput.addEventListener('change', function () {
            var selected = codeInput.options[codeInput.selectedIndex];
            var nilai = selected.getAttribute('data-nilai');
            if (nilai !== null && nilai !== '') {
                nilaiInput.value = nilai;
            }
        });
    });
</script>
