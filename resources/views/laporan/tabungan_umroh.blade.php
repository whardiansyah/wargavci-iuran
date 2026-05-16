@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="stat-card card-primary">
                <div class="stat-icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="stat-label">Tabungan Per {{ now()->format('d') }} {{ ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'][now()->month] }} {{ now()->format('Y') }}</div>
                <div class="stat-value">{{ 'Rp ' . number_format($kasBulanBerjalan->total_nominal ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-piggy-bank"></i> Laporan Tabungan Umroh</h5>
                </div>
                <div class="card-body">
                    @if ($chartData->isEmpty())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Belum ada data tabungan umroh.
                        </div>
                    @else
                        <p class="text-muted mb-3"><small><i class="fas fa-hand-pointer"></i> Klik batang grafik untuk melihat detail setoran anggota.</small></p>
                        <canvas id="tabunganChart"></canvas>
                    @endif
                </div>
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
