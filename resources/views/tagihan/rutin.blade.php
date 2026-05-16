@extends('layouts.admin')

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet">
<style>
    .select2-container--default .select2-selection--single {
        height: calc(1.5em + 0.75rem + 2px);
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        padding: 0.375rem 0.75rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 1.5;
        padding-left: 0;
        color: #212529;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(1.5em + 0.75rem + 2px);
    }
    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Tagihan Rutin</h5>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
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

                    <form method="GET" action="{{ route('tagihan.rutin') }}" class="row g-2 mb-3">
                        <div class="col-md-3">
                            <input type="month" name="periode" class="form-control" value="{{ $periode ?? '' }}" placeholder="Filter periode">
                        </div>
                        <div class="col-md-4">
                            <select name="nomor_rumah" id="select2-nomor-rumah" class="form-select" style="width: 100%;">
                                <option value="">-- Semua Rumah --</option>
                                @foreach ($masterPenghunis as $penghuni)
                                    <option value="{{ $penghuni->nomor_rumah }}" {{ ($nomorRumah ?? '') == $penghuni->nomor_rumah ? 'selected' : '' }}>
                                        {{ $penghuni->nomor_rumah }} - {{ $penghuni->kepala_keluarga }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="status_bayar" class="form-select">
                                <option value="">-- Semua Status --</option>
                                <option value="belum" {{ ($statusBayar ?? '') === 'belum' ? 'selected' : '' }}>Belum</option>
                                <option value="sudah" {{ ($statusBayar ?? '') === 'sudah' ? 'selected' : '' }}>Sudah</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            @if (request()->hasAny(['periode', 'nomor_rumah', 'status_bayar']))
                                <a href="{{ route('tagihan.rutin') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Reset
                                </a>
                            @endif
                        </div>
                    </form>

                    @if ($rows->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Rumah</th>
                                        <th>Kepala Keluarga</th>
                                        @foreach ($codes as $code)
                                            <th class="text-end">{{ $code }}</th>
                                        @endforeach
                                        <th>Status Bayar</th>
                                        <th class="text-end">Total</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($rows as $index => $row)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $row['masterPenghuni']?->nomor_rumah ?? '-' }}</td>
                                            <td>{{ $row['masterPenghuni']?->kepala_keluarga ?? '-' }}</td>
                                            @foreach ($codes as $code)
                                                <td class="text-end">Rp {{ number_format($row['values'][$code] ?? 0, 0, ',', '.') }}</td>
                                            @endforeach
                                            <td>
                                                <span class="badge {{ $row['statusBayar'] === 'sudah' ? 'bg-success' : 'bg-warning text-dark' }}">
                                                    {{ $row['statusBayar'] === 'sudah' ? 'Sudah' : 'Belum' }}
                                                </span>
                                            </td>
                                            <td class="text-end fw-bold">Rp {{ number_format($row['total'], 0, ',', '.') }}</td>
                                            <td>
                                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#tagihanRutinModal{{ $index }}" title="Lihat">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="3">Total</th>
                                        @foreach ($codes as $code)
                                            <th class="text-end">Rp {{ number_format($columnTotals[$code] ?? 0, 0, ',', '.') }}</th>
                                        @endforeach
                                        <th></th>
                                        <th class="text-end">Rp {{ number_format($grandTotal, 0, ',', '.') }}</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        @foreach ($rows as $index => $row)
                            @php
                                $penghuni = $row['masterPenghuni'];
                                $air = $row['pencatatanAir'];
                                $pembayaran = $row['pembayaran'];
                                $pemakaian = $air ? $air->meter_kini - $air->meter_lalu : 0;
                                $tagihanAir = $row['values']['iuran-air'] ?? 0;
                                $tanggalBayar = $pembayaran->tanggal_bayar ? $pembayaran->tanggal_bayar->format('Y-m-d') : now()->toDateString();
                                $sisaBayar = $row['sisaBayar'];
                                $totalTagihan = $row['totalTagihan'];
                            @endphp
                            <div class="modal fade" id="tagihanRutinModal{{ $index }}" tabindex="-1" aria-labelledby="tagihanRutinModalLabel{{ $index }}" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="tagihanRutinModalLabel{{ $index }}">Detail Tagihan Rutin</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="invoice-preview">
                                                <div class="invoice-header">
                                                    <img src="{{ asset('villa-cilame-logo.png') }}" alt="Villa Cilame Indah" class="invoice-logo">
                                                    <div class="invoice-title">
                                                        <h4>TAGIHAN IPL &amp; AIR BERSAMA</h4>
                                                        <h4>KOMPLEK VILLA CILAME INDAH 2</h4>
                                                    </div>
                                                </div>
                                                <div class="invoice-band"></div>
                                                <div class="invoice-info">
                                                    <div class="invoice-label">No Rumah</div>
                                                    <div class="invoice-value">{{ $penghuni?->nomor_rumah ?? '-' }} - {{ $penghuni?->kepala_keluarga ?? '-' }}</div>
                                                    <div class="invoice-label">Nama KK</div>
                                                    <div class="invoice-value">{{ $penghuni?->kepala_keluarga ?? '-' }}</div>
                                                    <div class="invoice-label">Periode</div>
                                                    <div class="invoice-value">{{ $periodeBulan }} <span class="invoice-year">{{ $periodeTahun }}</span></div>
                                                </div>

                                                <table class="invoice-table">
                                                    <tbody>
                                                        <tr>
                                                            <td>METERAN AWAL</td>
                                                            <td>:</td>
                                                            <td>{{ number_format($air?->meter_lalu ?? 0, 2, ',', '.') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>METERAN AKHIR</td>
                                                            <td>:</td>
                                                            <td>{{ number_format($air?->meter_kini ?? 0, 2, ',', '.') }}</td>
                                                        </tr>
                                                        <tr class="fw-bold">
                                                            <td>TOTAL PEMAKAIAN AIR</td>
                                                            <td>:</td>
                                                            <td>{{ number_format($pemakaian, 2, ',', '.') }}</td>
                                                        </tr>
                                                        <tr class="invoice-total-air">
                                                            <td>TOTAL TAGIHAN AIR</td>
                                                            <td>:</td>
                                                            <td>Rp{{ number_format($tagihanAir, 0, ',', '.') }}</td>
                                                        </tr>
                                                        @foreach ($codes as $code)
                                                            @continue($code === 'iuran-air')
                                                            <tr>
                                                                <td>{{ strtoupper(str_replace('-', ' ', $code)) }}</td>
                                                                <td>:</td>
                                                                <td>Rp{{ number_format($row['values'][$code] ?? 0, 0, ',', '.') }}</td>
                                                            </tr>
                                                        @endforeach
                                                        @if ($sisaBayar != 0)
                                                        <tr class="fw-bold">
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                        </tr>
                                                        <tr style="color: {{ $sisaBayar < 0 ? '#d9534f' : '#5cb85c' }};">
                                                            <td>{{ $sisaBayar < 0 ? 'Kekurangan Bayar' : ' Kelebihan Bayar' }}</td>
                                                            <td>:</td>
                                                            <td>Rp{{ number_format($sisaBayar, 0, ',', '.') }}</td>
                                                        </tr>
                                                        @endif
                                                        <tr class="invoice-grand-total">
                                                            <td>TOTAL TAGIHAN</td>
                                                            <td>:</td>
                                                            <td>Rp{{ number_format($totalTagihan, 0, ',', '.') }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <form method="POST" action="{{ route('tagihan.rutin.pembayaran') }}" class="mt-3 pembayaran-form">
                                                @csrf
                                                <input type="hidden" name="master_penghuni_id" value="{{ $penghuni?->id }}">
                                                <input type="hidden" name="periode" value="{{ $periode }}">
                                                <input type="hidden" name="nomor_rumah" value="{{ $nomorRumah }}">
                                                <input type="hidden" name="status_bayar" value="{{ $statusBayar }}">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered align-middle mb-0">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Jumlah Tagihan</th>
                                                                <th>Jumlah Bayar</th>
                                                                <th>Sisa/Lebih Bayar</th>
                                                                <th>Tanggal Bayar</th>
                                                                <th>Cara Bayar</th>
                                                                <th>Aksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <input type="number" min="0" name="jumlah_tagihan" class="form-control pembayaran-jumlah-tagihan" value="{{ $pembayaran->jumlah_tagihan ?? $totalTagihan }}" readonly>
                                                                </td>
                                                                <td>
                                                                    <input type="number" min="0" name="jumlah_bayar" class="form-control pembayaran-jumlah-bayar" value="{{ $pembayaran->jumlah_bayar ?? 0 }}" required>
                                                                </td>
                                                                <td>
                                                                    <input type="number" name="sisa_lebih_bayar" class="form-control pembayaran-sisa-lebih" value="{{ $pembayaran->sisa_lebih_bayar ?? (0 - $totalTagihan) }}" readonly>
                                                                </td>
                                                                <td>
                                                                    <input type="date" name="tanggal_bayar" class="form-control" value="{{ $tanggalBayar }}">
                                                                </td>
                                                                <td>
                                                                    <select name="cara_bayar" class="form-select">
                                                                        <option value="">-- Pilih --</option>
                                                                        @foreach(['Cash', 'LinkAja', 'Jago', 'BSI', 'Mandiri'] as $cb)
                                                                            <option value="{{ $cb }}" {{ ($pembayaran->cara_bayar ?? '') === $cb ? 'selected' : '' }}>{{ $cb }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <button type="submit" class="btn btn-primary">
                                                                        <i class="fas fa-save"></i> {{ $pembayaran->exists ? 'Update' : 'Simpan' }}
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-success btn-kirim-wa"
                                                    data-modal-id="tagihanRutinModal{{ $index }}"
                                                    data-phone="{{ $penghuni?->kontak_person ?? '' }}">
                                                <i class="fab fa-whatsapp"></i> Kirim ke WhatsApp
                                            </button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle"></i> Belum ada data tagihan rutin untuk filter ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .invoice-preview {
        border: 3px double #111;
        color: #000;
        font-size: 16px;
        line-height: 1.25;
        margin: 0 auto;
        max-width: 980px;
        background: #fff;
    }

    .invoice-header {
        align-items: center;
        display: grid;
        grid-template-columns: 220px 1fr;
        min-height: 135px;
        padding: 16px 22px 10px;
    }

    .invoice-logo {
        max-height: 92px;
        max-width: 180px;
    }

    .invoice-title {
        text-align: center;
        font-weight: 700;
    }

    .invoice-title h4 {
        font-size: 22px;
        font-weight: 800;
        margin: 0 0 38px;
    }

    .invoice-title h4:last-child {
        margin-bottom: 0;
    }

    .invoice-band,
    .invoice-total-air,
    .invoice-grand-total {
        background: #a8d08d;
    }

    .invoice-band {
        border-top: 2px solid #111;
        height: 14px;
    }

    .invoice-info {
        display: grid;
        grid-template-columns: 330px 1fr;
        font-weight: 700;
        padding: 4px 26px 2px 120px;
    }

    .invoice-value {
        padding-left: 8px;
    }

    .invoice-year {
        display: inline-block;
        margin-left: 120px;
    }

    .invoice-table {
        width: 100%;
        border-collapse: collapse;
    }

    .invoice-table td {
        padding: 2px 6px;
        vertical-align: top;
    }

    .invoice-table td:first-child {
        width: 72%;
    }

    .invoice-table td:nth-child(2) {
        text-align: center;
        width: 24px;
    }

    .invoice-table td:last-child {
        text-align: right;
        width: 220px;
    }

    .invoice-total-air td,
    .invoice-grand-total td {
        font-weight: 800;
    }

    @media (max-width: 768px) {
        .invoice-preview {
            font-size: 16px;
        }

        .invoice-header {
            grid-template-columns: 130px 1fr;
            padding: 12px;
        }

        .invoice-logo {
            max-width: 110px;
        }

        .invoice-title h4 {
            font-size: 17px;
            margin-bottom: 20px;
        }

        .invoice-info {
            grid-template-columns: 120px 1fr;
            padding-left: 16px;
        }

        .invoice-year {
            margin-left: 24px;
        }
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.pembayaran-form').forEach(function (form) {
            var jumlahTagihan = form.querySelector('.pembayaran-jumlah-tagihan');
            var jumlahBayar = form.querySelector('.pembayaran-jumlah-bayar');
            var sisaLebih = form.querySelector('.pembayaran-sisa-lebih');

            function updateSisaLebih() {
                var tagihan = parseInt(jumlahTagihan.value || '0', 10);
                var bayar = parseInt(jumlahBayar.value || '0', 10);
                sisaLebih.value = bayar - tagihan;
            }

            jumlahBayar.addEventListener('input', updateSisaLebih);
            updateSisaLebih();
        });
    });
