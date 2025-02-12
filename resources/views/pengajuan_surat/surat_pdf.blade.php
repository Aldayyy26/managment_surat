<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Edaran</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12pt; margin: 40px; }
        .container { width: 100%; max-width: 800px; margin: auto; padding: 20px; border: 1px solid #000; }
        .header { text-align: center; margin-bottom: 20px; }
        .header img { width: 80px; position: absolute; left: 10px; top: 10px; }
        .title { text-align: center; margin-bottom: 20px; }
        .content { text-align: justify; line-height: 1.6; }
        .signature { text-align: right; margin-top: 40px; }
        .signature img { width: 150px; display: block; margin-bottom: 5px; }
        .line { border-bottom: 2px solid #000; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ public_path('logo.png') }}" alt="Logo Instansi">
            <h2>{{ $pengajuanSurat->template->judul }}</h2>
            <p>Jl. Contoh No. 123, Kota XYZ, Telp: (021) 12345678</p>
            <div class="line"></div>
        </div>

        <div class="title">
            <h3><u>SURAT EDARAN</u></h3>
            <p>Nomor: {{ $pengajuanSurat->nomor_surat }}</p>
        </div>

        <div class="content">
            <p>Kepada Yth,</p>
            <p><strong>{{ $pengajuanSurat->user->name }}</strong></p>
            <p>Di Tempat</p>

            <p>Dengan ini kami menyampaikan informasi sebagai berikut:</p>
            <ul>
                @foreach(json_decode($pengajuanSurat->konten, true) as $key => $value)
                    <li><strong>{{ ucfirst($key) }}:</strong> {{ $value }}</li>
                @endforeach
            </ul>
        </div>

        <div class="signature">
            <p>Kota XYZ, {{ now()->format('d F Y') }}</p>
            <p><strong>Kepala Dinas XYZ</strong></p>
            @if($pengajuanSurat->signature)
                <img src="{{ 'storage/' . $pengajuanSurat->signature }}" alt="Tanda Tangan">
            @else
                <p>No signature available.</p>
            @endif
            <p><u>{{ $pengajuanSurat->user->name }}</u></p>
            <p>NIP: 123456789</p>
        </div>
    </div>
</body>
</html>
