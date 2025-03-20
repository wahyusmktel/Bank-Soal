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
                    <th>Validasi Soal</th>
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
                            @php
                                // Ambil data validasi soal
                                $validasi = \App\Models\ValidasiSoal::where('bank_soals_id', $soal->id)->first();
                                $soalData = $validasi ? json_decode($validasi->soal, true) : [];

                                // Coba ambil total soal dari parsed_soal (jika tersimpan dalam database)
                                $totalSoal = !empty($soal->parsed_soal)
                                    ? count(json_decode($soal->parsed_soal, true))
                                    : 0;

                                // Jika parsed_soal kosong, coba hitung dari jumlah soal di validasi
                                if ($totalSoal == 0 && !empty($soalData)) {
                                    $totalSoal = count($soalData);
                                }

                                // Hitung jumlah soal yang sudah divalidasi (hanya yang memiliki keterangan_validasi = true)
                                $totalValidasi = !empty($soalData)
                                    ? count(
                                        array_filter($soalData, function ($s) {
                                            return isset($s['keterangan_validasi']) &&
                                                $s['keterangan_validasi'] == true;
                                        }),
                                    )
                                    : 0;
                            @endphp

                            <span
                                class="badge {{ $totalValidasi === $totalSoal && $totalSoal > 0 ? 'bg-success' : 'bg-danger' }}">
                                {{ $totalValidasi }}/{{ $totalSoal }}
                            </span>
                            <br>
                            <small>Jumlah Validasi / Jumlah Soal</small>
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
            justify-content: center;
            flex-wrap: wrap;
            max-width: 100%;
            overflow: hidden;
            padding: 10px;
        }

        .pagination-vertical {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            /* 🔹 Menampilkan 2 kolom */
            gap: 5px;
            list-style: none;
            padding: 0;
        }

        .pagination-vertical .page-item {
            text-align: center;
            margin: 2px;
        }

        .pagination-vertical .page-item a {
            display: block;
            width: 40px;
            /* 🔹 Lebar tombol yang seragam */
            text-align: center;
            padding: 8px;
            border-radius: 5px;
            background-color: #f1f1f1;
            text-decoration: none;
            color: #333;
            font-weight: bold;
        }

        .pagination-vertical .page-item.active a {
            background-color: #6c5ce7;
            color: white;
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
                    <div id="validasiAlert" class="alert d-none"></div> <!-- 🔹 Alert untuk validasi soal -->

                    <div class="row">
                        <!-- **Kolom Konten Soal** -->
                        <div class="col-md-8">
                            <div id="soalPreviewContent">
                                <p class="text-muted">Memuat soal...</p>
                            </div>
                        </div>

                        <!-- **Kolom Paginasi Vertikal** -->
                        <div class="col-md-4">
                            <div class="pagination-container">
                                <nav aria-label="Page navigation">
                                    <ul class="pagination pagination-vertical" id="paginationContainer"></ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- **Footer dengan Checkbox Validasi & Tombol Navigasi** -->
                <div class="modal-footer d-flex justify-content-between">
                    <button class="btn btn-secondary prev-btn">Previous</button>

                    <!-- 🔹 Checkbox Validasi -->
                    <div class="d-flex align-items-center">
                        <input type="checkbox" id="validasiSoalCheckbox" class="form-check-input">
                        <label for="validasiSoalCheckbox" class="form-check-label ms-2">Tandai sebagai valid</label>
                    </div>

                    <button class="btn btn-primary next-btn">Next</button>
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
                            console.log("Data Diterima dari Server:", data);

                            let previewContainer = document.getElementById(
                                "soalPreviewContent");
                            let paginationContainer = document.getElementById(
                                "paginationContainer");
                            let validasiCheckbox = document.getElementById(
                                "validasiSoalCheckbox");
                            let validasiAlert = document.getElementById(
                                "validasiAlert"); // 🔥 Tambahan alert

                            if (!previewContainer || !paginationContainer || !
                                validasiCheckbox || !validasiAlert) {
                                console.error("Elemen tidak ditemukan dalam DOM!");
                                return;
                            }

                            previewContainer.innerHTML = "";
                            paginationContainer.innerHTML = "";

                            if (data.success) {
                                if (data.questions.length === 0) {
                                    previewContainer.innerHTML =
                                        `<p class='text-warning'>Soal tidak ditemukan dalam file.</p>`;
                                    return;
                                }
                                let currentPage = 0;
                                let totalQuestions = data.questions.length;
                                let perRow = 4; // Jumlah soal per baris dalam pagination

                                /** ✅ Fungsi untuk menampilkan soal berdasarkan index **/
                                function showQuestion(index) {
                                    if (index < 0 || index >= totalQuestions) return;
                                    currentPage = index;

                                    let question = data.questions[index];
                                    previewContainer.innerHTML = `
                                <h6><strong>Soal ${index + 1}:</strong> ${question.text}</h6>
                                <ul>${question.options.map(opt => `<li>${opt}</li>`).join("")}</ul>
                                <p><strong>Jawaban Benar:</strong> ${question.correctAnswer}</p>
                            `;

                                    // 🔥 Update status checkbox
                                    validasiCheckbox.checked = question.keterangan_validasi ||
                                        false;

                                    // 🔥 Update alert validasi
                                    updateValidasiAlert(question.keterangan_validasi, index);

                                    updatePagination();
                                }

                                /** ✅ Fungsi untuk memperbarui alert validasi **/
                                function updateValidasiAlert(isValid, index) {
                                    if (isValid) {
                                        validasiAlert.textContent =
                                            `✅ Soal Nomor ${index + 1} sudah divalidasi`;
                                        validasiAlert.classList.remove("d-none",
                                            "alert-danger");
                                        validasiAlert.classList.add("alert-success");
                                    } else {
                                        validasiAlert.textContent =
                                            `⚠️ Soal Nomor ${index + 1} belum divalidasi`;
                                        validasiAlert.classList.remove("d-none",
                                            "alert-success");
                                        validasiAlert.classList.add("alert-danger");
                                    }
                                }

                                /** ✅ Fungsi untuk memperbarui pagination **/
                                function updatePagination() {
                                    let paginationHTML = "<div class='row'>";
                                    for (let i = 0; i < totalQuestions; i++) {
                                        if (i % perRow === 0) paginationHTML +=
                                            `<div class='col-12 d-flex justify-content-center'>`;

                                        // **🔥 Cek apakah soal ini sudah divalidasi**
                                        let isValidated = data.questions[i]
                                            .keterangan_validasi || false;
                                        let activeClass = i === currentPage ? 'active' : '';
                                        let validatedClass = isValidated ?
                                            'bg-success text-white' : '';

                                        paginationHTML += `
            <li class="page-item ${activeClass}">
                <a class="page-link page-btn ${validatedClass}" href="javascript:void(0);" data-index="${i}">
                    ${i + 1}
                </a>
            </li>
        `;

                                        if ((i + 1) % perRow === 0 || i === totalQuestions - 1)
                                            paginationHTML += `</div>`;
                                    }
                                    paginationHTML += "</div>";
                                    paginationContainer.innerHTML = paginationHTML;

                                    // ✅ Event listener ke tombol nomor soal
                                    document.querySelectorAll(".page-btn").forEach(item => {
                                        item.addEventListener("click", function() {
                                            let newIndex = parseInt(this
                                                .getAttribute("data-index"));
                                            if (!isNaN(newIndex)) {
                                                showQuestion(newIndex);
                                            }
                                        });
                                    });
                                }

                                /** ✅ Event listener untuk tombol prev dan next **/
                                document.querySelector(".prev-btn").onclick = function() {
                                    if (currentPage > 0) {
                                        showQuestion(currentPage - 1);
                                    }
                                };

                                document.querySelector(".next-btn").onclick = function() {
                                    if (currentPage < totalQuestions - 1) {
                                        showQuestion(currentPage + 1);
                                    }
                                };

                                /** ✅ Event listener untuk checkbox validasi **/
                                validasiCheckbox.onchange = function() {
                                    let csrfToken = document.querySelector(
                                        'meta[name="csrf-token"]').getAttribute(
                                        'content');

                                    fetch('/guru/bank-soal/validasi', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': csrfToken
                                            },
                                            body: JSON.stringify({
                                                bank_soals_id: soalId,
                                                nomor_soal: currentPage + 1,
                                                keterangan_validasi: validasiCheckbox
                                                    .checked
                                            })
                                        })
                                        .then(res => {
                                            if (!res.ok) {
                                                throw new Error(
                                                    `HTTP error! Status: ${res.status}`
                                                );
                                            }
                                            return res.json();
                                        })
                                        .then(response => {
                                            console.log("Validasi berhasil disimpan:",
                                                response.message);

                                            // **🔄 Perbarui data validasi di frontend**
                                            data.questions[currentPage]
                                                .keterangan_validasi = validasiCheckbox
                                                .checked;

                                            // **✅ Perbarui alert validasi secara real-time**
                                            updateValidasiAlert(validasiCheckbox
                                                .checked, currentPage);

                                            // **✅ Perbarui warna pagination**
                                            updatePagination();

                                            // **✅ Debugging log**
                                            console.log(
                                                `Soal ${currentPage + 1} status validasi diperbarui:`,
                                                validasiCheckbox.checked);
                                        })
                                        .catch(error => {
                                            console.error("Gagal menyimpan validasi:",
                                                error);
                                        });
                                };

                                /** ✅ Tampilkan soal pertama kali **/
                                showQuestion(currentPage);

                                /** ✅ Tampilkan modal **/
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
    </script>
@endsection
