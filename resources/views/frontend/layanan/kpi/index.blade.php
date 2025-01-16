<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kerja Praktek Industri</title>
    <link rel="stylesheet" href="{{ asset('css/layanan/kpi.css') }}">

    <script>
        let mahasiswaCount = 1;
        let mahasiswaMax = 4; // Maximum allowed students

        function addMahasiswa() {
            if (mahasiswaCount < mahasiswaMax) {
                mahasiswaCount++;
                const container = document.getElementById('mahasiswaContainer');

                const mahasiswaDiv = document.createElement('div');
                mahasiswaDiv.classList.add('mahasiswa-group');
                mahasiswaDiv.id = `mahasiswa${mahasiswaCount}`;
                
                mahasiswaDiv.innerHTML = `
                    <div class="form-group">
                        <label for="namaMahasiswa${mahasiswaCount}">Nama Mahasiswa(${mahasiswaCount}):</label>
                        <input type="text" id="namaMahasiswa${mahasiswaCount}" name="namaMahasiswa${mahasiswaCount}" required>
                    </div>
                    <div class="form-group">
                        <label for="nimMahasiswa${mahasiswaCount}">NIM Mahasiswa(${mahasiswaCount}):</label>
                        <input type="text" id="nimMahasiswa${mahasiswaCount}" name="nimMahasiswa${mahasiswaCount}" required>
                    </div>
                    <button type="button" class="remove-mahasiswa-btn" onclick="removeMahasiswa(${mahasiswaCount})">Hapus Mahasiswa</button>
                `;

                container.appendChild(mahasiswaDiv);
            } else {
                alert("Maaf, maksimal jumlah mahasiswa untuk KPI adalah 4.");
            }
        }

        function removeMahasiswa(id) {
            const mahasiswaDiv = document.getElementById(`mahasiswa${id}`);
            if (mahasiswaDiv) {
                mahasiswaDiv.remove();
                mahasiswaCount--;
            }
        }
    </script>
</head>
<body>
    <div class="sidebar">
        <div style="width: 216px; height: 129px; position: relative">
            <a href="{{ url('/') }}" style="left: 27px; top: 19px; position: absolute; color: black; font-size: 24px; font-family: Inter; font-weight: 600; text-decoration: none;">sura</a>
            <a href="{{ url('/') }}" style="left: 71px; top: 19px; position: absolute; color: #0d83fd; font-size: 24px; font-family: Inter; font-weight: 600; text-decoration: none;">TI</a>
            <ul style="position: absolute; top: 70px; left: 27px;">
                <li>
                    <a href="javascript:void(0)">
                        <img src="{{ asset('img/data.png') }}" alt="Icon" class="icon"> Lengkapi Data 
                    </a>
                </li>
                <li><a href="{{ url('unduh-cv') }}">
                    <img src="{{ asset('img/cvunduh.png') }}" alt="Icon" class="icon"> Unduh Surat
                </a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <h2>Kerja Praktek Industri</h2>
        <form>
            @csrf
            <div class="form-group">
                <label for="nama">Nama Perusahaan:</label>
                <input type="text" id="nama" name="nama" required>
            </div>
            <div class="form-group">
                <label for="alamatPerusahaan">Alamat Perusahaan:</label>
                <input type="text" id="alamatPerusahaan" name="alamatPerusahaan" required>
            </div>
            <div class="form-group">
                <label for="tanggalMulai">Tanggal Mulai:</label>
                <input type="date" id="tanggalMulai" name="tanggalMulai" required>
            </div>
            <div class="form-group">
                <label for="tanggalSelesai">Tanggal Selesai:</label>
                <input type="date" id="tanggalSelesai" name="tanggalSelesai" required>
            </div>

            <!-- Container for dynamic Mahasiswa input fields -->
            <div id="mahasiswaContainer">
                <div class="mahasiswa-group" id="mahasiswa1">
                    <div class="form-group">
                        <label for="namaMahasiswa1">Nama Mahasiswa(1):</label>
                        <input type="text" id="namaMahasiswa1" name="namaMahasiswa1" required>
                    </div>
                    <div class="form-group">
                        <label for="nimMahasiswa1">NIM Mahasiswa(1):</label>
                        <input type="text" id="nimMahasiswa1" name="nimMahasiswa1" required>
                    </div>
                </div>
            </div>
            
            <!-- Button to add more Mahasiswa fields -->
            <button type="button" class="add-mahasiswa-btn" onclick="addMahasiswa()">Tambah Mahasiswa</button>
            
            <div class="form-group button-group">
                <button type="submit">Save</button>
            </div>
        </form>    
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
    </div>
</body>
</html>
