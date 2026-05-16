<div class="mb-3">
    <label for="anggota_id" class="form-label">Anggota <span class="text-danger">*</span></label>
    <select name="anggota_id" id="anggota_id" class="form-select select2-anggota @error('anggota_id') is-invalid @enderror" required>
        <option value="">-- Pilih Anggota --</option>
        @foreach ($anggotaList as $anggota)
            <option value="{{ $anggota->id }}" {{ old('anggota_id', $tabunganUmroh->anggota_id ?? '') == $anggota->id ? 'selected' : '' }}>
                {{ $anggota->nama }}
            </option>
        @endforeach
    </select>
    @error('anggota_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('.select2-anggota').select2({
            width: '100%',
            placeholder: '-- Pilih Anggota --',
            allowClear: true,
        });
    });
</script>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
        <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', isset($tabunganUmroh) ? $tabunganUmroh->tanggal?->format('Y-m-d') : '') }}" class="form-control @error('tanggal') is-invalid @enderror" required>
        @error('tanggal')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="nominal" class="form-label">Nominal <span class="text-danger">*</span></label>
        <input type="number" name="nominal" id="nominal" value="{{ old('nominal', $tabunganUmroh->nominal ?? '') }}" min="1" class="form-control @error('nominal') is-invalid @enderror" required>
        @error('nominal')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mb-3">
    <label for="cara_setor" class="form-label">Cara Setor <span class="text-danger">*</span></label>
    <input type="text" name="cara_setor" id="cara_setor" value="{{ old('cara_setor', $tabunganUmroh->cara_setor ?? '') }}" maxlength="50" class="form-control @error('cara_setor') is-invalid @enderror" required placeholder="Contoh: Tunai, Transfer, dll">
    @error('cara_setor')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="keterangan" class="form-label">Keterangan</label>
    <textarea name="keterangan" id="keterangan" rows="3" class="form-control @error('keterangan') is-invalid @enderror">{{ old('keterangan', $tabunganUmroh->keterangan ?? '') }}</textarea>
    @error('keterangan')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> Simpan
    </button>
    <a href="{{ route('tabungan_umroh.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>
