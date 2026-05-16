@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Pencatatan Air</h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Validasi Gagal!</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('pencatatan_air.update', $pencatatanAir) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="master_penghuni_id" class="form-label">Rumah <span class="text-danger">*</span></label>
                            <select id="master_penghuni_id" name="master_penghuni_id" class="form-select select2 @error('master_penghuni_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Rumah --</option>
                                @foreach ($masterPenghunis as $penghuni)
                                    <option value="{{ $penghuni->id }}" {{ old('master_penghuni_id', $pencatatanAir->master_penghuni_id) == $penghuni->id ? 'selected' : '' }}>
                                        {{ $penghuni->nomor_rumah }} - {{ $penghuni->kepala_keluarga }}
                                    </option>
                                @endforeach
                            </select>
                            @error('master_penghuni_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="periode" class="form-label">Periode (Bulan-Tahun) <span class="text-danger">*</span></label>
                            <input type="month" id="periode" name="periode" class="form-control @error('periode') is-invalid @enderror" value="{{ old('periode', sprintf('%04d-%02d', $pencatatanAir->periode_tahun, $pencatatanAir->periode_bulan)) }}" required>
                            @error('periode')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="meter_lalu" class="form-label">Meter Lalu <span class="text-danger">*</span></label>
                            <input type="number" min="0" step="0.01" id="meter_lalu" name="meter_lalu" class="form-control @error('meter_lalu') is-invalid @enderror" value="{{ old('meter_lalu', $pencatatanAir->meter_lalu) }}" required>
                            @error('meter_lalu')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="meter_kini" class="form-label">Meter Kini <span class="text-danger">*</span></label>
                            <input type="number" min="0" step="0.01" id="meter_kini" name="meter_kini" class="form-control @error('meter_kini') is-invalid @enderror" value="{{ old('meter_kini', $pencatatanAir->meter_kini) }}" required>
                            @error('meter_kini')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="pemakaian" class="form-label">Pemakaian</label>
                            <input type="number" step="0.01" id="pemakaian" name="pemakaian" class="form-control" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="total_tagihan" class="form-label">Total Tagihan Air<span class="text-danger">*</span></label>
                            <input type="number" min="0" id="total_tagihan" name="total_tagihan" class="form-control @error('total_tagihan') is-invalid @enderror" value="{{ old('total_tagihan', $pencatatanAir->total_tagihan) }}" readonly>
                            @error('total_tagihan')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('pencatatan_air.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('.select2').select2({
            width: '100%',
            placeholder: '-- Pilih Rumah --',
        });

        function updatePemakaian() {
            var meterLaluValue = document.getElementById('meter_lalu').value;
            var meterKiniValue = document.getElementById('meter_kini').value;
            if (meterLaluValue === '' || meterKiniValue === '') {
                document.getElementById('pemakaian').value = '';
                return;
            }
            var meterLalu = parseFloat(meterLaluValue);
            var meterKini = parseFloat(meterKiniValue);
            var pemakaian = meterKini - meterLalu;
            document.getElementById('pemakaian').value = pemakaian.toFixed(2);

            var hargaKubik = {{ $hargakubik->value ?? 0 }};
            var hargaAbodemen = {{ $hargaabodemen->value ?? 0 }};
            var totalTagihan = (pemakaian * hargaKubik) + hargaAbodemen;
            document.getElementById('total_tagihan').value = Math.ceil(totalTagihan / 1000) * 1000;
        }

        document.getElementById('meter_lalu').addEventListener('input', updatePemakaian);
        document.getElementById('meter_kini').addEventListener('input', updatePemakaian);
        updatePemakaian();
    });
</script>
@endsection
