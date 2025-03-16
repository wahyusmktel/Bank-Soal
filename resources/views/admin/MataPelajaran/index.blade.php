@extends('layouts.app')

@section('title', 'Daftar Mata Pelajaran')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Mata Pelajaran</h5>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
                Import Data Mata Pelajaran
            </button>
        </div>
        <div class="card-body">
            <!-- Form Pencarian -->
            <form method="GET" action="{{ route('admin.mata-pelajaran.index') }}" class="mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Cari Mata Pelajaran">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">Cari</button>
                        <a href="{{ route('admin.mata-pelajaran.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>

            <!-- Tabel Mata Pelajaran -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Mata Pelajaran</th>
                            <th>Produktif</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mataPelajarans as $index => $mataPelajaran)
                            <tr>
                                <td>{{ ($mataPelajarans->currentPage() - 1) * $mataPelajarans->perPage() + $index + 1 }}
                                </td>
                                <td>{{ $mataPelajaran->nama_mapel ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $mataPelajaran->is_produktif ? 'bg-success' : 'bg-danger' }}">
                                        {{ $mataPelajaran->is_produktif ? 'Ya' : 'Tidak' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $mataPelajaran->status ? 'bg-success' : 'bg-danger' }}">
                                        {{ $mataPelajaran->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada data mata pelajaran.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-3">
                {{ $mataPelajarans->links('vendor.pagination.bootstrap-4') }}
            </div>
        </div>
    </div>

    <!-- Modal Import Mata Pelajaran -->
    <div class="modal fade" id="importModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="importModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Data Mata Pelajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.mata-pelajaran.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <label for="file" class="form-label">Pilih File Excel</label>
                        <input type="file" class="form-control" name="file" required accept=".xlsx,.csv">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
