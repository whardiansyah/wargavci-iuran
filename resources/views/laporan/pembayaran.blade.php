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
    .chart-wrap {
        min-height: 360px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="dashboard-header">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start gap-3">
            <div>
                <h3 class="mb-1"><i class="fas fa-chart-column me-2"></i> Dashboard Laporan Pembayaran</h3>
                <p class="mb-0 text-muted">Ringkasan pembayaran per rumah dan periode untuk memantau penerimaan secara cepat.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <span class="dashboard-badge"><i class="fas fa-calendar-alt me-2"></i> {{ $periode ?: 'Semua Periode' }}</span>
                <span class="dashboard-badge success"><i class="fas fa-circle-check me-2"></i> Data terfilter</span>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-3 col-md-6">
            <div class="stat-card card-success">
                <div class="stat-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-label">Total Pembayaran</div>
                <div class="stat-value">{{ 'Rp ' . number_format($totalBayar, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card card-info">
                <div class="stat-icon">
                    <i class="fas fa-house"></i>
                </div>
                <div class="stat-label">Jumlah Rumah</div>
                <div class="stat-value">{{ number_format($jumlahRumah, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card card-primary">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-label">Rata-rata / Rumah</div>
                <div class="stat-value">{{ 'Rp ' . number_format($rataPerRumah, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card card-warning">
                <div class="stat-icon">
                    <i class="fas fa-medal"></i>
                </div>
                <div class="stat-label">Top Rumah</div>
                <div class="stat-value">{{ $topRumah->first()['nomor_rumah'] ?? '-' }}</div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="chart-card">
                <div class="chart-card-header">
                    <i class="fas fa-chart-bar me-2"></i> Grafik Pembayaran per Rumah
                </div>
                <form method="GET" action="{{ route('laporan.pembayaran') }}" class="row g-2 mb-3">
                    <div class="col-md-4">
                        <input type="month" name="periode" class="form-control form-control-sm" value="{{ $periode ?? '' }}">
                    </div>
                    <div class="col-md-5">
                        <select name="nomor_rumah" id="select2-nomor-rumah" class="form-select form-select-sm" style="width: 100%;">
                            <option value="">-- Semua Rumah --</option>
                            @foreach ($masterPenghunis as $penghuni)
                                <option value="{{ $penghuni->nomor_rumah }}" {{ ($nomorRumah ?? '') == $penghuni->nomor_rumah ? 'selected' : '' }}>
                                    {{ $penghuni->nomor_rumah }} - {{ $penghuni->kepala_keluarga }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button class="btn btn-primary btn-sm" type="submit">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        @if (request()->hasAny(['periode', 'nomor_rumah']))
                            <a href="{{ route('laporan.pembayaran') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-times"></i> Reset
                            </a>
                        @endif
                    </div>
                </form>

                @if ($chartData->isEmpty())
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle"></i> Belum ada data pembayaran untuk filter ini.
                    </div>
                @else
                    <div class="chart-wrap">
                        <canvas id="pembayaranChart"></canvas>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-4">
            <div class="table-card">
                <div class="table-card-header">
                    <i class="fas fa-ranking-star me-2"></i> Top 5 Rumah
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Rumah</th>
                                <th class="text-end">Bayar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topRumah as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item['nomor_rumah'] }} - {{ $item['kepala_keluarga'] }}</td>
                                    <td class="text-end fw-semibold">{{ 'Rp ' . number_format($item['total_bayar'] ?? 0, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Belum ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-12">
            <div class="table-card">
                <div class="table-card-header">
                    <i class="fas fa-list me-2"></i> Detail Pembayaran
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nomor Rumah</th>
                                <th>Kepala Keluarga</th>
                                <th>Periode</th>
                                <th class="text-end">Jumlah Bayar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($chartData as $index => $row)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $row['nomor_rumah'] }}</td>
                                    <td>{{ $row['kepala_keluarga'] }}</td>
                                    <td>{{ $row['periode'] }}</td>
                                    <td class="text-end">Rp {{ number_format($row['total_bayar'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
    $(document).ready(function () {
        $('#select2-nomor-rumah').select2({
            placeholder: '-- Semua Rumah --',
            allowClear: true,
            width: '100%',
        });
    });

    const pembayaranData = @json($chartData);
    const pembayaranCtx = document.getElementById('pembayaranChart');

    if (pembayaranCtx) {
        new Chart(pembayaranCtx, {
            type: 'bar',
            data: {
                labels: pembayaranData.map(row => row.label),
                datasets: [{
                    label: 'Jumlah Bayar',
                    data: pembayaranData.map(row => row.total_bayar),
                    backgroundColor: pembayaranData.map((_, index) => `hsl(${(index * 41 + 155) % 360}, 62%, 52%)`),
                    borderRadius: 4,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label(context) {
                                return ' Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 30,
                            minRotation: 0,
                        }
                    }
                }
            }
        });
    }
</script>
@endpush
