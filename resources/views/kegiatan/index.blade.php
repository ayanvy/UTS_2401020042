<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Data Kegiatan Mahasiswa</title>
    </head>
    <body>
        <h1>Sistem Data Kegiatan Mahasiswa</h1>
        @if(session('success'))
            <p>{{ session('success') }}</p>
        @endif
        
        @if(session('error'))
            <p>{{ session('error') }}</p>
        @endif

        @if($errors->any())
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <h2>Tambah Kegiatan</h2>

        <form method="POST" action="{{ url('/kegiatan') }}">
            @csrf

            <p>
                <label>Kode kegiatan:</label> 
                <input name="kode_kegiatan" maxlength="10" value="{{ old('kode_kegiatan') }}" required>
            </p>

            <p>
                <label>Nama kegiatan:</label> 
                <input name="nama_kegiatan" value="{{ old('nama_kegiatan') }}" required>
            </p>

            <p>
                <label>Email PIC:</label> 
                <input type="email" name="email_pic" value="{{ old('email_pic') }}" required>
            </p>

            <p>
                <label>Kuota:</label>
                <input type="number" name="kuota" min="10" max="200" value="{{ old('kuota') }}" required>
            </p>

            <p>
                <label>Jenis:</label> 
                <select name="jenis_kegiatan_id" required>
                    <option value="">-- Pilih jenis --</option>
                    @foreach($jenisKegiatan as $j)
                    <option value="{{ $j['id'] }}" @selected(old('jenis_kegiatan_id') == $j['id'])>{{ $j['nama_jenis'] }}</option>
                    @endforeach
                </select>
            </p>

            <button type="submit">Tambah</button></form>

            <h2>Cari dan Filter</h2>

            <form method="GET" action="{{ url('/kegiatan') }}">

            <label>Kata kunci:</label>
            <input name="q" value="{{ $q }}" placeholder="Kode, nama, email PIC">

            <label>Jenis:</label>
            <select name="jenis_kegiatan_id">
                <option value="">Semua jenis</option>
                @foreach($jenisKegiatan as $j)
                <option value="{{ $j['id'] }}" @selected((string)$jenis === (string)$j['id'])>{{ $j['nama_jenis'] }}</option>
                @endforeach
            </select>

            <button type="submit">Cari / Filter</button> <a href="{{ url('/kegiatan') }}">Reset</a>

        </form>

        <h2>Daftar Kegiatan</h2>

        <table border="1" cellpadding="6">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Email PIC</th>
                    <th>Kuota</th>
                    <th>Status Kuota</th>
                    <th>Jenis</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kegiatan as $k)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $k['kode_kegiatan'] }}</td>
                        <td>{{ $k['nama_kegiatan'] }}</td>
                        <td>{{ $k['email_pic'] }}</td>
                        <td>{{ $k['kuota'] }}</td>
                        <td>
                            @if(statusKuota((int)$k['kuota']) === 'Besar')
                                Besar
                            @else 
                                Terbatas
                            @endif
                        </td>
                        <td>{{ $k['nama_jenis'] }}</td>
                        <td><a href="{{ url('/kegiatan/'.$k['id'].'/edit') }}">Edit</a>
                            <form method="POST" action="{{ url('/kegiatan/'.$k['id']) }}">
                                @csrf 
                                @method('DELETE')
                                <button type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">Tidak ada data kegiatan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </body>
</html>
