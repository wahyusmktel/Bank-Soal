@extends('layouts.app')

@section('title', 'Mapping Mata Pelajaran')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Mapping Mata Pelajaran</h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMappingModal">
                Tambah Mapping
            </button>
        </div>
        <div class="card-body">

            <!-- Form Pencarian dan Filter -->
            <form method="GET" action="{{ route('admin.maping.index') }}" class="mb-3">
                <div class="row">
                    <!-- Filter Nama Guru -->
                    <div class="col-md-3 mt-2">
                        <label class="form-label">Cari Nama Guru</label>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Masukan Nama Guru">
                    </div>

                    <!-- Filter Data Ujian -->
                    <div class="col-md-3 mt-2">
                        <label class="form-label">Data Ujian</label>
                        <select name="data_ujian_id" class="form-control">
                            <option value="">-- Pilih Ujian --</option>
                            @foreach ($dataUjians as $ujian)
                                <option value="{{ $ujian->id }}"
                                    {{ request('data_ujian_id') == $ujian->id ? 'selected' : '' }}>
                                    {{ $ujian->nama_ujian }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Tahun Pelajaran (Dengan Semester) -->
                    <div class="col-md-3 mt-2">
                        <label class="form-label">Tahun Pelajaran</label>
                        <select name="tahun_pelajaran_id" class="form-control">
                            <option value="">-- Pilih Tahun - Semester --</option>
                            @foreach ($tahunPelajarans as $tahun)
                                <option value="{{ $tahun->id }}"
                                    {{ request('tahun_pelajaran_id') == $tahun->id ? 'selected' : '' }}>
                                    {{ $tahun->nama_tahun }} - Semester {{ $tahun->semester }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tombol Submit -->
                    <div class="col-md-3 mt-8">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('admin.maping.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>

            <!-- Tabel Mapping Mata Pelajaran -->
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Guru</th>
                            <th>Ujian</th>
                            <th>Tahun Pelajaran</th>
                            <th>Mata Pelajaran</th>
                            <th>Jumlah Mapel</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mapingMapels as $index => $maping)
                            <tr>
                                <td align="left" valign="top">
                                    {{ ($mapingMapels->currentPage() - 1) * $mapingMapels->perPage() + $index + 1 }}</td>
                                <td align="left" valign="top">{{ $maping->guru->Nama }}</td>
                                <td align="left" valign="top">{{ $maping->dataUjian->nama_ujian }}</td>
                                <td align="left" valign="top">
                                    {{ $maping->dataUjian->tahunPelajaran->nama_tahun ?? '-' }} - Semester
                                    {{ $maping->dataUjian->tahunPelajaran->semester ?? '-' }}</td>
                                <td>
                                    {!! $maping->mapel_kelas_nama !!}
                                </td>
                                <td align="left" valign="top">{{ $maping->jumlah_mapel }}</td>
                                <td>
                                    <span class="badge {{ $maping->status ? 'bg-success' : 'bg-danger' }}">
                                        {{ $maping->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="icon-base ti tabler-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <!-- Tombol Edit -->
                                            <a class="dropdown-item edit-mapping" href="javascript:void(0);"
                                                data-id="{{ $maping->id }}" data-guru="{{ $maping->guru_id }}"
                                                data-ujian="{{ $maping->data_ujian_id }}"
                                                data-mata-pelajaran="{{ $maping->mata_pelajaran_id }}"
                                                data-status="{{ $maping->status }}">
                                                <i class="icon-base ti tabler-edit me-1"></i> Edit
                                            </a>

                                            <!-- Tombol Delete -->
                                            <form action="{{ route('admin.maping.destroy', $maping->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger delete-mapping">
                                                    <i class="icon-base ti tabler-trash me-1"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data mapping.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-3">
                {{ $mapingMapels->links('vendor.pagination.bootstrap-4') }}
            </div>
        </div>
    </div>

    <!-- Modal Tambah Mapping -->
    <div class="modal fade" id="addMappingModal" data-bs-backdrop="static" tabindex="-1"
        aria-labelledby="addMappingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addMappingModalLabel">Tambah Mapping Mata Pelajaran & Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.maping.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Pilih Guru</label>
                            <select class="form-control" name="guru_id" required>
                                <option value="">-- Pilih Guru --</option>
                                @foreach ($gurus as $guru)
                                    <option value="{{ $guru->id }}">{{ $guru->Nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pilih Ujian</label>
                            <select class="form-control" name="data_ujian_id" required>
                                <option value="">-- Pilih Ujian --</option>
                                @foreach ($dataUjians as $ujian)
                                    <option value="{{ $ujian->id }}">{{ $ujian->nama_ujian }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Pilihan Mata Pelajaran dan Kelas -->
                        <div class="mb-3">
                            <label class="form-label">Pilih Mata Pelajaran & Kelas</label>
                            <div id="mapelKelasContainer">
                                <div class="d-flex mb-2">
                                    <select class="form-control mapel-select" name="mata_pelajaran_id[]" required>
                                        <option value="">-- Pilih Mata Pelajaran --</option>
                                        @foreach ($mataPelajarans as $mapel)
                                            <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }}</option>
                                        @endforeach
                                    </select>
                                    <select class="form-control kelas-select ms-2" name="kelas_id[0][]" multiple required>
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach ($kelasuuu as $item)
                                            <option value="{{ $item->id }}">{{ $item->nama_kelas }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-danger ms-2 remove-mapping">X</button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-success mt-2" id="addMapping">Tambah Mata
                                Pelajaran</button>
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

    <!-- Modal Edit Mapping -->
    <div class="modal fade" id="editMappingModal" data-bs-backdrop="static" tabindex="-1"
        aria-labelledby="editMappingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editMappingModalLabel">Edit Mapping Mata Pelajaran & Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editMappingForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" id="editMappingId" name="id">

                        <div class="mb-3">
                            <label class="form-label">Pilih Guru</label>
                            <select class="form-control" name="guru_id" id="editGuruId" required>
                                <option value="">-- Pilih Guru --</option>
                                @foreach ($gurus as $guru)
                                    <option value="{{ $guru->id }}">{{ $guru->Nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Pilih Ujian</label>
                            <select class="form-control" name="data_ujian_id" id="editUjianId" required>
                                <option value="">-- Pilih Ujian --</option>
                                @foreach ($dataUjians as $ujian)
                                    <option value="{{ $ujian->id }}">{{ $ujian->nama_ujian }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Mata Pelajaran & Kelas -->
                        <div class="mb-3">
                            <label class="form-label">Pilih Mata Pelajaran & Kelas</label>
                            <div id="editMapelKelasContainer"></div>
                            <button type="button" class="btn btn-success mt-2" id="addMappingEdit">Tambah Mata
                                Pelajaran</button>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-control" name="status" id="editStatus" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("addMapping").addEventListener("click", function() {
                let container = document.getElementById("mapelKelasContainer");
                container.appendChild(createMapelKelasRow());
            });

            document.getElementById("addMappingEdit").addEventListener("click", function() {
                let container = document.getElementById("editMapelKelasContainer");
                container.appendChild(createMapelKelasRow());
            });

            document.querySelectorAll(".edit-mapping").forEach(function(button) {
                button.addEventListener("click", function() {
                    let mappingId = this.getAttribute("data-id");
                    let guruId = this.getAttribute("data-guru");
                    let ujianId = this.getAttribute("data-ujian");
                    let mataPelajaranData = JSON.parse(this.getAttribute("data-mata-pelajaran"));
                    let status = this.getAttribute("data-status");

                    document.getElementById("editMappingId").value = mappingId;
                    document.getElementById("editGuruId").value = guruId;
                    document.getElementById("editUjianId").value = ujianId;
                    document.getElementById("editStatus").value = status;

                    let container = document.getElementById("editMapelKelasContainer");
                    container.innerHTML = "";

                    mataPelajaranData.forEach(function(data) {
                        let row = createMapelKelasRow(data.mata_pelajaran_id, data
                            .kelas_id);
                        container.appendChild(row);
                    });

                    document.getElementById("editMappingForm").setAttribute("action",
                        "/admin/maping/" + mappingId);
                    new bootstrap.Modal(document.getElementById("editMappingModal")).show();
                });
            });



            function createMapelKelasRow(selectedMapel = "", selectedKelas = []) {
                let newRow = document.createElement("div");
                newRow.classList.add("d-flex", "mb-2");

                let mapelSelect = `
            <select class="form-control mapel-select" name="mata_pelajaran_id[]" required>
                <option value="">-- Pilih Mata Pelajaran --</option>
                @foreach ($mataPelajarans as $mapel)
                    <option value="{{ $mapel->id }}" ${selectedMapel === '{{ $mapel->id }}' ? 'selected' : ''}>
                        {{ $mapel->nama_mapel }}
                    </option>
                @endforeach
            </select>
        `;

                let kelasSelect = `
            <select class="form-control kelas-select ms-2" name="kelas_id[${selectedMapel}][]" multiple required>
                <option value="">-- Pilih Kelas --</option>
                @foreach ($kelasuuu as $item)
                    <option value="{{ $item->id }}" ${selectedKelas.includes('{{ $item->id }}') ? 'selected' : ''}>
                        {{ $item->nama_kelas }}
                    </option>
                @endforeach
            </select>
        `;

                newRow.innerHTML = mapelSelect + kelasSelect + `
            <button type="button" class="btn btn-danger ms-2 remove-mapping">X</button>
        `;

                newRow.querySelector(".remove-mapping").addEventListener("click", function() {
                    newRow.remove();
                });

                return newRow;
            }
        });

        document.querySelectorAll(".delete-mapping").forEach(function(button) {
            button.addEventListener("click", function(event) {
                if (!confirm("Apakah Anda yakin ingin menghapus mapping ini?")) {
                    event.preventDefault();
                }
            });
        });
    </script>

@endsection
