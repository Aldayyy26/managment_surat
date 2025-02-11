<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Pengajuan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12pt; }
        .header { text-align: center; margin-bottom: 20px; }
        .content { margin: 20px; }
        .signature { margin-top: 40px; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>UNIVERSITAS XYZ</h2>
        <p>Jl. Contoh No. 123, Kota, Provinsi</p>
        <hr>
    </div>

    <div class="content">
        <p>Kepada Yth,</p>
        <p><strong>{{ $pengajuanSurat->template->judul }}</strong></p>
        <p>Dengan hormat,</p>
        <p>Bersama surat ini, saya mengajukan permohonan {{ strtolower($pengajuanSurat->template->judul) }} dengan rincian sebagai berikut:</p>

        <p><strong>Detail Pengajuan:</strong></p>
        <ul>
            @foreach(json_decode($pengajuanSurat->konten, true) as $key => $value)
                <li><strong>{{ ucfirst($key) }}:</strong> {{ $value }}</li>
            @endforeach
        </ul>

        <p>Demikian surat ini dibuat untuk diproses sebagaimana mestinya.</p>

        <div class="signature">
    <p>Hormat saya,</p>
    <br><br>
        @if ($pengajuanSurat->signature)
            <br><br>
            <img src="data:image/png;base64,{{ $pengajuanSurat->signature }}" alt="Signature" style="max-width: 200px;">
        @endif
        @if ($pengajuanSurat->user->hasRole('kepalaprodi'))
            <p>Hormat kami,</p>
            <br><br>
            <p>{{ $pengajuanSurat->user->name }}</p>
        @endif
    </div>

    </div>
</body>
</html>
