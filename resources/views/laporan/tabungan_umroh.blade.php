@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="dashboard-header">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start gap-3">
            <div>
                <h3 class="mb-1"><i class="fas fa-piggy-bank me-2"></i> Dashboard Tabungan Umroh</h3>
                <p class="mb-0 text-muted">Ringkasan perolehan tabungan anggota berdasarkan program dan metode setor.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <span class="dashboard-badge"><i class="fas fa-filter me-2"></i> {{ $programLabel }}</span>
                <span class="dashboard-badge success"><i class="fas fa-circle-check me-2"></i> {{ now()->translatedFormat('F Y') }}</span>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-3 col-md-6">
            <div class="stat-card card-primary">
                <div class="stat-icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="stat-label">Total Setoran</div>
                <div class="stat-value">{{ 'Rp ' . number_format($totalSetoran ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card card-success">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-label">Jumlah Anggota</div>
                <div class="stat-value">{{ number_format($jumlahAnggota ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card card-info">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-label">Rata-rata / Anggota</div>
                <div class="stat-value">{{ 'Rp ' . number_format($rataRataPerAnggota ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card card-warning">
                <div class="stat-icon">
                    <i class="fas fa-medal"></i>
                </div>
                <div class="stat-label">Top Kontributor</div>
                <div class="stat-value">{{ $topAnggota->first()['nama'] ?? '-' }}</div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="chart-card">
                <div class="chart-card-header">
                    <i class="fas fa-chart-bar me-2"></i> Distribusi Tabungan Anggota
                </div>
                <form method="GET" action="{{ url('laporan/tabungan-umroh') }}" class="row g-2 align-items-end mb-3">
                    <div class="col-auto">
                        <label class="form-label mb-1">Program</label>
                        <select name="program_id" class="form-select form-select-sm" style="min-width:220px;">
                            <option value="">— Semua Program —</option>
                            @foreach ($programs as $prog)
                                <option value="{{ $prog->id }}" {{ $programId == $prog->id ? 'selected' : '' }}>{{ $prog->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-filter"></i> Terapkan
                        </button>
                        <a href="{{ url('laporan/tabungan-umroh') }}" class="btn btn-outline-secondary btn-sm ms-1">
                            <i class="fas fa-times"></i> Reset
                        </a>
                    </div>
                </form>
                @if ($chartData->isEmpty())
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle"></i> Belum ada data tabungan umroh untuk filter ini.
                    </div>
                @else
                    <p class="text-muted mb-3"><small><i class="fas fa-hand-pointer"></i> Klik batang grafik untuk melihat detail setoran anggota.</small></p>
                    <canvas id="tabunganChart"></canvas>
                @endif
            </div>
        </div>

        <div class="col-lg-4">
            <div class="table-card">
                <div class="table-card-header">
                    <i class="fas fa-ranking-star me-2"></i> Top 5 Kontributor
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th class="text-end">Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topAnggota as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item['nama'] }}</td>
                                    <td class="text-end fw-semibold">{{ 'Rp ' . number_format($item['total_nominal'] ?? 0, 0, ',', '.') }}</td>
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

            <div class="chart-card mt-3">
                <div class="chart-card-header">
                    <i class="fas fa-money-bill-wave me-2"></i> Metode Setor
                </div>
                @if($metodeSetor->isEmpty())
                    <div class="text-muted small">Belum ada data metode setor.</div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($metodeSetor as $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>{{ $item->cara_setor ?? '-' }}</span>
                                <span class="fw-semibold">{{ 'Rp ' . number_format($item->total_nominal ?? 0, 0, ',', '.') }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Tabungan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalLoading" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
                <div id="modalContent" style="display:none;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0" id="modalNama"></h6>
                        <span class="badge bg-primary fs-6" id="modalTotal"></span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Nominal</th>
                                    <th>Cara Setor</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="modalTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
    const chartData = @json($chartData);
    const trendData = @json($trendData);

    const ctx = document.getElementById('tabunganChart');
    if (ctx) {
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.map(d => d.nama),
                datasets: [{
                    label: 'Total Tabungan',
                    data: chartData.map(d => d.total_nominal),
                    backgroundColor: chartData.map((_, i) => `hsl(${(i * 47) % 360}, 65%, 55%)`),
                    borderRadius: 4,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                onClick(event, elements) {
                    if (!elements.length) return;
                    const idx = elements[0].index;
                    const anggotaId = chartData[idx].anggota_id;
                    openDetail(anggotaId);
                },
                onHover(event, elements) {
                    event.native.target.style.cursor = elements.length ? 'pointer' : 'default';
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label(ctx) {
                                return ' Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw);
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
                        ticks: { maxRotation: 30 }
                    }
                }
            }
        });
    }

    const trendCtx = document.getElementById('trendChart');
    if (trendCtx && trendData.length) {
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: trendData.map(d => d.label),
                datasets: [{
                    label: 'Setoran Bulanan',
                    data: trendData.map(d => d.total_nominal),
                    borderColor: '#4568DC',
                    backgroundColor: 'rgba(69, 104, 220, 0.15)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw)
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => 'Rp ' + new Intl.NumberFormat('id-ID').format(value)
                        }
                    }
                }
            }
        });
    }

    function openDetail(anggotaId) {
        const modal = new bootstrap.Modal(document.getElementById('detailModal'));
        document.getElementById('modalLoading').style.display = 'block';
        document.getElementById('modalContent').style.display = 'none';
        modal.show();

        fetch(`{{ url('laporan/tabungan-umroh/detail') }}/${anggotaId}`)
            .then(r => r.json())
            .then(data => {
                document.getElementById('modalNama').textContent = data.nama;
                document.getElementById('modalTotal').textContent =
                    'Total: Rp ' + new Intl.NumberFormat('id-ID').format(data.total);

                const tbody = document.getElementById('modalTableBody');
                tbody.innerHTML = data.rows.map((row, i) => `
                    <tr>
                        <td>${i + 1}</td>
                        <td>${row.tanggal}</td>
                        <td>Rp ${new Intl.NumberFormat('id-ID').format(row.nominal)}</td>
                        <td>${row.cara_setor}</td>
                        <td>${row.keterangan}</td>
                    </tr>
                `).join('');

                document.getElementById('modalLoading').style.display = 'none';
                document.getElementById('modalContent').style.display = 'block';
            });
    }
</script>
@endsection
