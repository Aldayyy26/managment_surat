<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat {{ $pengajuanSurat->template->judul }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #fff; line-height: 1.6; }
        .container { width: 100%; max-width: 800px; margin: auto; padding: 20px; background: white; }
        .header { text-align: center; margin-bottom: 20px; position: relative; }
        .header img { width: 80px; position: absolute; left: 10px; top: 10px; }
        .title { text-align: center; margin-bottom: 10px; }
        .content { margin-top: 20px; padding: 0 40px; text-align: justify; }
        .signature { text-align: right; margin-top: 40px; padding-right: 40px; }
        .signature img { width: 150px; display: block; margin-bottom: 5px; }
        p { margin: 6px 0; }
        .indent-middle { padding-left: 80px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Kop Surat -->
        <div class="header">
            <img src="{{ url('img/phb.png') }}" alt="Logo Instansi">
        </div>

        <!-- Header Surat -->
        <p>Nomor: 06.03/TI.PHB/II/{{ now()->format('Y') }}</p>
        <p>Lampiran: {{ $pengajuanSurat->template->lampiran }}</p>
        <p>Hal: {{ $pengajuanSurat->template->perihal }}</p>
        <p>Kepada Yth. :<strong> {{ $pengajuanSurat->template->kepada_yth }} {{ json_decode($pengajuanSurat->konten, true)['nama instansi'] ?? '' }}</strong></p>
        <p>Di Tempat</p>

        <!-- Isi Surat -->
        <div class="content">
            @php
                // Ambil konten JSON dan template isi surat
                $dataKonten = json_decode($pengajuanSurat->konten, true);
                $isiSurat = $pengajuanSurat->template->isi_surat;

                // Ganti setiap {{ field }} dengan data dari konten
                foreach ($dataKonten as $key => $value) {
                    $isiSurat = str_replace('{{ ' . $key . ' }}', $value, $isiSurat);
                }

                // Hilangkan placeholder yang belum terisi
                $isiSurat = preg_replace('/\{\{.*?\}\}/', '-', $isiSurat);
            @endphp

            {!! $isiSurat !!}
        </div>

        <!-- Penutup Surat -->
        <div class="content">
            <p>{{ $pengajuanSurat->template->penutup }}</p>
        </div>

        <!-- Tanda Tangan -->
        <div class="signature">
            <p>Kota Tegal, {{ now()->format('d F Y') }}</p>
            <p>Ka. Prodi S.Tr. Teknik Informatika</p>

            @if($pengajuanSurat->signature)
                <img src="{{ asset('storage/' . $pengajuanSurat->signature) }}" alt="Tanda Tangan">
            @else
                <p><em>Tanda tangan belum tersedia</em></p>
            @endif

            <p><u><strong>{{ $kepalaProdi->name ?? 'Nama Pejabat' }}</strong></u></p>
            <p>NIPY:</p>
        </div>
    </div>
</body>
</html>
