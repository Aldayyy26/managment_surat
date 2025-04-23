<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat {{ $pengajuanSurat->template->judul }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; line-height: 1.4; }
        .container { width: 100%; max-width: 800px; margin: auto; padding: 10px; }
        .header { text-align: center; margin-bottom: 10px; }
        .header img { width: 80px; position: absolute; left: 10px; top: 10px; }
        .title { text-align: center; margin-bottom: 10px; }
        .content { text-align: justify; line-height: 1.6; margin-bottom: 20px; }
        .signature { text-align: right; margin-top: 30px; }
        .signature img { width: 150px; display: block; margin-bottom: 5px; }
        p { margin: 5px 0; }  /* Adjusted margin to reduce space between paragraphs */
    </style>
</head>
<body>
    <div class="container">
        <!-- Kop Surat -->
        <div class="header">
            <img src="{{ url('img/phb.png') }}" alt="Logo Instansi">
        </div>

        <!-- Judul Surat -->
        <p>Nomor: 06.03/TI.PHB/II/{{ now()->format('Y') }}</p>
        <p>Lampiran: {{ $pengajuanSurat->template->lampiran }}</p>
        <p>Hal: {{ $pengajuanSurat->template->perihal }}</p>
        <p>Kepada Yth,</p>
        <p>{{ $pengajuanSurat->template->kepada_yth }}</p>
        <p>{{ $konten['nama instansi'] ?? '-' }}</p>
        <p>Di Tempat</p>

        <!-- Isi Surat -->
        <div class="content">
            <p>{{ $pengajuanSurat->template->pembuka }}</p>
            <ul>
                @foreach(json_decode($pengajuanSurat->konten, true) as $key => $value)
                    <li><strong>{{ ucfirst($key) }}:</strong> {{ $value }}</li>
                @endforeach
            </ul>
            <p>{{ $pengajuanSurat->template->teks_atas }}</p>
            <p>{{ $pengajuanSurat->template->konten['nama judul'] ?? '-' }}</p>
            <p>{{ $pengajuanSurat->template->teks_bawah }}</p>
            <p>{{ $pengajuanSurat->template->penutup }}</p>
        </div>

        <!-- Tanda Tangan -->
        <div class="signature">
            <p>Kota Tegal, {{ now()->format('d F Y') }}</p>
            <p>Ka. Prodi S.Tr. Teknik Informatika</p>
            @if($pengajuanSurat->signature)
                <img src="{{ asset('storage/' . $pengajuanSurat->signature) }}" alt="Tanda Tangan">
            @else
                <p>No signature available.</p>
            @endif
            <p><u>{{ $kepalaProdi->name ?? 'Nama Pejabat' }}</u></p>
            <p>NIP: 123456789</p>
        </div>
    </div>
</body>
</html>
