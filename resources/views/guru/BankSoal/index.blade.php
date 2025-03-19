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
                        $mapelKelasData = json_decode($soal->mata_pelajaran_id, true) ?? [];
                        $mapelId = $mapelKelasData['mata_pelajaran_id'] ?? null;
                        $kelasIds = $mapelKelasData['kelas_id'] ?? [];

                        $mapelNama = \App\Models\MataPelajaran::where('id', $mapelId)->value('nama_mapel');
                        $kelasList = \App\Models\Kelas::whereIn('id', $kelasIds)->pluck('nama_kelas')->toArray();
                    @endphp
                    <tr>
                        <td>{{ ($bankSoals->currentPage() - 1) * $bankSoals->perPage() + $index + 1 }}</td>
                        <td>{{ $mapelNama ?? 'Unknown Mapel' }}</td>
                        <td>{{ implode(', ', $kelasList) ?: 'Tidak ada kelas' }}</td>
                        <td>
                            <a href="{{ asset('storage/' . $soal->file_soal) }}" target="_blank">
                                {{ basename($soal->file_soal) }}
                            </a>
                        </td>
                        <td>
                            <span class="badge {{ $soal->status ? 'bg-success' : 'bg-danger' }}">
                                {{ $soal->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-primary open-zip-modal"
                                data-id="{{ $soal->id }}">
                                Lihat Soal
                            </button>
                            <button type="button" class="btn btn-sm btn-warning open-preview-modal"
                                data-id="{{ $soal->id }}">
                                Preview Soal
                            </button>
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
    {{-- <div class="modal fade" id="addBankSoalModal" data-bs-backdrop="static" tabindex="-1"
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
    </div> --}}

    <!-- Modal Tambah Bank Soal -->
    <div class="modal fade" id="addBankSoalModal" data-bs-backdrop="static" tabindex="-1"
        aria-labelledby="addBankSoalModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBankSoalModalLabel">Tambah Bank Soal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="bankSoalForm" action="{{ route('guru.bank-soal.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <!-- Pilihan Mata Pelajaran -->
                        <div class="mb-3">
                            <label class="form-label">Pilih Mata Pelajaran <span class="text-danger">*</span></label>
                            <select id="mataPelajaranSelect" class="form-control" name="mata_pelajaran_id" required>
                                <option value="">-- Pilih Mata Pelajaran --</option>
                                @foreach ($mapingMapels as $maping)
                                    @foreach ($maping->mapel_kelas_list as $data)
                                        <option value="{{ $data['mata_pelajaran_id'] }}"
                                            data-kelas="{{ json_encode($data['kelas']) }}">
                                            {{ $data['mapel'] }}
                                        </option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>

                        <!-- Pilihan Kelas (Multiple Select) -->
                        <div class="mb-3">
                            <label class="form-label">Pilih Kelas <span class="text-danger">*</span></label>
                            <select id="kelasSelect" class="form-control select2" name="kelas_id[]" multiple="multiple"
                                required>
                                <option value="">-- Pilih Mata Pelajaran Terlebih Dahulu --</option>
                            </select>
                            <small class="text-muted">Bisa memilih lebih dari satu kelas</small>
                        </div>

                        <!-- Upload File Soal -->
                        <div class="mb-3">
                            <label class="form-label">Upload File Soal <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="file_soal" id="fileSoal" accept=".zip"
                                required>
                            <small class="text-muted">Format file: .zip (Max: 5MB)</small>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="submitButton">Simpan</button>
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
            let mataPelajaranSelect = document.getElementById("mataPelajaranSelect");
            let kelasSelect = document.getElementById("kelasSelect");
            let fileSoal = document.getElementById("fileSoal");

            // Update pilihan kelas berdasarkan mata pelajaran yang dipilih
            mataPelajaranSelect.addEventListener("change", function() {
                let selectedOption = this.options[this.selectedIndex];
                let kelasData = selectedOption.getAttribute("data-kelas");

                // Kosongkan kelasSelect sebelum menambahkan pilihan baru
                kelasSelect.innerHTML = "";

                if (kelasData) {
                    let kelasList = JSON.parse(kelasData); // Pastikan kelasData dalam bentuk array

                    if (Array.isArray(kelasList) && kelasList.length > 0) {
                        kelasList.forEach(kelas => {
                            let option = document.createElement("option");
                            option.value = kelas.id; // Menggunakan UUID sebagai value
                            option.textContent = kelas.nama; // Menampilkan nama kelas
                            kelasSelect.appendChild(option);
                        });
                    } else {
                        kelasSelect.innerHTML = `<option value="">-- Tidak Ada Kelas Tersedia --</option>`;
                    }
                } else {
                    kelasSelect.innerHTML =
                        `<option value="">-- Pilih Mata Pelajaran Terlebih Dahulu --</option>`;
                }
            });

            // Validasi File Soal (Hanya ZIP & Maksimal 5MB)
            fileSoal.addEventListener("change", function() {
                let file = this.files[0];
                if (file) {
                    let fileSizeMB = file.size / (1024 * 1024);
                    if (!file.name.endsWith(".zip") || fileSizeMB > 5) {
                        alert("Format file harus .zip dan maksimal 5MB!");
                        this.value = "";
                    }
                }
            });

            // Cegah Submit Jika Input Tidak Lengkap
            document.getElementById("bankSoalForm").addEventListener("submit", function(event) {
                if (!mataPelajaranSelect.value || kelasSelect.value.length === 0 || !fileSoal.value) {
                    alert("Pastikan semua input sudah diisi!");
                    event.preventDefault();
                }
            });
        });
    </script>

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

    <style>
        .pagination-container {
            display: flex;
            flex-wrap: wrap;
            /* 🔹 Membungkus ke baris baru jika penuh */
            justify-content: center;
            max-width: 100%;
            overflow: hidden;
            /* 🔹 Menghindari overflow */
            padding: 10px;
            gap: 5px;
        }

        .pagination .page-item {
            flex: 0 1 auto;
            /* 🔹 Membuat item tidak memanjang */
        }

        .pagination .page-item a {
            min-width: 40px;
            /* 🔹 Ukuran tombol yang seragam */
            text-align: center;
            padding: 8px;
            border-radius: 5px;
        }
    </style>

    <!-- Modal untuk Preview Soal -->
    <div class="modal fade" id="previewSoalModal" data-bs-backdrop="static" tabindex="-1"
        aria-labelledby="previewSoalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewSoalModalLabel">Preview Soal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="soalPreviewContent">
                        <p class="text-muted">Memuat soal...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    {{-- <div class="col-lg-12">
                        <div class="demo-inline-spacing">
                            <nav aria-label="Page navigation">
                                <ul class="pagination pagination-rounded justify-content-center">
                                    <!-- Pagination akan diisi oleh JavaScript -->
                                </ul>
                            </nav>
                        </div>
                    </div> --}}
                    <div class="col-lg-12">
                        <div class="pagination-container">
                            <nav aria-label="Page navigation">
                                <ul class="pagination pagination-rounded justify-content-center" id="paginationContainer">
                                    <!-- 🔹 Nomor soal akan dimasukkan lewat JavaScript -->
                                </ul>
                            </nav>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk Preview Soal Fix-->
    {{-- <div class="modal fade" id="previewSoalModal" data-bs-backdrop="static" tabindex="-1"
        aria-labelledby="previewSoalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewSoalModalLabel">Preview Soal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="soalPreviewContent">
                        <p class="text-muted">Memuat soal...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col-lg-12">
                        <div class="demo-inline-spacing">
                            <nav aria-label="Page navigation">
                                <ul class="pagination pagination-rounded justify-content-center">
                                    <li class="page-item prev">
                                        <a class="page-link" href="javascript:void(0);"><i
                                                class="icon-base ti tabler-chevrons-left icon-sm"></i></a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="javascript:void(0);">1</a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="javascript:void(0);">2</a>
                                    </li>
                                    <li class="page-item active">
                                        <a class="page-link" href="javascript:void(0);">3</a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="javascript:void(0);">4</a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="javascript:void(0);">5</a>
                                    </li>
                                    <li class="page-item next">
                                        <a class="page-link" href="javascript:void(0);"><i
                                                class="icon-base ti tabler-chevrons-right icon-sm"></i></a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}


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

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".open-preview-modal").forEach(function(element) {
                element.addEventListener("click", function() {
                    let soalId = this.getAttribute("data-id");

                    fetch(`/guru/bank-soal/preview/${soalId}`)
                        .then(response => response.json())
                        .then(data => {
                            console.log("Data Diterima dari Server:", data); // Debugging

                            let previewContainer = document.getElementById(
                                "soalPreviewContent");
                            let paginationContainer = document.querySelector(".pagination");
                            previewContainer.innerHTML = "";
                            paginationContainer.innerHTML = "";

                            if (data.success) {
                                if (data.questions.length === 0) {
                                    previewContainer.innerHTML =
                                        `<p class='text-warning'>Soal tidak ditemukan dalam file.</p>`;
                                    return;
                                }

                                let currentPage = 0; // Mulai dari soal pertama
                                let totalQuestions = data.questions.length;

                                function showQuestion(index) {
                                    let question = data.questions[index];
                                    previewContainer.innerHTML = `
                                <h6><strong>Soal ${index + 1}:</strong> ${question.text}</h6>
                                <ul>
                                    ${question.options.map(opt => `<li>${opt}</li>`).join("")}
                                </ul>
                                <p><strong>Jawaban Benar:</strong> ${question.correctAnswer}</p>
                            `;
                                    updatePagination(index);
                                }

                                function updatePagination(currentIndex) {
                                    let paginationHTML = `
        <li class="page-item prev ${currentIndex === 0 ? 'disabled' : ''}">
            <a class="page-link prev-btn" href="javascript:void(0);"><i class="icon-base ti tabler-chevrons-left icon-sm"></i></a>
        </li>
    `;

                                    for (let i = 0; i < totalQuestions; i++) {
                                        paginationHTML += `
            <li class="page-item ${i === currentIndex ? 'active' : ''}">
                <a class="page-link page-btn" href="javascript:void(0);" data-index="${i}">${i + 1}</a>
            </li>
        `;
                                    }

                                    paginationHTML += `
        <li class="page-item next ${currentIndex === totalQuestions - 1 ? 'disabled' : ''}">
            <a class="page-link next-btn" href="javascript:void(0);"><i class="icon-base ti tabler-chevrons-right icon-sm"></i></a>
        </li>
    `;

                                    paginationContainer.innerHTML = paginationHTML;

                                    // Tambahkan event listener untuk tombol nomor soal
                                    document.querySelectorAll(".pagination .page-btn").forEach(
                                        item => {
                                            item.addEventListener("click", function() {
                                                let newIndex = parseInt(this
                                                    .getAttribute("data-index"));
                                                if (!isNaN(newIndex)) {
                                                    currentPage = newIndex;
                                                    showQuestion(currentPage);
                                                }
                                            });
                                        });

                                    // Event listener untuk tombol prev dan next
                                    document.querySelector(".pagination .prev-btn")
                                        .addEventListener("click", function() {
                                            if (currentPage > 0) {
                                                currentPage--;
                                                showQuestion(currentPage);
                                            }
                                        });

                                    document.querySelector(".pagination .next-btn")
                                        .addEventListener("click", function() {
                                            if (currentPage < totalQuestions - 1) {
                                                currentPage++;
                                                showQuestion(currentPage);
                                            }
                                        });
                                }

                                // Tampilkan soal pertama kali
                                showQuestion(currentPage);

                                // Tampilkan modal
                                let modal = new bootstrap.Modal(document.getElementById(
                                    "previewSoalModal"));
                                modal.show();
                            } else {
                                previewContainer.innerHTML =
                                    `<p class='text-danger'>${data.message}</p>`;
                            }
                        })
                        .catch(error => {
                            console.error("Error:", error);
                            document.getElementById("soalPreviewContent").innerHTML =
                                "<p class='text-danger'>Gagal memuat soal.</p>";
                        });
                });
            });
        });

        // Sudah Fix
        // document.addEventListener("DOMContentLoaded", function() {
        //     document.querySelectorAll(".open-preview-modal").forEach(function(element) {
        //         element.addEventListener("click", function() {
        //             let soalId = this.getAttribute("data-id");

        //             fetch(`/guru/bank-soal/preview/${soalId}`)
        //                 .then(response => response.json())
        //                 .then(data => {
        //                     console.log("Data Diterima dari Server:", data); // Debugging

        //                     let previewContainer = document.getElementById(
        //                         "soalPreviewContent");
        //                     previewContainer.innerHTML = "";

        //                     if (data.success) {
        //                         if (data.questions.length === 0) {
        //                             previewContainer.innerHTML =
        //                                 `<p class='text-warning'>Soal tidak ditemukan dalam file.</p>`;
        //                             return;
        //                         }

        //                         data.questions.forEach((question, index) => {
        //                             let questionElement = document.createElement("div");
        //                             questionElement.innerHTML = `
    //                         <h6><strong>Soal ${index + 1}:</strong> ${question.text}</h6>
    //                         <ul>
    //                             ${question.options.map(opt => `<li>${opt}</li>`).join("")}
    //                         </ul>
    //                         <p><strong>Jawaban Benar:</strong> ${question.correctAnswer}</p>
    //                         <hr>
    //                     `;
        //                             previewContainer.appendChild(questionElement);
        //                         });

        //                         let modal = new bootstrap.Modal(document.getElementById(
        //                             "previewSoalModal"));
        //                         modal.show();
        //                     } else {
        //                         previewContainer.innerHTML =
        //                             `<p class='text-danger'>${data.message}</p>`;
        //                     }
        //                 })
        //                 .catch(error => {
        //                     console.error("Error:", error);
        //                     document.getElementById("soalPreviewContent").innerHTML =
        //                         "<p class='text-danger'>Gagal memuat soal.</p>";
        //                 });
        //         });
        //     });
        // });

        // document.addEventListener("DOMContentLoaded", function() {
        //     document.querySelectorAll(".open-preview-modal").forEach(function(element) {
        //         element.addEventListener("click", function() {
        //             let soalId = this.getAttribute("data-id");

        //             fetch(`/guru/bank-soal/preview/${soalId}`)
        //                 .then(response => response.json())
        //                 .then(data => {
        //                     console.log("Data Diterima dari Server:", data); // Debugging

        //                     let previewContainer = document.getElementById(
        //                         "soalPreviewContent");
        //                     previewContainer.innerHTML = "";

        //                     if (data.success) {
        //                         if (data.questions.length === 0) {
        //                             previewContainer.innerHTML =
        //                                 `<p class='text-warning'>Soal tidak ditemukan dalam file.</p>`;
        //                             return;
        //                         }

        //                         data.questions.forEach((question, index) => {
        //                             let questionElement = document.createElement("div");
        //                             questionElement.innerHTML = `
    //                         <h6><strong>Soal ${index + 1}:</strong> ${question.text}</h6>
    //                         <ul>
    //                             ${question.options.map(opt => `<li>${opt}</li>`).join("")}
    //                         </ul>
    //                         <p><strong>Jawaban Benar:</strong> ${question.correctAnswer}</p>
    //                         <hr>
    //                     `;
        //                             previewContainer.appendChild(questionElement);
        //                         });

        //                         let modal = new bootstrap.Modal(document.getElementById(
        //                             "previewSoalModal"));
        //                         modal.show();
        //                     } else {
        //                         previewContainer.innerHTML =
        //                             `<p class='text-danger'>${data.message}</p>`;
        //                     }
        //                 })
        //                 .catch(error => {
        //                     console.error("Error:", error);
        //                     document.getElementById("soalPreviewContent").innerHTML =
        //                         "<p class='text-danger'>Gagal memuat soal.</p>";
        //                 });
        //         });
        //     });
        // });
    </script>
@endsection
