@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Mapping Mata Pelajaran</h2>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">Tambah Mapping</button>

        {{-- Tabel Data --}}
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Guru</th>
                    <th>Data Ujian</th>
                    <th>Tahun Pelajaran</th> <!-- Tambahan -->
                    <th>Semester</th> <!-- Tambahan -->
                    <th>Mata Pelajaran & Kelas</th>
                    <th>Jumlah Bank Soal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $groupedMapings = $mapings->groupBy('guru_id');
                    $no = 1;
                @endphp

                @foreach ($groupedMapings as $guru_id => $mapingGroup)
                    @php
                        $rowspan = count($mapingGroup);
                        $first = true;
                    @endphp

                    @foreach ($mapingGroup as $maping)
                        <tr>
                            @if ($first)
                                <td rowspan="{{ $rowspan }}" class="text-start align-top">{{ $no++ }}</td>
                            @endif

                            @if ($first)
                                <td rowspan="{{ $rowspan }}" class="text-start align-top">{{ $maping->guru->Nama }}</td>
                            @endif

                            <td class="text-start align-top">{{ $maping->dataUjian->nama_ujian }}</td>
                            <td>{{ $maping->dataUjian->tahunPelajaran->nama_tahun ?? '-' }}</td>
                            <td>{{ $maping->dataUjian->tahunPelajaran->semester ?? '-' }}</td>

                            <td class="text-start align-top">
                                @php
                                    $mataPelajaran = json_decode($maping->mata_pelajaran_id, true);
                                @endphp
                                @if (!empty($mataPelajaran) && is_array($mataPelajaran))
                                    @foreach ($mataPelajaran as $mapel)
                                        @php
                                            $mapelNama = \App\Models\MataPelajaran::find($mapel['mata_pelajaran_id']);
                                            $kelasNama = \App\Models\Kelas::whereIn('id', $mapel['kelas_id'])
                                                ->pluck('nama_kelas')
                                                ->toArray();
                                        @endphp
                                        <strong>{{ $mapelNama->nama_mapel ?? 'Tidak Ditemukan' }}</strong>:
                                        {{ implode(', ', $kelasNama) }} <br>
                                    @endforeach
                                @else
                                    <span class="text-muted">Belum ada data</span>
                                @endif
                            </td>

                            <td class="text-center align-middle">
                                @php
                                    // Hitung jumlah mata pelajaran spesifik di baris ini
                                    $jumlahMapelBaris = count($mataPelajaran ?? []);
                                @endphp
                                {{ $jumlahMapelBaris }}
                            </td>

                            @if ($first)
                                <td rowspan="{{ $rowspan }}" class="align-middle">
                                    <span class="badge bg-success">Aktif</span>
                                </td>
                            @endif


                            <td>
                                <button class="btn btn-warning btn-sm edit-btn" data-bs-toggle="modal"
                                    data-bs-target="#editModal" data-id="{{ $maping->id }}"
                                    data-guru_id="{{ $maping->guru_id }}"
                                    data-data_ujian_id="{{ $maping->data_ujian_id }}"
                                    data-mata_pelajaran="{{ $maping->mata_pelajaran_id }}">
                                    Edit
                                </button>

                                <form action="{{ route('admin.maping.destroy', $maping->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>

                        @php $first = false; @endphp
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Modal Tambah Data --}}
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('admin.maping.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Mapping Mata Pelajaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-2">
                            <label>Nama Guru</label>
                            <select name="guru_id" class="form-control" required>
                                <option value="" disabled selected>Pilih Guru</option>
                                @foreach ($gurus as $guru)
                                    <option value="{{ $guru->id }}">{{ $guru->Nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <label>Data Ujian</label>
                            <select name="data_ujian_id" class="form-control" required>
                                <option value="" disabled selected>Pilih Data Ujian</option>
                                @foreach ($dataUjians as $dataUjian)
                                    <option value="{{ $dataUjian->id }}">{{ $dataUjian->nama_ujian }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="mapel-container">
                            <div class="row mb-2">
                                <div class="col-5">
                                    <label>Mata Pelajaran</label>
                                    <select class="form-control" name="mata_pelajaran[0][mata_pelajaran_id]" required>
                                        <option value="" disabled selected>Pilih Mata Pelajaran</option>
                                        @foreach ($mataPelajarans as $mapel)
                                            <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-5">
                                    <label>Kelas</label>
                                    <select class="form-control kelas-select" name="mata_pelajaran[0][kelas_id][]" multiple
                                        required>
                                        @foreach ($kelas as $kls)
                                            <option value="{{ $kls->id }}">{{ $kls->nama_kelas }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-danger remove-mapel">X</button>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-success" id="addMapel">Tambah Mata Pelajaran</button>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Mapping Mata Pelajaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit_id" name="id">

                        <div class="mb-2">
                            <label>Nama Guru</label>
                            <select name="guru_id" id="edit_guru_id" class="form-control" required>
                                <option value="" disabled selected>Pilih Guru</option>
                                @foreach ($gurus as $guru)
                                    <option value="{{ $guru->id }}">{{ $guru->Nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <label>Data Ujian</label>
                            <select name="data_ujian_id" id="edit_data_ujian_id" class="form-control" required>
                                <option value="" disabled selected>Pilih Data Ujian</option>
                                @foreach ($dataUjians as $dataUjian)
                                    <option value="{{ $dataUjian->id }}">{{ $dataUjian->nama_ujian }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="edit_mapel_container"></div>

                        <button type="button" class="btn btn-success" id="editAddMapel">Tambah Mata Pelajaran</button>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('addMapel').addEventListener('click', function() {
            let index = document.querySelectorAll('#mapel-container .row').length;
            let newRow = document.querySelector('#mapel-container .row').cloneNode(true);

            newRow.querySelector('select[name^="mata_pelajaran"]').setAttribute('name',
                `mata_pelajaran[${index}][mata_pelajaran_id]`);
            newRow.querySelector('select.kelas-select').setAttribute('name',
                `mata_pelajaran[${index}][kelas_id][]`);

            document.getElementById('mapel-container').appendChild(newRow);
        });

        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-mapel')) {
                event.target.closest('.row').remove();
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function() {
                    let id = this.getAttribute('data-id');
                    let guru_id = this.getAttribute('data-guru_id');
                    let data_ujian_id = this.getAttribute('data-data_ujian_id');
                    let mataPelajaran = JSON.parse(this.getAttribute('data-mata_pelajaran') ||
                        '[]');

                    document.getElementById('edit_id').value = id;
                    document.getElementById('edit_guru_id').value = guru_id;
                    document.getElementById('edit_data_ujian_id').value = data_ujian_id;

                    let container = document.getElementById('edit_mapel_container');
                    container.innerHTML = '';

                    mataPelajaran.forEach((mapel, index) => {
                        let html = generateMapelRow(index, mapel.mata_pelajaran_id, mapel
                            .kelas_id);
                        container.insertAdjacentHTML('beforeend', html);
                    });

                    document.getElementById('editForm').setAttribute('action',
                        `/admin/maping/${id}`);
                });
            });

            document.getElementById('editAddMapel').addEventListener('click', function() {
                let index = document.querySelectorAll('#edit_mapel_container .row').length;
                let html = generateMapelRow(index, null, []);
                document.getElementById('edit_mapel_container').insertAdjacentHTML('beforeend', html);
            });

            document.addEventListener('click', function(event) {
                if (event.target.classList.contains('remove-mapel')) {
                    event.target.closest('.row').remove();
                }
            });

            function generateMapelRow(index, selectedMapel, selectedKelas) {
                let mapelOptions = `@foreach ($mataPelajarans as $mapel)
            <option value="{{ $mapel->id }}" ${selectedMapel == "{{ $mapel->id }}" ? 'selected' : ''}>{{ $mapel->nama_mapel }}</option>
        @endforeach`;

                let kelasOptions = `@foreach ($kelas as $kls)
            <option value="{{ $kls->id }}" ${selectedKelas.includes("{{ $kls->id }}") ? 'selected' : ''}>{{ $kls->nama_kelas }}</option>
        @endforeach`;

                return `
            <div class="row mb-2">
                <div class="col-5">
                    <label>Mata Pelajaran</label>
                    <select class="form-control" name="mata_pelajaran[${index}][mata_pelajaran_id]" required>
                        <option value="" disabled selected>Pilih Mata Pelajaran</option>
                        ${mapelOptions}
                    </select>
                </div>
                <div class="col-5">
                    <label>Kelas</label>
                    <select class="form-control kelas-select" name="mata_pelajaran[${index}][kelas_id][]" multiple required>
                        ${kelasOptions}
                    </select>
                </div>
                <div class="col-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger remove-mapel">X</button>
                </div>
            </div>
        `;
            }
        });
    </script>
@endsection
