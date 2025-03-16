@extends('layouts.app')

@section('title', 'Daftar Kelas')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Kelas</h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addKelasModal">
                Tambah Kelas
            </button>
        </div>
        <div class="card-body">
            <!-- Form Pencarian -->
            <form method="GET" action="{{ route('admin.kelas.index') }}" class="mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Cari Nama Kelas atau Tingkat">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">Cari</button>
                        <a href="{{ route('admin.kelas.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>

            <!-- Tabel Kelas -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Kelas</th>
                            <th>Tingkat Kelas</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kelas as $index => $data)
                            <tr>
                                <td>{{ ($kelas->currentPage() - 1) * $kelas->perPage() + $index + 1 }}</td>
                                <td>{{ $data->nama_kelas }}</td>
                                <td>{{ $data->tingkat_kelas }}</td>
                                <td>
                                    <span class="badge {{ $data->status ? 'bg-success' : 'bg-danger' }}">
                                        {{ $data->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada data kelas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-3">
                {{ $kelas->links('vendor.pagination.bootstrap-4') }}
            </div>
        </div>
    </div>

    <!-- Modal Tambah Kelas -->
    <div class="modal fade" id="addKelasModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="addKelasModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addKelasModalLabel">Tambah Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.kelas.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Kelas</label>
                            <input type="text" class="form-control" name="nama_kelas" required
                                placeholder="Contoh: X IPA 1">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tingkat Kelas</label>
                            <select class="form-control" name="tingkat_kelas" required>
                                <option value="">-- Pilih Tingkat Kelas --</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
