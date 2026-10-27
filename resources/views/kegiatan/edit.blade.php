<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Edit Kegiatan</title>
    </head>
    <body>
        <h1>Edit Kegiatan</h1>
        <a href="{{ url('/kegiatan') }}">Kembali</a>
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

        <form method="POST" action="{{ url('/kegiatan/'.$item['id']) }}">
            @csrf 
            @method('PUT')

            <p>
                <label>Kode:</label>
                <input name="kode_kegiatan" maxlength="10" value="{{ old('kode_kegiatan',$item['kode_kegiatan']) }}" required>
            </p>

            <p>
                <label>Nama:</label>
                <input name="nama_kegiatan" value="{{ old('nama_kegiatan',$item['nama_kegiatan']) }}" required>
            </p>

            <p>
                <label>Email PIC:</label>
                <input type="email" name="email_pic" value="{{ old('email_pic',$item['email_pic']) }}" required>
            </p>

            <p>
                <label>Kuota:</label>
                <input type="number" name="kuota" min="10" max="200" value="{{ old('kuota',$item['kuota']) }}" required>
            </p>

            <p>
                <label>Jenis:</label> 
                <select name="jenis_kegiatan_id" required>
                    @foreach($jenisKegiatan as $j)
                    <option value="{{ $j['id'] }}" @selected((string)old('jenis_kegiatan_id',$item['jenis_kegiatan_id']) === (string)$j['id'])>{{ $j['nama_jenis'] }}</option>
                    @endforeach
                </select>
            </p>

            <button type="submit">Simpan Perubahan</button>
        </form>
    </body>
</html>
