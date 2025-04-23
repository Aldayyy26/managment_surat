<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat<p><strong>{{ $pengajuanSurat->template->judul }}</strong></p>
    </title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { width: 100%; max-width: 800px; margin: auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header img { width: 80px; position: absolute; left: 10px; top: 10px; }
        .title { text-align: center; margin-bottom: 20px; }
        .content { text-align: justify; line-height: 1.6; }
        .signature { text-align: right; margin-top: 40px; }
        .signature img { width: 150px; display: block; margin-bottom: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Kop Surat -->
        <div class="header">
            <img src="{{ asset('img/phb.png') }}" alt="Logo Instansi">
        </div>

        <!-- Judul Surat -->
        <p>Nomor: 06.03/TI.PHB/{{ now()->format('F') }}/{{ now()->format('Y') }}</p>
        <p>Lampiran:</p><p><strong>{{ $pengajuanSurat->template->lampiran }}</strong></p>
        <p>Hal:</p><p><strong>{{ $pengajuanSurat->template->perihal }}</strong></p>
        <p>Kepada Yth,</p>
            <p><strong>{{ $pengajuanSurat->template->kepada_yth }}</strong></p><p><strong>{{ $konten['nama instansi'] ?? '-' }}</strong></p>
            <p>Di Tempat</p>
        <!-- Isi Surat -->
        <div class="content">
            <p><strong>{{ $pengajuanSurat->template->pembuka }}</strong></p>
            </p><p><strong>{{ $konten['nama'] ?? '-' }}</strong></p>
            </p><p><strong>{{ $konten['nim'] ?? '-' }}</strong></p>
            </p><p><strong>{{ $konten['prodi'] ?? '-' }}</strong></p>
            <p><strong>{{ $pengajuanSurat->template->teks_atas }}</strong></p><p><strong>{{ $konten['nama judul'] ?? '-' }}</strong></p><p><strong>{{ $pengajuanSurat->template->teks_bawah }}</strong></p>
            <p><strong>{{ $pengajuanSurat->template->penutup }}</strong></p>
        </div>

        <!-- Tanda Tangan -->
        <div class="signature">
            <p>Kota XYZ, {{ now()->format('d F Y') }}</p>
            <p><strong>Ka. Prodi S.Tr. Teknik Informatika</strong></p>
            @if($pengajuanSurat->signature)
                <img src="{{ 'storage/' . $pengajuanSurat->signature }}" alt="Tanda Tangan">
            @else
                <p>No signature available.</p>
            @endif
            <p><u>{{ $kepalaProdi->name ?? 'Nama Pejabat' }}</u></p>
            <p>NIP: 123456789</p>
        </div>
    </div>
</body>
</html>
