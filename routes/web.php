<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

if (!function_exists('statusKuota')) {
    function statusKuota(int $kuota): string
    {
        return $kuota >= 75 ? 'Besar' : 'Terbatas';
    }
}

function tanganiKesalahanDatabase(Throwable $e)
{
    report($e);
    return back()->withInput()->with('error', 'Terjadi gangguan database. Silakan coba lagi.');
}

Route::redirect('/', '/kegiatan');

Route::get('/kegiatan', function (Request $request) {
    try {
        $pdo = DB::connection()->getPdo();
        $master = $pdo->prepare('SELECT id, nama_jenis FROM jenis_kegiatan ORDER BY nama_jenis');
        $master->execute();
        $jenisKegiatan = $master->fetchAll(PDO::FETCH_ASSOC);

        $q = trim((string) $request->query('q', ''));
        $jenis = $request->query('jenis_kegiatan_id', '');
        $sql = 'SELECT k.*, j.nama_jenis FROM kegiatan k JOIN jenis_kegiatan j ON j.id = k.jenis_kegiatan_id WHERE 1=1';
        $params = [];
        if ($q !== '') {
            $sql .= ' AND (k.kode_kegiatan LIKE :q1 OR k.nama_kegiatan LIKE :q2 OR k.email_pic LIKE :q3)';
            $params['q1'] = "%{$q}%";
            $params['q2'] = "%{$q}%";
            $params['q3'] = "%{$q}%";
        }
        if ($jenis !== '' && ctype_digit((string) $jenis)) {
            $sql .= ' AND k.jenis_kegiatan_id = :jenis';
            $params['jenis'] = (int) $jenis;
        } elseif ($jenis !== '') {
            $sql .= ' AND 1=0';
        }
        $sql .= ' ORDER BY k.id ASC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $kegiatan = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return view('kegiatan.index', compact('kegiatan', 'jenisKegiatan', 'q', 'jenis'));
    } catch (Throwable $e) {
        report($e);
        return response('Terjadi gangguan database. Silakan coba lagi.', 500);
    }
});

Route::post('/kegiatan', function (Request $request) {
    $request->merge([
        'kode_kegiatan' => trim(strip_tags((string) $request->input('kode_kegiatan'))),
        'nama_kegiatan' => trim(strip_tags((string) $request->input('nama_kegiatan'))),
        'email_pic' => trim(strip_tags((string) $request->input('email_pic'))),
    ]);
    $data = $request->validate([
        'kode_kegiatan' => ['required', 'regex:/^[A-Za-z0-9]{10}$/', 'unique:kegiatan,kode_kegiatan'],
        'nama_kegiatan' => 'required|string|min:3|max:100',
        'email_pic' => 'required|email|max:100|unique:kegiatan,email_pic',
        'kuota' => 'required|integer|between:10,200',
        'jenis_kegiatan_id' => 'required|integer|exists:jenis_kegiatan,id',
    ]);
    try {
        $pdo = DB::connection()->getPdo();
        $stmt = $pdo->prepare('INSERT INTO kegiatan (kode_kegiatan,nama_kegiatan,email_pic,kuota,jenis_kegiatan_id,created_at,updated_at) VALUES (:kode,:nama,:email,:kuota,:jenis,NOW(),NOW())');
        $stmt->execute(['kode'=>$data['kode_kegiatan'],'nama'=>$data['nama_kegiatan'],'email'=>$data['email_pic'],'kuota'=>$data['kuota'],'jenis'=>$data['jenis_kegiatan_id']]);
        return redirect('/kegiatan')->with('success', 'Kegiatan berhasil ditambahkan.');
    } catch (Throwable $e) {
        return tanganiKesalahanDatabase($e);
    }
});

Route::get('/kegiatan/{id}/edit', function (int $id) {
    try {
        $pdo = DB::connection()->getPdo();
        $stmt = $pdo->prepare('SELECT * FROM kegiatan WHERE id = :id');
        $stmt->execute(['id'=>$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        abort_if(!$item, 404);
        $stmt = $pdo->prepare('SELECT id,nama_jenis FROM jenis_kegiatan ORDER BY nama_jenis');
        $stmt->execute();
        $jenisKegiatan = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return view('kegiatan.edit', compact('item','jenisKegiatan'));
    } catch (Symfony\Component\HttpKernel\Exception\HttpException $e) {
        throw $e;
    } catch (Throwable $e) {
        report($e);
        return response('Terjadi gangguan database. Silakan coba lagi.', 500);
    }
})->whereNumber('id');

Route::put('/kegiatan/{id}', function (Request $request, int $id) {
    $request->merge([
        'kode_kegiatan' => trim(strip_tags((string) $request->input('kode_kegiatan'))),
        'nama_kegiatan' => trim(strip_tags((string) $request->input('nama_kegiatan'))),
        'email_pic' => trim(strip_tags((string) $request->input('email_pic'))),
    ]);
    $data = $request->validate([
        'kode_kegiatan' => ['required', 'regex:/^[A-Za-z0-9]{10}$/', Rule::unique('kegiatan','kode_kegiatan')->ignore($id)],
        'nama_kegiatan' => 'required|string|min:3|max:100',
        'email_pic' => ['required','email','max:100', Rule::unique('kegiatan','email_pic')->ignore($id)],
        'kuota' => 'required|integer|between:10,200',
        'jenis_kegiatan_id' => 'required|integer|exists:jenis_kegiatan,id',
    ]);
    try {
        $pdo = DB::connection()->getPdo();
        $stmt = $pdo->prepare('SELECT id FROM kegiatan WHERE id = :id');
        $stmt->execute(['id'=>$id]);
        abort_if(!$stmt->fetch(), 404);
        $stmt = $pdo->prepare('UPDATE kegiatan SET kode_kegiatan=:kode,nama_kegiatan=:nama,email_pic=:email,kuota=:kuota,jenis_kegiatan_id=:jenis,updated_at=NOW() WHERE id=:id');
        $stmt->execute(['kode'=>$data['kode_kegiatan'],'nama'=>$data['nama_kegiatan'],'email'=>$data['email_pic'],'kuota'=>$data['kuota'],'jenis'=>$data['jenis_kegiatan_id'],'id'=>$id]);
        return redirect('/kegiatan')->with('success','Kegiatan berhasil diperbarui.');
    } catch (Symfony\Component\HttpKernel\Exception\HttpException $e) {
        throw $e;
    } catch (Throwable $e) {
        return tanganiKesalahanDatabase($e);
    }
})->whereNumber('id');

Route::delete('/kegiatan/{id}', function (int $id) {
    try {
        $pdo = DB::connection()->getPdo();
        $stmt = $pdo->prepare('DELETE FROM kegiatan WHERE id = :id');
        $stmt->execute(['id'=>$id]);
        abort_if($stmt->rowCount() === 0, 404);
        return redirect('/kegiatan')->with('success','Kegiatan berhasil dihapus.');
    } catch (Symfony\Component\HttpKernel\Exception\HttpException $e) {
        throw $e;
    } catch (Throwable $e) {
        return tanganiKesalahanDatabase($e);
    }
})->whereNumber('id');
