@extends('layouts.app')

@section('title', 'Daftar Guru')

@section('content')

    <!-- Pesan Error -->
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <h5 class="card-header">Cari Data</h5>
                <div class="card-body">
                    <!-- Form Pencarian & Filter -->
                    <form method="GET" action="{{ route('admin.guru.index') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-3 mt-2">
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                                    placeholder="Cari Nama/NUPTK/NIP/HP/Email/NIK">
                            </div>
                            <div class="col-md-2 mt-2">
                                <select name="jk" class="form-control">
                                    <option value="">-- Pilih JK --</option>
                                    <option value="L" {{ request('jk') == 'L' ? 'selected' : '' }}>Laki-Laki</option>
                                    <option value="P" {{ request('jk') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-3 mt-2">
                                <select name="status_kepegawaian" class="form-control">
                                    <option value="">-- Pilih Status Kepegawaian --</option>
                                    @foreach ($statusKepegawaians as $status)
                                        <option value="{{ $status }}"
                                            {{ request('status_kepegawaian') == $status ? 'selected' : '' }}>
                                            {{ $status }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 mt-2">
                                <select name="jenis_ptk" class="form-control">
                                    <option value="">-- Pilih Jenis PTK --</option>
                                    @foreach ($jenisPtks as $jenis)
                                        <option value="{{ $jenis }}"
                                            {{ request('jenis_ptk') == $jenis ? 'selected' : '' }}>
                                            {{ $jenis }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mt-2 d-flex justify-content-end">
                                <div class="input-group">
                                    <button type="submit" class="btn btn-primary">Cari</button>
                                    <a href="{{ route('admin.guru.index') }}" class="btn btn-secondary">Reset</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-header mb-0">Data Guru</h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#aturPenggunaModal">
                Atur Pengguna
            </button>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
                Import Data Guru
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <!-- Tabel Data Guru -->
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>
                                <input class="form-check-input" type="checkbox" id="checkAll">
                            </th> <!-- Checkbox untuk memilih semua -->
                            <th class="mt-4">No</th>
                            <th>Nama</th>
                            <th>Akun</th>
                            <th>NIK</th>
                            <th>Password</th>
                            <th>Email</th>
                            <th>HP</th>
                            <th>JK</th>
                            <th>Status Kepegawaian</th>
                            <th>Jenis PTK</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($gurus as $index => $guru)
                            <tr>
                                <td><input type="checkbox" class="guru-checkbox form-check-input" name="guru_ids[]"
                                        value="{{ $guru->id }}"></td>
                                <td>{{ ($gurus->currentPage() - 1) * $gurus->perPage() + $index + 1 }}</td>
                                <td>{{ $guru->Nama }}</td>
                                <td>
                                    @if ($guru->akunGuru)
                                        <span class="badge bg-label-success me-1">Active</span>
                                    @else
                                        <span class="badge bg-label-warning me-1">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $guru->NIK }}</td>
                                <td>
                                    <span class="password-text">{{ $guru->password }}</span>
                                    <button class="btn btn-xs p-0 ms-1 copy-password border-0 bg-transparent"
                                        data-password="{{ $guru->password }}" title="Copy Password">
                                        <i class="icon-base ti tabler-copy fs-5"></i>
                                    </button>
                                </td>

                                <td>{{ $guru->Email ?? '-' }}</td>
                                <td>{{ $guru->HP ?? '-' }}</td>
                                <td>{{ $guru->JK == 'L' ? 'Laki-Laki' : 'Perempuan' }}</td>
                                <td>{{ $guru->Status_Kepegawaian ?? '-' }}</td>
                                <td>{{ $guru->Jenis_PTK ?? '-' }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="icon-base ti tabler-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('admin.guru.show', $guru->id) }}">
                                                <i class="icon-base ti tabler-eye me-1"></i> Lihat Detail
                                            </a>
                                            <a class="dropdown-item reset-password" href="javascript:void(0);"
                                                data-id="{{ $guru->id }}" data-nama="{{ $guru->Nama }}">
                                                <i class="icon-base ti tabler-refresh me-1"></i> Reset Password
                                            </a>
                                            <a class="dropdown-item" href="javascript:void(0);"><i
                                                    class="icon-base ti tabler-trash me-1"></i> Delete</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">Tidak ada data guru.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $gurus->links('vendor.pagination.bootstrap-4') }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="importModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="importModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Data Guru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.guru.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <label for="file" class="form-label">Pilih File Excel</label>
                                <input type="file" class="form-control" name="file" required accept=".xlsx,.csv">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="aturPenggunaModal" data-bs-backdrop="static" tabindex="-1"
        aria-labelledby="aturPenggunaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="aturPenggunaModalLabel">Konfirmasi Atur Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin mengatur akun pengguna untuk guru yang dipilih? Berikut adalah daftar guru
                        yang akan dibuatkan akun:</p>
                    <ul id="guruTerpilihList"></ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="generateAkunGuru" disabled>Generate Akun</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let checkAll = document.getElementById("checkAll");
            let btnAturPengguna = document.querySelector("[data-bs-target='#aturPenggunaModal']");
            let btnGenerateAkun = document.getElementById("generateAkunGuru");
            let guruTerpilihList = document.getElementById("guruTerpilihList");

            /** ✅ Cek All: Saat #checkAll diubah, semua checkbox akan mengikuti */
            if (checkAll) {
                checkAll.addEventListener("change", function() {
                    let isChecked = this.checked;
                    document.querySelectorAll(".guru-checkbox").forEach(el => el.checked = isChecked);
                });
            }

            /** ✅ Jika semua checkbox diceklis, checkAll juga aktif */
            document.addEventListener("change", function(event) {
                if (event.target.classList.contains("guru-checkbox")) {
                    let allChecked = document.querySelectorAll(".guru-checkbox").length === document
                        .querySelectorAll(".guru-checkbox:checked").length;
                    checkAll.checked = allChecked;
                }
            });

            /** ✅ Event untuk klik tombol Reset Password */
            document.querySelectorAll(".reset-password").forEach(button => {
                button.addEventListener("click", function() {
                    selectedGuruId = this.dataset.id; // Simpan ID Guru
                    let guruNama = this.dataset.nama;

                    // Tampilkan daftar guru yang akan direset passwordnya
                    guruTerpilihList.innerHTML = `<li>${guruNama}</li>`;
                    btnGenerateAkun.disabled = false; // Aktifkan tombol generate akun

                    // Simpan mode reset password
                    btnGenerateAkun.dataset.isResetPassword = "true";
                    btnGenerateAkun.dataset.guruIds = JSON.stringify([selectedGuruId]);

                    // Buka modal
                    let modal = new bootstrap.Modal(document.getElementById("aturPenggunaModal"));
                    modal.show();
                });
            });

            /** ✅ Klik "Atur Pengguna" untuk menampilkan daftar guru terpilih di modal */
            btnAturPengguna.addEventListener("click", function() {
                let selectedIds = [];
                let selectedNames = [];

                document.querySelectorAll(".guru-checkbox:checked").forEach(el => {
                    selectedIds.push(el.value);
                    let namaGuru = el.closest("tr").querySelector("td:nth-child(3)");
                    if (namaGuru) {
                        selectedNames.push(namaGuru.innerText);
                    }
                });

                // Jika ada data yang dipilih, tampilkan di modal
                if (selectedIds.length > 0) {
                    guruTerpilihList.innerHTML = selectedNames.map(name => `<li>${name}</li>`).join("");
                    btnGenerateAkun.disabled = false; // Aktifkan tombol "Generate Akun"
                } else {
                    guruTerpilihList.innerHTML =
                        "<li class='text-danger'>Tidak ada guru yang dipilih.</li>";
                    btnGenerateAkun.disabled = true; // Nonaktifkan tombol jika tidak ada data
                }

                // Simpan data ID yang dipilih ke dalam tombol "Generate Akun"
                btnGenerateAkun.dataset.guruIds = JSON.stringify(selectedIds);
            });

            /** ✅ Klik "Generate Akun" untuk mengirim data ke server */
            btnGenerateAkun.addEventListener("click", function() {
                let csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
                if (!csrfTokenMeta) {
                    console.error("CSRF token meta tag not found!");
                    return;
                }

                let selectedIds = JSON.parse(btnGenerateAkun.dataset.guruIds);

                if (selectedIds.length === 0) {
                    Swal.fire("Peringatan!", "Pilih minimal satu guru!", "warning");
                    return;
                }

                // Ubah teks tombol agar menunjukkan sedang diproses
                btnGenerateAkun.innerHTML = "Memproses...";
                btnGenerateAkun.disabled = true;

                fetch("{{ route('admin.guru.generateAkun') }}", {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": csrfTokenMeta.getAttribute("content"),
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({
                            guru_ids: selectedIds
                        })
                    }).then(response => response.json())
                    .then(data => {
                        Swal.fire("Sukses!", data.message, "success").then(() => {
                            location.reload();
                        });
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        Swal.fire("Error!", "Terjadi kesalahan saat mengatur akun pengguna.", "error");
                        btnGenerateAkun.innerHTML = "Generate Akun";
                        btnGenerateAkun.disabled = false;
                    });
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".copy-password").forEach(button => {
                button.addEventListener("click", function() {
                    let password = this.dataset.password;
                    if (!password || password === '-') {
                        alert("Password tidak tersedia!");
                        return;
                    }

                    // Copy ke clipboard
                    navigator.clipboard.writeText(password).then(() => {
                        // Ganti ikon sementara menjadi ikon checklist
                        this.innerHTML =
                            '<i class="icon-base ti tabler-check text-success"></i>';
                        this.title = "Copied!";

                        // Kembalikan ikon setelah 2 detik
                        setTimeout(() => {
                            this.innerHTML =
                                '<i class="icon-base ti tabler-copy"></i>';
                            this.title = "Copy Password";
                        }, 2000);
                    }).catch(err => {
                        console.error("Gagal menyalin password", err);
                        alert("Gagal menyalin password!");
                    });
                });
            });
        });
    </script>
@endsection
