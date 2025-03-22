@extends('layouts.guru')

@section('title', 'Dashboard Guru')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-solid-primary d-flex align-items-center" role="alert">
                <span class="alert-icon rounded">
                  <i class="icon-base ti tabler-book icon-md"></i>
                </span>
                Dibawah ini adalah daftar mata pelajaran yang diajar oleh guru bersangkutan.
            </div>
        </div>
    </div>

    <div class="card mt-3">

        <h5 class="card-header">Daftar Mata Pelajaran</h5>

        <!-- Tabel Mata Pelajaran -->
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Mata Pelajaran</th>
                        <th>Kelas</th>
                        <th>Data Ujian</th>
                        <th>Tahun Pelajaran</th>
                        <th>Semester</th>
                        <th>Upload Soal?</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @forelse ($mapingMapels as $maping)
                        @foreach ($maping->mapel_kelas_list as $data)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $data['mapel'] }}</td>
                                <td>{{ $data['kelas'] }}</td>
                                <td>{{ $maping->dataUjian->nama_ujian }}</td>
                                <td>{{ $maping->dataUjian->tahunPelajaran->nama_tahun ?? '-' }}</td>
                                <td>{{ $maping->dataUjian->tahunPelajaran->semester ?? '-' }}</td>
                                <td>
                                    @if ($data['sudah_upload'])
    <span class="badge bg-success">Sudah</span>
@else
    <span class="badge bg-danger">Belum</span>
@endif
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada mata pelajaran aktif.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $mapingMapels->links('vendor.pagination.bootstrap-4') }}
            </div>
        </div>
    </div>

@endsection