</script>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
    $(document).ready(function () {
        $('#select2-nomor-rumah').select2({
            placeholder: '-- Semua Rumah --',
            allowClear: true,
            width: '100%',
        });
    });

    document.querySelectorAll('.btn-kirim-wa').forEach(function (btn) {
        btn.addEventListener('click', async function () {
            const modalId = this.dataset.modalId;
            const rawPhone = (this.dataset.phone || '').replace(/\D/g, '');
            const invoiceEl = document.querySelector('#' + modalId + ' .invoice-preview');

            if (!invoiceEl) {
                alert('Invoice tidak ditemukan.');
                return;
            }

            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Memproses...';

            try {
                const canvas = await html2canvas(invoiceEl, {
                    scale: 2,
                    useCORS: true,
                    backgroundColor: '#ffffff',
                });

                canvas.toBlob(async function (blob) {
                    const fileName = 'tagihan-' + modalId + '.png';
                    const file = new File([blob], fileName, { type: 'image/png' });

                    const canShareFile = navigator.share && navigator.canShare && navigator.canShare({ files: [file] });

                    if (canShareFile) {
                        try {
                            await navigator.share({ files: [file], title: 'Tagihan Rutin' });
                        } catch (shareErr) {
                            if (shareErr.name !== 'AbortError') {
                                fallbackDownloadAndWa(blob, fileName, rawPhone);
                            }
                        }
                    } else {
                        fallbackDownloadAndWa(blob, fileName, rawPhone);
                    }

                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }, 'image/png');

            } catch (err) {
                alert('Gagal mengonversi tagihan: ' + err.message);
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        });
    });

    function fallbackDownloadAndWa(blob, fileName, rawPhone) {
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = fileName;
        a.click();
        setTimeout(function () { URL.revokeObjectURL(url); }, 1000);

        if (rawPhone) {
            let waPhone = rawPhone;
            if (waPhone.startsWith('0')) {
                waPhone = '62' + waPhone.slice(1);
            } else if (waPhone.startsWith('8')) {
                waPhone = '62' + waPhone;
            }
            window.open('https://wa.me/' + waPhone, '_blank');
        }
    }
</script>
@endpush
