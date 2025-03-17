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
                    <th>Mata Pelajaran</th>
                    <th>Kelas</th>
                    <th>File Soal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bankSoals as $index => $soal)
                    @php
                        // Decode JSON mata_pelajaran_id
                        $mapelKelasData = json_decode($soal->mata_pelajaran_id, true);

                        // Pastikan data berbentuk array valid
                        if (!is_array($mapelKelasData)) {
                            $mapelKelasData = [];
                        }

                        // Ambil ID mata pelajaran dan kelas dari JSON
                        $mapelId = $mapelKelasData['mata_pelajaran_id'] ?? null; // Mata pelajaran hanya satu ID
                        $kelasIds = $mapelKelasData['kelas_id'] ?? []; // Kelas bisa lebih dari satu (array)

                        // Ambil nama mata pelajaran berdasarkan ID
                        $mapelNama = \App\Models\MataPelajaran::where('id', $mapelId)->value('nama_mapel');

                        // Ambil nama kelas berdasarkan kelas_id yang dipilih
                        $kelasList = \App\Models\Kelas::whereIn('id', $kelasIds)->pluck('nama_kelas')->toArray();
                    @endphp
                    <tr>
                        <td>{{ ($bankSoals->currentPage() - 1) * $bankSoals->perPage() + $index + 1 }}</td>
                        <td>{{ $mapelNama ?? 'Unknown Mapel' }}</td> <!-- Menampilkan nama mata pelajaran -->
                        <td>{{ !empty($kelasList) ? implode(', ', $kelasList) : 'Tidak ada kelas' }}</td>
                        <!-- Menampilkan daftar kelas -->
                        {{-- <td>
                            <a href="{{ asset('storage/' . $soal->file_soal) }}" target="_blank">
                                {{ basename($soal->file_soal) }}
                            </a>
                        </td> --}}
                        <td>
                            <a href="javascript:void(0);" class="open-zip-modal"
                                data-file="{{ asset('storage/' . $soal->file_soal) }}" data-id="{{ $soal->id }}">
                                {{ basename($soal->file_soal) }}
                            </a>
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
                                    {{-- <form action="{{ route('guru.bank-soal.destroy', $soal->id) }}" method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus soal ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger delete-bank-soal">
                                            <i class="icon-base ti tabler-trash me-1"></i> Hapus
                                        </button>
                                    </form> --}}
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada bank soal.</td>
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
                        <!-- Pilihan Mata Pelajaran -->
                        <div class="mb-3">
                            <label class="form-label">Pilih Mata Pelajaran</label>
                            <select class="form-control" name="mata_pelajaran_id" required>
                                <option value="">-- Pilih Mata Pelajaran --</option>
                                @foreach ($mapingMapels as $maping)
                                    @foreach ($maping->mapel_kelas_list as $data)
                                        <option value="{{ $data['mata_pelajaran_id'] }}">{{ $data['mapel'] }}</option>
                                    @endforeach
                                @endforeach
                            </select>

                        </div>

                        <!-- Pilihan Kelas (Multiple Select) -->
                        <div class="mb-3">
                            <label class="form-label">Pilih Kelas</label>
                            <select class="form-control select2" name="kelas_id[]" multiple="multiple" required>
                                @foreach ($kelas as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_kelas }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Bisa memilih lebih dari satu kelas</small>
                        </div>

                        <!-- Upload File Soal -->
                        <div class="mb-3">
                            <label class="form-label">Upload File Soal</label>
                            <input type="file" class="form-control" name="file_soal" accept=".zip" required>
                            <small class="text-muted">Format file: .zip (Max: 5MB)</small>
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

    <!-- Modal untuk menampilkan isi ZIP dalam struktur folder -->
    <div class="modal fade" id="zipContentModal" tabindex="-1" aria-labelledby="zipContentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="zipContentModalLabel">Isi File ZIP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul id="zipFileTree" class="list-group"></ul>
                    <!-- Tempat menampilkan daftar file sebagai struktur folder -->
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".open-zip-modal").forEach(function(element) {
                element.addEventListener("click", function() {
                    let soalId = this.getAttribute("data-id");

                    // Kirim permintaan AJAX ke server untuk mendapatkan isi ZIP dalam struktur folder
                    fetch(`/guru/bank-soal/lihat-zip/${soalId}`)
                        .then(response => response.json())
                        .then(data => {
                            let fileTreeContainer = document.getElementById("zipFileTree");
                            fileTreeContainer.innerHTML = ""; // Bersihkan isi sebelumnya

                            if (data.success) {
                                fileTreeContainer.appendChild(buildFileTree(data.fileTree));
                            } else {
                                fileTreeContainer.innerHTML =
                                    "<li class='list-group-item'>Gagal membaca file ZIP.</li>";
                            }

                            // Tampilkan modal
                            new bootstrap.Modal(document.getElementById("zipContentModal"))
                                .show();
                        })
                        .catch(error => {
                            console.error("Error:", error);
                        });
                });
            });

            // Fungsi untuk membuat struktur folder
            function buildFileTree(tree, isSub = false) {
                let ul = document.createElement("ul");
                ul.classList.add("list-group", "ms-3");

                for (let key in tree) {
                    let li = document.createElement("li");
                    li.classList.add("list-group-item");

                    if (Object.keys(tree[key]).length > 0) {
                        li.innerHTML = `<i class="icon-base ti tabler-folder me-1"></i> <strong>${key}</strong>`;
                        let subUl = buildFileTree(tree[key], true);
                        li.appendChild(subUl);
                    } else {
                        li.innerHTML = `<i class="icon-base ti tabler-file me-1"></i> ${key}`;
                    }

                    ul.appendChild(li);
                }

                return ul;
            }
        });
    </script>

@endsection
