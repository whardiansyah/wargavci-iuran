@extends('layouts.admin')

@section('content')
    <!-- Stat Cards -->
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="stat-card card-primary">
                <div class="stat-icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="stat-label">Saldo Per {{ now()->format('d') }} {{ ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'][now()->month] }} {{ now()->format('Y') }}</div>
                <div class="stat-value">{{ 'Rp ' . number_format($saldoKas, 0, ',', '.') }}</div>
                <!-- <small class="text-muted">Total Kredit - Total Debet</small> -->
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stat-card card-success">
                <div class="stat-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-label">Kas Masuk Bulan Berjalan</div>
                <div class="stat-value">{{ 'Rp ' . number_format($kasBulanBerjalan->kas_masuk ?? 0, 0, ',', '.') }}</div>
                <!-- <small class="text-muted">Total Kredit Bulan Ini</small> -->
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stat-card card-danger">
                <div class="stat-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="stat-label">Kas Keluar Bulan Berjalan</div>
                <div class="stat-value">{{ 'Rp ' . number_format($kasBulanBerjalan->kas_keluar ?? 0, 0, ',', '.') }}</div>
                <!-- <small class="text-muted">Total Debet Bulan Ini</small> -->
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stat-card card-info">
                <div class="stat-icon">
                    <i class="fas fa-tint"></i>
                </div>
                <div class="stat-label">Pendapatan Iuran Air</div>
                <div class="stat-value">{{ 'Rp ' . number_format($pendapatanIuranAir ?? 0, 0, ',', '.') }}</div>
                <!-- <small class="text-muted">Tagihan Iuran Air Lunas Bulan Ini</small> -->
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <div class="col-lg-6">
            <div class="chart-card">
                <div class="chart-card-header" style="color: white; background: #2dce89; padding: 15px; margin: -20px -20px 20px -20px; border-radius: 8px 8px 0 0;">
                    <i class="fas fa-chart-bar"></i> Metode Bayar
                </div>
                <div class="mb-3">
                    <form method="GET" action="{{ route('dashboard') }}" class="d-flex gap-2 align-items-center flex-wrap">
                        @foreach(request()->except(['daily_sales_periode']) as $key => $val)
                            <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                        @endforeach
                        <select name="daily_sales_periode" class="form-select form-select-sm" style="width: auto;">
                            @foreach($periodeList as $p)
                                <option value="{{ $p }}" {{ $dailySalesPeriode == $p ? 'selected' : '' }}>{{ $p }}</option>
                            @endforeach
                            @if($periodeList->isEmpty())
                                <option value="{{ $dailySalesPeriode }}">{{ $dailySalesPeriode }}</option>
                            @endif
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                    </form>
                </div>
                <canvas id="dailySalesChart" data-periode="{{ $dailySalesPeriode }}"></canvas>
                <div class="chart-card-footer">
                    <i class="fas fa-history"></i> Total pembayaran per cara bayar
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="chart-card">
                <div class="chart-card-header" style="color: white; background: #fb6340; padding: 15px; margin: -20px -20px 20px -20px; border-radius: 8px 8px 0 0;">
                    <i class="fas fa-chart-bar"></i> Transaksi Kas
                </div>
                <div class="mb-3">
                    <form method="GET" action="{{ route('dashboard') }}" class="d-flex gap-2 align-items-center flex-wrap">
                        <select name="bulan" class="form-select form-select-sm" style="width: auto;">
                            @foreach([1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'] as $m => $label)
                                <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <select name="tahun" class="form-select form-select-sm" style="width: auto;">
                            @foreach($tahunList as $y)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                            @if($tahunList->isEmpty())
                                <option value="{{ $tahun }}">{{ $tahun }}</option>
                            @endif
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                    </form>
                </div>
                <canvas id="transaksiKasChart"></canvas>
                <div class="chart-card-footer">
                    <i class="fas fa-history"></i> Total kredit &amp; debet per kode
                </div>
            </div>
        </div>
    </div>

    <!-- Penggunaan Air Chart -->
    <div class="row">
        <div class="col-lg-6">
            <div class="chart-card">
                <div class="chart-card-header" style="color: white; background: #11cdef; padding: 15px; margin: -20px -20px 20px -20px; border-radius: 8px 8px 0 0;">
                    <i class="fas fa-tint"></i> Penggunaan Air
                </div>
                <canvas id="penggunaanAirChart"></canvas>
                <div class="chart-card-footer">
                    <i class="fas fa-history"></i> Total pemakaian air (m³) per bulan — 12 bulan terakhir
                </div>
            </div>
        </div>

        <!-- Task List -->
        <div class="col-lg-6">
            <div class="table-card">
                <div class="table-card-header">
                    <i class="fas fa-check-square"></i> Tasks
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td style="width: 40px;">
                                    <input type="checkbox" checked>
                                </td>
                                <td>
                                    <strong>Sign contract for "What are conference organizers afraid of?"</strong>
                                    <br>
                                    <small class="text-muted">From My Boss</small>
                                </td>
                                <td style="text-align: right;">
                                    <button class="btn btn-sm btn-link text-primary"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-link text-danger"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="checkbox">
                                </td>
                                <td>
                                    <strong>Lines From Great Russian Literature? Or E-mails From My Boss?</strong>
                                    <br>
                                    <small class="text-muted">From My Boss</small>
                                </td>
                                <td style="text-align: right;">
                                    <button class="btn btn-sm btn-link text-primary"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-link text-danger"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="checkbox">
                                </td>
                                <td>
                                    <strong>Flooded: One year later, assessing what was lost and found, in ravaging rain swept through metro Detroit</strong>
                                    <br>
                                    <small class="text-muted">From My Boss</small>
                                </td>
                                <td style="text-align: right;">
                                    <button class="btn btn-sm btn-link text-primary"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-link text-danger"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Employees Stats Table -->
    <div class="row">
        <div class="col-12">
            <div class="table-card">
                <div class="table-card-header">
                    <i class="fas fa-users"></i> Employees Stats
                    <span class="float-end" style="font-size: 12px; font-weight: normal;">New employees on 16th September, 2016</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Salary</th>
                                <th>Country</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Dakota Rice</td>
                                <td>$36,738</td>
                                <td>Niger</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Minerva Hooper</td>
                                <td>$23,789</td>
                                <td>Curacao</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Sage Rodriguez</td>
                                <td>$56,142</td>
                                <td>Netherlands</td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>Philip Chaney</td>
                                <td>$38,735</td>
                                <td>Korea, South</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Metode Bayar -->
    <div class="modal fade" id="modalMetodeBayar" tabindex="-1" aria-labelledby="modalMetodeBayarLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header" style="background:#2dce89; color:white;">
                    <h5 class="modal-title" id="modalMetodeBayarLabel"><i class="fas fa-chart-bar me-2"></i>Detail Metode Bayar</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="modalMetodeBayarLoading" class="text-center py-4">
                        <div class="spinner-border text-success" role="status"></div>
                        <p class="mt-2 text-muted">Memuat data...</p>
                    </div>
                    <div id="modalMetodeBayarContent" style="display:none;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <span class="fw-bold" id="modalCaraBayarLabel"></span>
                                &mdash; Periode: <span id="modalPeriodeLabel"></span>
                            </div>
                            <div class="text-end">
                                <small class="text-muted">Total: <strong id="modalTotalBayar" class="text-success"></strong></small>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover" id="tabelDetailMetodeBayar">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Nama KK</th>
                                        <th>No. Rumah</th>
                                        <th>Tgl Bayar</th>
                                        <th class="text-end">Tagihan</th>
                                        <th class="text-end">Dibayar</th>
                                    </tr>
                                </thead>
                                <tbody id="bodyDetailMetodeBayar"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const formatRp = v => 'Rp ' + new Intl.NumberFormat('id-ID').format(v);

        function bukaDailySalesDetail(caraBayar, periode) {
            document.getElementById('modalCaraBayarLabel').textContent = caraBayar;
            document.getElementById('modalPeriodeLabel').textContent = periode;
            document.getElementById('modalMetodeBayarLoading').style.display = '';
            document.getElementById('modalMetodeBayarContent').style.display = 'none';
            document.getElementById('bodyDetailMetodeBayar').innerHTML = '';

            const modal = new bootstrap.Modal(document.getElementById('modalMetodeBayar'));
            modal.show();

            fetch(`{{ route('dashboard.metode_bayar_detail') }}?periode=${encodeURIComponent(periode)}&cara_bayar=${encodeURIComponent(caraBayar)}`)
                .then(r => r.json())
                .then(data => {
                    const tbody = document.getElementById('bodyDetailMetodeBayar');
                    let totalBayar = 0;
                    data.forEach((row, i) => {
                        totalBayar += row.jumlah_bayar;
                        tbody.innerHTML += `<tr>
                            <td>${i + 1}</td>
                            <td>${row.nama}</td>
                            <td>${row.nomor_rumah}</td>
                            <td>${row.tanggal_bayar}</td>
                            <td class="text-end">${formatRp(row.jumlah_tagihan)}</td>
                            <td class="text-end text-success fw-bold">${formatRp(row.jumlah_bayar)}</td>
                        </tr>`;
                    });
                    document.getElementById('modalTotalBayar').textContent = formatRp(totalBayar);
                    document.getElementById('modalMetodeBayarLoading').style.display = 'none';
                    document.getElementById('modalMetodeBayarContent').style.display = '';
                })
                .catch(() => {
                    document.getElementById('modalMetodeBayarLoading').innerHTML = '<p class="text-danger">Gagal memuat data.</p>';
                });
        }

        // Daily Sales Chart
        const dailySalesCtx = document.getElementById('dailySalesChart').getContext('2d');
        const dailySalesData = @json($dailySalesChart);
        const dailySalesPeriode = document.getElementById('dailySalesChart').dataset.periode;
        new Chart(dailySalesCtx, {
            type: 'bar',
            data: {
                labels: dailySalesData.map(d => d.cara_bayar),
                datasets: [{
                    label: 'Total Pembayaran',
                    data: dailySalesData.map(d => d.total_bayar),
                    backgroundColor: [
                        '#2dce89', '#fb6340', '#11cdef', '#f5365c', '#5e72e4', '#ffd600'
                    ],
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' ' + formatRp(ctx.raw)
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                },
                onClick: (evt, elements) => {
                    if (elements.length > 0) {
                        const idx = elements[0].index;
                        const caraBayar = dailySalesData[idx].cara_bayar;
                        bukaDailySalesDetail(caraBayar, dailySalesPeriode);
                    }
                },
                onHover: (evt, elements) => {
                    evt.native.target.style.cursor = elements.length > 0 ? 'pointer' : 'default';
                }
            }
        });

        // Transaksi Kas Chart
        const transaksiKasCtx = document.getElementById('transaksiKasChart').getContext('2d');
        const transaksiKasData = @json($transaksiKasChart);
        new Chart(transaksiKasCtx, {
            type: 'bar',
            data: {
                labels: transaksiKasData.map(d => d.kode),
                datasets: [
                    {
                        label: 'Kredit',
                        data: transaksiKasData.map(d => d.total_kredit),
                        backgroundColor: '#2dce89'
                    },
                    {
                        label: 'Debet',
                        data: transaksiKasData.map(d => d.total_debet),
                        backgroundColor: '#fb6340'
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                }
            }
        });

        // Penggunaan Air Chart
        const penggunaanAirCtx = document.getElementById('penggunaanAirChart').getContext('2d');
        const penggunaanAirData = @json($penggunaanAirChart);
        new Chart(penggunaanAirCtx, {
            type: 'line',
            data: {
                labels: penggunaanAirData.map(d => d.label),
                datasets: [{
                    label: 'Pemakaian Air (m³)',
                    data: penggunaanAirData.map(d => d.total),
                    borderColor: '#11cdef',
                    backgroundColor: 'rgba(17, 205, 239, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#11cdef',
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    datalabels: {
                        align: 'top',
                        anchor: 'end',
                        color: '#0d8ea4',
                        font: { weight: 'bold', size: 11 },
                        formatter: value => value.toLocaleString('id-ID') + ' m³'
                    }
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' ' + ctx.raw.toLocaleString('id-ID') + ' m³'
                        }
                    },
                    datalabels: {
                        align: 'top',
                        anchor: 'end',
                        color: '#0d8ea4',
                        font: { weight: 'bold', size: 11 },
                        formatter: value => value.toLocaleString('id-ID') + ' m³'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => value.toLocaleString('id-ID') + ' m³'
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });
    </script>
@endsection
