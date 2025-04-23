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
        .content {
            text-align: center; /* Centers the text horizontally */
            justify-content: center; /* Ensures content is centered in the container */
            display: flex;
            flex-direction: column; /* Ensures that the paragraphs are stacked vertically */
            align-items: center; /* Centers the content vertically */
            text-align: justify; /* Justifies the text */
        }
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
        <br>
        <!-- Judul Surat -->
        <p>Nomor: 06.03/TI.PHB/II/{{ now()->format('Y') }}</p>
        <p>Lampiran: {{ $pengajuanSurat->template->lampiran }}</p>
        <p>Hal: {{ $pengajuanSurat->template->perihal }}</p>
        <p>Kepada Yth,</p><p>{{ $pengajuanSurat->template->kepada_yth }}</p><p><strong></strong> {{ json_decode($pengajuanSurat->konten, true)['nama instansi'] }}</p>
        <p>Di Tempat</p>
        <br>
        <!-- Isi Surat -->
        <div class="content">
            <p>{{ $pengajuanSurat->template->pembuka }}</p>

            <!-- Isi Surat observasi -->
            <p>Nama: {{ json_decode($pengajuanSurat->konten, true)['nama'] }}</p>
            <p>Nim : {{ json_decode($pengajuanSurat->konten, true)['NIM'] }}</p>
            <p>Prodi : {{ json_decode($pengajuanSurat->konten, true)['prodi'] }}</p>
            <!-- Isi Surat observasi -->

            <p>{{ $pengajuanSurat->template->teks_atas }}</p><p><strong> {{ json_decode($pengajuanSurat->konten, true)['judul skripsi'] }}</strong></p><p>{{ $pengajuanSurat->template->teks_bawah }}</p>
            <br>
            <p>{{ $pengajuanSurat->template->penutup }}</p>
        </div>

        <!-- Tanda Tangan -->
        <div class="signature">
            <p>Kota Tegal, {{ now()->format('d F Y') }}</p>
            <p>Ka. Prodi S.Tr. Teknik Informatika</p>
            @if($pengajuanSurat->signature)
                <img src="{{ 'storage/' . $pengajuanSurat->signature }}" alt="Tanda Tangan">
            @else
                <p>No signature available.</p>
            @endif
            <p><u><strong>{{ $kepalaProdi->name ?? 'Nama Pejabat' }}</strong></u></p>
            <p>NIPY:</p>
        </div>
    </div>
</body>
</html>
