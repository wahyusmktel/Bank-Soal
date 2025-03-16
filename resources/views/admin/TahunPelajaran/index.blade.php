@extends('layouts.app')

@section('title', 'Daftar Tahun Pelajaran')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Tahun Pelajaran</h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTahunPelajaranModal">
                Tambah Data
            </button>
        </div>
        <div class="card-body">
            <!-- Form Pencarian -->
            <form method="GET" action="{{ route('admin.tahun-pelajaran.index') }}" class="mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Cari Tahun/Semester">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">Cari</button>
                        <a href="{{ route('admin.tahun-pelajaran.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>

            <!-- Tabel Tahun Pelajaran -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Tahun</th>
                            <th>Semester</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tahunPelajarans as $index => $tahunPelajaran)
                            <tr>
                                <td>{{ ($tahunPelajarans->currentPage() - 1) * $tahunPelajarans->perPage() + $index + 1 }}
                                </td>
                                <td>{{ $tahunPelajaran->nama_tahun }}</td>
                                <td>
                                    {{ $tahunPelajaran->semester == 1 ? 'Ganjil' : 'Genap' }}
                                </td>
                                <td>
                                    <label class="switch switch-square">
                                        <input type="checkbox" class="switch-input update-status"
                                            data-id="{{ $tahunPelajaran->id }}"
                                            {{ $tahunPelajaran->status ? 'checked' : '' }}>
                                        <span class="switch-toggle-slider">
                                            <span class="switch-on"></span>
                                            <span class="switch-off"></span>
                                        </span>
                                        <span
                                            class="switch-label">{{ $tahunPelajaran->status ? 'Active' : 'Inactive' }}</span>
                                    </label>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada data tahun pelajaran.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-3">
                {{ $tahunPelajarans->links('vendor.pagination.bootstrap-4') }}
            </div>
        </div>
    </div>

    <!-- Modal Tambah Tahun Pelajaran -->
    <div class="modal fade" id="addTahunPelajaranModal" data-bs-backdrop="static" tabindex="-1"
        aria-labelledby="addTahunPelajaranModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTahunPelajaranModalLabel">Tambah Tahun Pelajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.tahun-pelajaran.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Tahun</label>
                            <input type="text" class="form-control" name="nama_tahun" required
                                placeholder="Contoh: 2023/2024">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Semester</label>
                            <select class="form-control" name="semester" required>
                                <option value="1">Ganjil</option>
                                <option value="2">Genap</option>
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

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".update-status").forEach(function(switchInput) {
                switchInput.addEventListener("change", function() {
                    let tahunId = this.dataset.id;
                    let isChecked = this.checked;

                    fetch("{{ route('admin.tahun-pelajaran.updateStatus') }}", {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": document.querySelector(
                                    'meta[name="csrf-token"]').getAttribute("content"),
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({
                                id: tahunId,
                                status: isChecked
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload(); // Reload halaman agar status berubah
                            } else {
                                alert("Gagal mengubah status.");
                                this.checked = !
                                    isChecked; // Kembalikan ke nilai sebelumnya jika gagal
                            }
                        })
                        .catch(error => {
                            console.error("Error:", error);
                            alert("Terjadi kesalahan.");
                            this.checked = !isChecked;
                        });
                });
            });
        });
    </script>

@endsection
