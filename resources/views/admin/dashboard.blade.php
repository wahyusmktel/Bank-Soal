@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="row g-6">
    <!-- View sales -->
    <div class="col-xl-4">
      <div class="card">
        <div class="d-flex align-items-end row">
          <div class="col-7">
            <div class="card-body text-nowrap">
                <h5 class="card-title mb-0">
                    {{ $tahunAktif ? $tahunAktif->nama_tahun : 'Tahun Tidak Tersedia' }}
                    - Semester {{ $tahunAktif ? $tahunAktif->semester : '-' }}
                </h5>

                <p class="mb-2">
                    {{ $ujianAktif ? $ujianAktif->nama_ujian : 'Tidak Ada Ujian Aktif' }}
                </p>

                <h4 class="text-primary mb-1">
                    {{ $jumlahSoal }} Bank Soal
                </h4>

                <a href="{{ route('admin.bank-soal.index') }}" class="btn btn-primary">
                    Lihat Bank Soal
                </a>
            </div>
          </div>
          <div class="col-5 text-center text-sm-left">
            <div class="card-body pb-0 px-0 px-md-4">
              <img
                src="../../assets/img/illustrations/card-advance-sale.png"
                height="140"
                alt="view sales" />
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- View sales -->

    <!-- Statistics -->
    <div class="col-xl-8 col-md-12">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between">
                <h5 class="card-title mb-0">Statistik Aplikasi</h5>
                <small class="text-body-secondary">Terakhir diperbarui {{ now()->diffForHumans() }}</small>
            </div>
            <div class="card-body d-flex align-items-end">
                <div class="w-100">
                    <div class="row gy-3">
                        <!-- Jumlah Guru -->
                        <div class="col-md-3 col-6">
                            <div class="d-flex align-items-center">
                                <div class="badge rounded bg-label-info me-4 p-2">
                                    <i class="icon-base ti tabler-users icon-lg"></i>
                                </div>
                                <div class="card-info">
                                    <h5 class="mb-0">{{ $jumlahGuru }}</h5>
                                    <small>Jumlah Guru</small>
                                </div>
                            </div>
                        </div>
                        <!-- Jumlah Bank Soal -->
                        <div class="col-md-3 col-6">
                            <div class="d-flex align-items-center">
                                <div class="badge rounded bg-label-success me-4 p-2">
                                    <i class="icon-base ti tabler-file-text icon-lg"></i>
                                </div>
                                <div class="card-info">
                                    <h5 class="mb-0">{{ $jumlahSoal }}</h5>
                                    <small>Total Bank Soal</small>
                                </div>
                            </div>
                        </div>
                        <!-- Belum Divalidasi -->
                        <div class="col-md-3 col-6">
                            <div class="d-flex align-items-center">
                                <div class="badge rounded bg-label-danger me-4 p-2">
                                    <i class="icon-base ti tabler-alert-circle icon-lg"></i>
                                </div>
                                <div class="card-info">
                                    <h5 class="mb-0">{{ $jumlahBelumValidasi }}</h5>
                                    <small>Belum Divalidasi</small>
                                </div>
                            </div>
                        </div>
                        <!-- Jumlah Mata Pelajaran -->
                        <div class="col-md-3 col-6">
                            <div class="d-flex align-items-center">
                                <div class="badge rounded bg-label-primary me-4 p-2">
                                    <i class="icon-base ti tabler-book icon-lg"></i>
                                </div>
                                <div class="card-info">
                                    <h5 class="mb-0">{{ $jumlahMapel }}</h5>
                                    <small>Mata Pelajaran</small>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end .row -->
                </div>
            </div>
        </div>
    </div>
    <!--/ Statistics -->
  </div>





    {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}

    {{-- <div class="card mb-4">
        <div class="card-header">
            Grafik Upload Soal per Guru
        </div>
        <div class="card-body">
            <canvas id="uploadSoalChart" height="100"></canvas>
        </div>
    </div> --}}

    {{-- <script>
        const ctx = document.getElementById('uploadSoalChart').getContext('2d');
        const uploadSoalChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($grafikData->pluck('nama')) !!},
                datasets: [{
                    label: 'Jumlah Soal',
                    data: {!! json_encode($grafikData->pluck('jumlah_soal')) !!},
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    title: {
                        display: true,
                        text: 'Jumlah Upload Soal per Guru'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        precision: 0
                    }
                }
            }
        });
    </script> --}}

@endsection
