@extends('layouts.app')

@section('title', 'Monitoring Bank Soal')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Monitoring Bank Soal</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Guru</th>
                            <th>Mata Pelajaran</th>
                            <th>Kelas</th>
                            <th>Sudah Upload</th>
                            <th>Aksi</th> <!-- Tambahkan kolom Aksi -->
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $rowspanCount = [];
                            foreach ($mapingMapelsList as $maping) {
                                $rowspanCount[$maping->guru_nama] = isset($rowspanCount[$maping->guru_nama])
                                    ? $rowspanCount[$maping->guru_nama] + 1
                                    : 1;
                            }
                        @endphp

                        @php $no = 1; @endphp
                        @foreach ($mapingMapelsList as $index => $maping)
                            <tr>
                                @if ($rowspanCount[$maping->guru_nama] !== 0)
                                    <td rowspan="{{ $rowspanCount[$maping->guru_nama] }}">{{ $no++ }}</td>
                                    <td rowspan="{{ $rowspanCount[$maping->guru_nama] }}">{{ $maping->guru_nama }}</td>
                                    @php $rowspanCount[$maping->guru_nama] = 0; @endphp
                                @endif
                                <td>{{ $maping->mata_pelajaran_nama }}</td>
                                <td>
                                    {!! $maping->kelas_list !!}
                                </td>
                                <td>
                                    <span class="badge {{ $maping->sudah_upload ? 'bg-success' : 'bg-danger' }}">
                                        {{ $maping->sudah_upload ? 'Sudah' : 'Belum' }}
                                    </span>
                                </td>
                                <td>
                                    @if ($maping->sudah_upload)
                                        <a href="javascript:void(0);" class="btn btn-sm btn-primary open-zip-modal"
                                            data-file="{{ asset('storage/' . $maping->file_soal) }}"
                                            data-id="{{ $maping->bank_soal_id }}">
                                            <i class="icon-base ti tabler-eye me-1"></i> Lihat File Soal
                                        </a>
                                    @else
                                        <span class="text-muted">Belum Upload</span>
                                    @endif
                                </td>


                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
                    fetch(`/admin/bank-soal/lihat-zip/${soalId}`)
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
