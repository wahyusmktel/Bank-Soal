@extends('layouts.app')

@section('title', 'Detail Pegawai')

@section('content')

    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <a class="btn btn-sm btn-icon btn-label-secondary waves-effect"
                        href="{{ route('admin.guru.index') }}"><span
                            class="icon-base ti tabler-chevron-left icon-20px scaleX-n1-rtl"></span></a>
                    <h5 class="mb-0">Detail Pegawai</h5>
                    <small class="text-body-secondary float-end">Informasi Lengkap</small>
                </div>
                <div class="card-body">
                    <form>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="divider text-start">
                                    <div class="divider-text">Biodata</div>
                                </div>
                            </div>
                            <!-- Kolom Kiri -->
                            <div class="col-md-6">
                                <div class="mb-3"><label class="form-label">Nama</label>
                                    <input type="text" class="form-control" value="{{ $guru->Nama }}" readonly />
                                </div>

                                <div class="mb-3"><label class="form-label">NIK</label>
                                    <input type="text" class="form-control" value="{{ $guru->NIK ?? '-' }}" readonly />
                                </div>
                                <div class="mb-3"><label class="form-label">No KK</label>
                                    <input type="text" class="form-control" value="{{ $guru->No_KK ?? '-' }}" readonly />
                                </div>
                                <div class="mb-3"><label class="form-label">Jenis Kelamin</label>
                                    <input type="text" class="form-control"
                                        value="{{ $guru->JK == 'L' ? 'Laki-Laki' : 'Perempuan' }}" readonly />
                                </div>
                                <div class="mb-3"><label class="form-label">Tempat Lahir</label>
                                    <input type="text" class="form-control" value="{{ $guru->Tempat_Lahir ?? '-' }}"
                                        readonly />
                                </div>
                                <div class="mb-3"><label class="form-label">Tanggal Lahir</label>
                                    <input type="text" class="form-control"
                                        value="{{ $guru->Tanggal_Lahir ? \Carbon\Carbon::parse($guru->Tanggal_Lahir)->translatedFormat('d F Y') : '-' }}"
                                        readonly />

                                </div>
                                <div class="mb-3"><label class="form-label">Agama</label>
                                    <input type="text" class="form-control" value="{{ $guru->Agama ?? '-' }}" readonly />
                                </div>
                                <div class="mb-3"><label class="form-label">Alamat Jalan</label>
                                    <input type="text" class="form-control" value="{{ $guru->Alamat_Jalan ?? '-' }}"
                                        readonly />
                                </div>
                                <div class="mb-3"><label class="form-label">RT/RW</label>
                                    <input type="text" class="form-control"
                                        value="{{ $guru->RT ?? '-' }}/{{ $guru->RW ?? '-' }}" readonly />
                                </div>
                                <div class="mb-3"><label class="form-label">Nama Ibu Kandung</label>
                                    <input type="text" class="form-control" value="{{ $guru->Nama_Ibu_Kandung ?? '-' }}"
                                        readonly />
                                </div>
                                <div class="mb-3"><label class="form-label">Kewarganegaraan</label>
                                    <input type="text" class="form-control" value="{{ $guru->Kewarganegaraan ?? '-' }}"
                                        readonly />
                                </div>


                            </div>

                            <!-- Kolom Kanan -->
                            <div class="col-md-6">
                                <div class="mb-3"><label class="form-label">Nama Dusun</label>
                                    <input type="text" class="form-control" value="{{ $guru->Nama_Dusun ?? '-' }}"
                                        readonly />
                                </div>
                                <div class="mb-3"><label class="form-label">Desa/Kelurahan</label>
                                    <input type="text" class="form-control" value="{{ $guru->Desa_Kelurahan ?? '-' }}"
                                        readonly />
                                </div>
                                <div class="mb-3"><label class="form-label">Kecamatan</label>
                                    <input type="text" class="form-control" value="{{ $guru->Kecamatan ?? '-' }}"
                                        readonly />
                                </div>
                                <div class="mb-3"><label class="form-label">Kode Pos</label>
                                    <input type="text" class="form-control" value="{{ $guru->Kode_Pos ?? '-' }}"
                                        readonly />
                                </div>
                                <div class="mb-3"><label class="form-label">No. HP</label>
                                    <input type="text" class="form-control" value="{{ $guru->HP ?? '-' }}" readonly />
                                </div>
                                <div class="mb-3"><label class="form-label">Email</label>
                                    <input type="text" class="form-control" value="{{ $guru->Email ?? '-' }}"
                                        readonly />
                                </div>
                                <div class="mb-3"><label class="form-label">Status Perkawinan</label>
                                    <input type="text" class="form-control"
                                        value="{{ $guru->Status_Perkawinan ?? '-' }}" readonly />
                                </div>
                                <div class="mb-3"><label class="form-label">Nama Suami/Istri</label>
                                    <input type="text" class="form-control"
                                        value="{{ $guru->Nama_Suami_Istri ?? '-' }}" readonly />
                                </div>
                                <div class="mb-3"><label class="form-label">Pekerjaan Suami/Istri</label>
                                    <input type="text" class="form-control"
                                        value="{{ $guru->Pekerjaan_Suami_Istri ?? '-' }}" readonly />
                                </div>


                                <div class="mb-3"><label class="form-label">Lintang</label>
                                    <input type="text" class="form-control" value="{{ $guru->Lintang ?? '-' }}"
                                        readonly />
                                </div>
                                <div class="mb-3"><label class="form-label">Bujur</label>
                                    <input type="text" class="form-control" value="{{ $guru->Bujur ?? '-' }}"
                                        readonly />
                                </div>

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="divider text-start">
                                    <div class="divider-text">Kepegawaian</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3"><label class="form-label">NUPTK</label>
                                    <input type="text" class="form-control" value="{{ $guru->NUPTK ?? '-' }}"
                                        readonly />
                                </div>
                                <div class="mb-3"><label class="form-label">NIP</label>
                                    <input type="text" class="form-control" value="{{ $guru->NIP ?? '-' }}"
                                        readonly />
                                </div>
                                <div class="mb-3"><label class="form-label">Status Kepegawaian</label>
                                    <input type="text" class="form-control"
                                        value="{{ $guru->Status_Kepegawaian ?? '-' }}" readonly />
                                </div>
                                <div class="mb-3"><label class="form-label">Jenis PTK</label>
                                    <input type="text" class="form-control" value="{{ $guru->Jenis_PTK ?? '-' }}"
                                        readonly />
                                </div>
                                <div class="mb-3"><label class="form-label">Tugas Tambahan</label>
                                    <input type="text" class="form-control"
                                        value="{{ $guru->Tugas_Tambahan ?? '-' }}" readonly />
                                </div>
                                <div class="mb-3"><label class="form-label">SK Pengangkatan</label>
                                    <input type="text" class="form-control"
                                        value="{{ $guru->SK_Pengangkatan ?? '-' }}" readonly />
                                </div>
                                <div class="mb-3"><label class="form-label">TMT Pengangkatan</label>
                                    <input type="text" class="form-control"
                                        value="{{ $guru->TMT_Pengangkatan ?? '-' }}" readonly />
                                </div>
                                <div class="mb-3"><label class="form-label">Lembaga Pengangkatan</label>
                                    <input type="text" class="form-control"
                                        value="{{ $guru->Lembaga_Pengangkatan ?? '-' }}" readonly />
                                </div>
                                {{-- <div class="mb-3"><label class="form-label">Pangkat Golongan</label>
                                    <input type="text" class="form-control"
                                        value="{{ $guru->Pangkat_Golongan ?? '-' }}" readonly />
                                </div> --}}

                                {{-- <div class="mb-3"><label class="form-label">SK CPNS</label>
                                    <input type="text" class="form-control" value="{{ $guru->SK_CPNS ?? '-' }}"
                                        readonly />
                                </div> --}}
                                {{-- <div class="mb-3"><label class="form-label">Tanggal CPNS</label>
                                    <input type="text" class="form-control" value="{{ $guru->Tanggal_CPNS ?? '-' }}"
                                        readonly />
                                </div> --}}
                                {{-- <div class="mb-3"><label class="form-label">NIP Suami/Istri</label>
                                    <input type="text" class="form-control"
                                        value="{{ $guru->NIP_Suami_Istri ?? '-' }}" readonly />
                                </div> --}}
                            </div>
                            <div class="col-md-6">
                                {{-- <div class="mb-3"><label class="form-label">TMT PNS</label>
                                    <input type="text" class="form-control" value="{{ $guru->TMT_PNS ?? '-' }}"
                                        readonly />
                                </div> --}}
                                {{-- <div class="mb-3"><label class="form-label">Sudah Lisensi Kepala Sekolah</label>
                                    <input type="text" class="form-control"
                                        value="{{ $guru->Sudah_Lisensi_Kepala_Sekolah ? 'Ya' : 'Tidak' }}" readonly />
                                </div> --}}
                                {{-- <div class="mb-3"><label class="form-label">Pernah Diklat Kepengawasan</label>
                                    <input type="text" class="form-control"
                                        value="{{ $guru->Pernah_Diklat_Kepengawasan ? 'Ya' : 'Tidak' }}" readonly />
                                </div> --}}
                                <div class="mb-3"><label class="form-label">Sumber Gaji</label>
                                    <input type="text" class="form-control" value="{{ $guru->Sumber_Gaji ?? '-' }}"
                                        readonly />
                                </div>
                                <div class="mb-3"><label class="form-label">Keahlian Braille</label>
                                    <input type="text" class="form-control"
                                        value="{{ $guru->Keahlian_Braille ? 'Ya' : 'Tidak' }}" readonly />
                                </div>
                                <div class="mb-3"><label class="form-label">Keahlian Bahasa Isyarat</label>
                                    <input type="text" class="form-control"
                                        value="{{ $guru->Keahlian_Bahasa_Isyarat ? 'Ya' : 'Tidak' }}" readonly />
                                </div>
                                <div class="mb-3"><label class="form-label">NPWP</label>
                                    <input type="text" class="form-control" value="{{ $guru->NPWP ?? '-' }}"
                                        readonly />
                                </div>
                                <div class="mb-3"><label class="form-label">Nama Wajib Pajak</label>
                                    <input type="text" class="form-control"
                                        value="{{ $guru->Nama_Wajib_Pajak ?? '-' }}" readonly />
                                </div>
                                <div class="mb-3"><label class="form-label">Bank</label>
                                    <input type="text" class="form-control" value="{{ $guru->Bank ?? '-' }}"
                                        readonly />
                                </div>
                                <div class="mb-3"><label class="form-label">Nomor Rekening Bank</label>
                                    <input type="text" class="form-control"
                                        value="{{ $guru->Nomor_Rekening_Bank ?? '-' }}" readonly />
                                </div>
                                <div class="mb-3"><label class="form-label">Rekening Atas Nama</label>
                                    <input type="text" class="form-control"
                                        value="{{ $guru->Rekening_Atas_Nama ?? '-' }}" readonly />
                                </div>
                                {{-- <div class="mb-3"><label class="form-label">Karpeg</label>
                                    <input type="text" class="form-control" value="{{ $guru->Karpeg ?? '-' }}"
                                        readonly />
                                </div> --}}
                                {{-- <div class="mb-3"><label class="form-label">Karis/Karsu</label>
                                    <input type="text" class="form-control" value="{{ $guru->Karis_Karsu ?? '-' }}"
                                        readonly />
                                </div> --}}
                                {{-- <div class="mb-3"><label class="form-label">NUKS</label>
                                    <input type="text" class="form-control" value="{{ $guru->NUKS ?? '-' }}"
                                        readonly />
                                </div> --}}
                            </div>
                        </div>

                        <div class="mt-3">
                            <a href="{{ route('admin.guru.index') }}" class="btn btn-secondary">Kembali</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
