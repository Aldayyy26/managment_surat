<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Persetujuan</title>
    <style>
        body { font-family: Arial, sans-serif; }
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
        <!-- Kop Surat -->
        <div class="header">
            <img src="{{ asset('img/phb.png') }}" alt="Logo Instansi">
        </div>


        <!-- Judul Surat -->
        <div class="title">
            <p>Nomor: 123/ABC/2024</p>
        </div>

        <!-- Isi Surat -->
        <div class="content">
            <p>Kepada Yth,</p> <p><strong>{{ $pengajuanSurat->template->kepada_yth }}</strong></p>
            <p>Di Tempat</p>

            <p>Dengan ini kami menyetujui permohonan surat yang diajukan dengan rincian sebagai berikut:</p>

            <p><strong>Nama Surat:</strong> {{ $pengajuanSurat->template->judul }}</p>
            <p><strong>Tanggal Pengajuan:</strong> {{ $pengajuanSurat->created_at->format('d-m-Y') }}</p>

            <p>Demikian surat persetujuan ini dibuat untuk digunakan sebagaimana mestinya.</p>
        </div>

        <!-- Tanda Tangan -->
        <div class="signature">
            <p>Kota XYZ, {{ now()->format('d F Y') }}</p>
            <p><strong>Kepala Dinas XYZ</strong></p>
            @if($pengajuanSurat->signature)
                <img src="{{ 'storage/' . $pengajuanSurat->signature }}" alt="Tanda Tangan">
            @else
                <p>No signature available.</p>
            @endif
            <p><u>Nama Pejabat</u></p>
            <p>NIP: 123456789</p>
        </div>
    </div>
</body>
</html>
