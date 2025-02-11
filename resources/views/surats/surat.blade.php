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
            <img src="{{ public_path('logo.png') }}" alt="Logo Instansi">
            <h2>PEMERINTAHAN KOTA XYZ</h2>
            <h3>DINAS XYZ</h3>
            <p>Jl. Contoh No. 123, Kota XYZ, Telp: (021) 12345678</p>
            <div class="line"></div>
        </div>

        <!-- Judul Surat -->
        <div class="title">
            <h3><u>SURAT PERSETUJUAN</u></h3>
            <p>Nomor: 123/ABC/2024</p>
        </div>

        <!-- Isi Surat -->
        <div class="content">
            <p>Kepada Yth,</p>
            <p><strong>{{ $pengajuanSurat->user->name }}</strong></p>
            <p>Di Tempat</p>

            <p>Dengan ini kami menyetujui permohonan surat yang diajukan dengan rincian sebagai berikut:</p>

            <p><strong>Nama Surat:</strong> {{ $pengajuanSurat->template->judul }}</p>
            <p><strong>Tanggal Pengajuan:</strong> {{ $pengajuanSurat->created_at->format('d-m-Y') }}</p>

            <p>Demikian surat persetujuan ini dibuat untuk digunakan sebagaimana mestinya.</p>
        </div>

        <!-- Canvas untuk Tanda Tangan -->
        <div>
            <canvas id="signatureCanvas" width="300" height="150" style="border: 1px solid #000;"></canvas>
        </div>

        <!-- Tombol untuk kirim tanda tangan -->
        <div>
            <button id="submitSignature">Kirim Tanda Tangan</button>
        </div>

        <!-- Tanda Tangan -->
        <div class="signature">
            <p>Kota XYZ, {{ now()->format('d F Y') }}</p>
            <p><strong>Kepala Dinas XYZ</strong></p>
            @if($pengajuanSurat->signature)
                <img src="{{ asset('storage/' . $pengajuanSurat->signature) }}" alt="Tanda Tangan">
            @endif
            <p><u>Nama Pejabat</u></p>
            <p>NIP: 123456789</p>
        </div>
    </div>

    <script>
        // Inisialisasi canvas untuk tanda tangan
        const canvas = document.getElementById('signatureCanvas');
        const context = canvas.getContext('2d');
        let isDrawing = false;

        // Fungsi untuk mulai menggambar
        canvas.addEventListener('mousedown', (e) => {
            isDrawing = true;
            context.beginPath();
            context.moveTo(e.offsetX, e.offsetY);
        });

        // Fungsi untuk menggambar saat mouse bergerak
        canvas.addEventListener('mousemove', (e) => {
            if (isDrawing) {
                context.lineTo(e.offsetX, e.offsetY);
                context.stroke();
            }
        });

        // Fungsi untuk berhenti menggambar
        canvas.addEventListener('mouseup', () => {
            isDrawing = false;
        });

        // Kirim tanda tangan saat tombol diklik
        document.getElementById('submitSignature').addEventListener('click', () => {
            const signatureData = canvas.toDataURL(); // Mengambil data gambar base64

            const formData = new FormData();
            formData.append('signature', signatureData);

            fetch('/approve/{{ $pengajuanSurat->id }}/approve', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
            })
            .catch(error => console.error('Error:', error));
        });
    </script>
</body>
</html>
