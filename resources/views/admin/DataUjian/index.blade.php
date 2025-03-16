@extends('layouts.app')

@section('title', 'Daftar Data Ujian')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Data Ujian</h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDataUjianModal">
                Tambah Data
            </button>
        </div>
        <div class="card-body">
            <!-- Form Pencarian -->
            <form method="GET" action="{{ route('admin.data-ujian.index') }}" class="mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Cari Nama Ujian">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">Cari</button>
                        <a href="{{ route('admin.data-ujian.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>

            <!-- Tabel Data Ujian -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Ujian</th>
                            <th>Tahun Pelajaran</th>
                            <th>Semester</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Akhir</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($dataUjians as $index => $dataUjian)
                            <tr>
                                <td>{{ ($dataUjians->currentPage() - 1) * $dataUjians->perPage() + $index + 1 }}</td>
                                <td>{{ $dataUjian->nama_ujian }}</td>
                                <td>{{ $dataUjian->tahunPelajaran->nama_tahun }}</td>
                                <td>{{ $dataUjian->tahunPelajaran->semester == 1 ? 'Ganjil' : 'Genap' }}</td>
                                <td>{{ \Carbon\Carbon::parse($dataUjian->tgl_mulai)->translatedFormat('d F Y H:i') }}</td>
                                <td>{{ \Carbon\Carbon::parse($dataUjian->tgl_akhir)->translatedFormat('d F Y H:i') }}</td>
                                <td>
                                    <label class="switch switch-square">
                                        <input type="checkbox" class="switch-input update-status-ujian"
                                            data-id="{{ $dataUjian->id }}" {{ $dataUjian->status ? 'checked' : '' }}>
                                        <span class="switch-toggle-slider">
                                            <span class="switch-on"></span>
                                            <span class="switch-off"></span>
                                        </span>
                                        <span class="switch-label">{{ $dataUjian->status ? 'Active' : 'Inactive' }}</span>
                                    </label>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data ujian.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-3">
                {{ $dataUjians->links('vendor.pagination.bootstrap-4') }}
            </div>
        </div>
    </div>

    <!-- Modal Tambah Data Ujian -->
    <div class="modal fade" id="addDataUjianModal" data-bs-backdrop="static" tabindex="-1"
        aria-labelledby="addDataUjianModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDataUjianModalLabel">Tambah Data Ujian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.data-ujian.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tahun Pelajaran</label>
                            <select class="form-control" name="tahun_pelajaran_id" required>
                                <option value="">-- Pilih Tahun Pelajaran --</option>
                                @foreach ($tahunPelajarans as $tahun)
                                    <option value="{{ $tahun->id }}">{{ $tahun->nama_tahun }}
                                        ({{ $tahun->semester == 1 ? 'Ganjil' : 'Genap' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Ujian</label>
                            <input type="text" class="form-control" name="nama_ujian" required
                                placeholder="Contoh: Ujian Akhir Semester">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="datetime-local" class="form-control" name="tgl_mulai" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal Akhir</label>
                            <input type="datetime-local" class="form-control" name="tgl_akhir" required>
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
            document.querySelectorAll(".update-status-ujian").forEach(function(switchInput) {
                switchInput.addEventListener("change", function() {
                    let ujianId = this.dataset.id;
                    let isChecked = this.checked;

                    fetch("{{ route('admin.data-ujian.updateStatus') }}", {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": document.querySelector(
                                    'meta[name="csrf-token"]').getAttribute("content"),
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({
                                id: ujianId,
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
