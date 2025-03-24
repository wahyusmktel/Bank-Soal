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
                            <img src="../../assets/img/illustrations/card-advance-sale.png" height="140"
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
                                        <h5 class="mb-0">{{ $jumlahSudahUpload }} / {{ $jumlahBelumUpload }} Upload</h5>
                                        <small>Sudah / Belum</small>
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

        <!-- Source Visit -->
        <div class="col-xxl-4 col-md-6 col-12">
            <div class="card h-100">
              <div class="card-header d-flex justify-content-between">
                <div class="card-title mb-0">
                    <h5 class="mb-1">Guru Belum Upload</h5>
                    <p class="card-subtitle">{{ count($guruBelumUpload) }} Data</p>
                </div>
                <div class="dropdown">
                  <button
                    class="btn btn-text-secondary rounded-pill text-body-secondary border-0 p-2 me-n1"
                    type="button"
                    id="sourceVisits"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">
                    <i class="icon-base ti tabler-dots-vertical icon-md text-body-secondary"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-end" aria-labelledby="sourceVisits">
                    <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                    <a class="dropdown-item" href="javascript:void(0);">Download</a>
                    <a class="dropdown-item" href="javascript:void(0);">View All</a>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <ul class="list-unstyled mb-0">
                    @forelse ($guruBelumUpload as $data)
                        <li class="mb-4">
                            <div class="d-flex align-items-center">
                                <div class="badge bg-label-secondary text-body p-2 me-4 rounded">
                                    <i class="icon-base ti tabler-alert-triangle icon-md"></i>
                                </div>
                                <div class="d-flex justify-content-between w-100 flex-wrap gap-2">
                                    <div class="me-2">
                                        <h6 class="mb-0">{{ $data['nama_guru'] }}</h6>
                                        <small class="text-body">{{ $data['mapel'] }} - {{ $data['kelas'] }}</small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="ms-4 badge bg-label-danger">Belum Upload</div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li>
                            <p class="text-center text-muted">Semua guru telah mengunggah soal 🎉</p>
                        </li>
                    @endforelse
                </ul>
              </div>
            </div>
          </div>
          <!--/ Source Visit -->
    </div>

@endsection
