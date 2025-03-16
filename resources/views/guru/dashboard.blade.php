@extends('layouts.guru')

@section('title', 'Dashboard Guru')

@section('content')
    <h4 class="py-4 mb-6">Dashboard Guru</h4>
    <p>Selamat datang di halaman dashboard Guru.</p>

    <!-- Tabel Mata Pelajaran -->
    <div class="table-responsive">
        <table class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Mata Pelajaran</th>
                    <th>Kelas</th>
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
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada mata pelajaran aktif.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-3">
        {{ $mapingMapels->links('vendor.pagination.bootstrap-4') }}
    </div>
@endsection
