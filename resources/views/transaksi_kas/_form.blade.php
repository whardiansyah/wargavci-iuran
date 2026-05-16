@php
    $periodeValue = old('periode', isset($transaksiKas) && $transaksiKas->periode_tahun && $transaksiKas->periode_bulan ? sprintf('%04d-%02d', $transaksiKas->periode_tahun, $transaksiKas->periode_bulan) : '');
@endphp

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="tanggal" class="form-label">Tanggal</label>
        <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', isset($transaksiKas) ? $transaksiKas->tanggal?->format('Y-m-d') : '') }}" class="form-control @error('tanggal') is-invalid @enderror">
        @error('tanggal')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="kode" class="form-label">Kode</label>
        <input type="text" name="kode" id="kode" value="{{ old('kode', $transaksiKas->kode ?? '') }}" maxlength="20" class="form-control @error('kode') is-invalid @enderror">
        @error('kode')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="nomor_ref" class="form-label">Nomor Ref</label>
        <input type="text" name="nomor_ref" id="nomor_ref" value="{{ old('nomor_ref', $transaksiKas->nomor_ref ?? '') }}" maxlength="50" class="form-control @error('nomor_ref') is-invalid @enderror">
        @error('nomor_ref')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mb-3">
    <label for="deskripsi" class="form-label">Deskripsi</label>
    <input type="text" name="deskripsi" id="deskripsi" value="{{ old('deskripsi', $transaksiKas->deskripsi ?? '') }}" class="form-control @error('deskripsi') is-invalid @enderror" required>
    @error('deskripsi')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="keterangan" class="form-label">Keterangan</label>
    <textarea name="keterangan" id="keterangan" rows="3" class="form-control @error('keterangan') is-invalid @enderror">{{ old('keterangan', $transaksiKas->keterangan ?? '') }}</textarea>
    @error('keterangan')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="kredit" class="form-label">Kredit</label>
        <input type="number" name="kredit" id="kredit" value="{{ old('kredit', $transaksiKas->kredit ?? 0) }}" min="0" class="form-control @error('kredit') is-invalid @enderror" required>
        @error('kredit')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="debet" class="form-label">Debet</label>
        <input type="number" name="debet" id="debet" value="{{ old('debet', $transaksiKas->debet ?? 0) }}" min="0" class="form-control @error('debet') is-invalid @enderror" required>
        @error('debet')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="saldo" class="form-label">Saldo</label>
        <input type="number" name="saldo" id="saldo" value="{{ old('saldo', $transaksiKas->saldo ?? 0) }}" min="0" class="form-control @error('saldo') is-invalid @enderror" required>
        @error('saldo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="jenis" class="form-label">Jenis</label>
        <select name="jenis" id="jenis" class="form-select @error('jenis') is-invalid @enderror" required>
            <option value="transaksi" {{ old('jenis', $transaksiKas->jenis ?? 'transaksi') === 'transaksi' ? 'selected' : '' }}>Transaksi</option>
            <option value="saldo_awal" {{ old('jenis', $transaksiKas->jenis ?? '') === 'saldo_awal' ? 'selected' : '' }}>Saldo Awal</option>
        </select>
        @error('jenis')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="periode" class="form-label">Periode</label>
        <input type="month" name="periode" id="periode" value="{{ $periodeValue }}" class="form-control @error('periode') is-invalid @enderror">
        @error('periode')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> Simpan
    </button>
    <a href="{{ route('transaksi_kas.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>
