@extends('layouts.guru')

@section('title', 'Bank Soal')

@section('content')
    <h4 class="py-4 mb-6">Bank Soal</h4>

    <!-- Notifikasi -->
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Tombol Tambah Bank Soal -->
    <div class="d-flex justify-content-between mb-3">
        <h5>Daftar Bank Soal</h5>
        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBankSoalModal">Tambah Bank Soal</a>
    </div>

    <!-- Tabel Bank Soal -->
    <div class="table-responsive">
        <table class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>File Soal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bankSoals as $index => $soal)
                    <tr>
                        <td>{{ ($bankSoals->currentPage() - 1) * $bankSoals->perPage() + $index + 1 }}</td>
                        <td><a href="{{ asset('storage/' . $soal->file_soal) }}" target="_blank">{{ $soal->file_soal }}</a>
                        </td>
                        <td>
                            <span class="badge {{ $soal->status ? 'bg-success' : 'bg-danger' }}">
                                {{ $soal->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="icon-base ti tabler-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ asset('storage/' . $soal->file_soal) }}"
                                        target="_blank">
                                        <i class="icon-base ti tabler-eye me-1"></i> Lihat Soal
                                    </a>
                                    <a class="dropdown-item edit-bank-soal" href="javascript:void(0);"
                                        data-id="{{ $soal->id }}" data-file="{{ $soal->file_soal }}"
                                        data-status="{{ $soal->status }}">
                                        <i class="icon-base ti tabler-edit me-1"></i> Edit
                                    </a>
                                    <form action="{{ route('guru.bank-soal.destroy', $soal->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger delete-bank-soal">
                                            <i class="icon-base ti tabler-trash me-1"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Tidak ada bank soal.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-3">
        {{ $bankSoals->links('vendor.pagination.bootstrap-4') }}
    </div>

    <!-- Modal Tambah Bank Soal -->
    <div class="modal fade" id="addBankSoalModal" data-bs-backdrop="static" tabindex="-1"
        aria-labelledby="addBankSoalModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBankSoalModalLabel">Tambah Bank Soal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('guru.bank-soal.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Upload File Soal</label>
                            <input type="file" class="form-control" name="file_soal" required>
                            <small class="text-muted">Format file: PDF, DOC, DOCX (Max: 2MB)</small>
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
