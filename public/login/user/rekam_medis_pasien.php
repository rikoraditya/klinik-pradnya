<?php
session_start();
require '../../php/functions.php';

if (!isset($_SESSION["login_user"])) {
    header("location:../user_login.php");
    exit;
}

// Ambil no_hp dari session
$no_hp_login = $_SESSION['no_hp'];

// Ambil data pasien berdasarkan no_hp login
$pasien_data = query("SELECT nik FROM pasien WHERE no_hp = '$no_hp_login' LIMIT 1");
if (!$pasien_data) {
    die("Data pasien tidak ditemukan.");
}
$nik_login = $pasien_data[0]['nik'];

// Pagination
$JumlahDataPerHalaman = 5;
$JumlahData = count(query("
    SELECT kunjungan.id FROM kunjungan
    LEFT JOIN rekam_medis ON kunjungan.no_rm = rekam_medis.no_rm
    LEFT JOIN pasien ON rekam_medis.nik = pasien.nik
    WHERE pasien.no_hp = '$no_hp_login' AND pasien.nik = '$nik_login'
"));
$JumlahHalaman = ceil($JumlahData / $JumlahDataPerHalaman);
$HalamanAktif = (isset($_GET["halaman"])) ? $_GET["halaman"] : 1;
$AwalData = ($JumlahDataPerHalaman * $HalamanAktif) - $JumlahDataPerHalaman;

// Ambil data kunjungan berdasarkan no_hp dan nik login
$kunjungan = query("
    SELECT 
        kunjungan.*, 
        pasien.nik,
        pasien.nama AS nama_pasien, 
        pasien.jenis_kelamin,
        pasien.no_hp,
        pasien.tempat_lahir,
        pasien.tanggal_lahir,
        pasien.alamat,
        pasien.nik_bpjs,
        dokter.nama AS nama_dokter
    FROM kunjungan
    LEFT JOIN rekam_medis ON kunjungan.no_rm = rekam_medis.no_rm
    LEFT JOIN pasien ON rekam_medis.nik = pasien.nik
    LEFT JOIN dokter ON kunjungan.dokter_id = dokter.id
    WHERE pasien.no_hp = '$no_hp_login' AND pasien.nik = '$nik_login'
    ORDER BY kunjungan.tanggal_kunjungan DESC
    LIMIT $AwalData, $JumlahDataPerHalaman
");

// Ambil semua ID kunjungan yang muncul
$id_kunjungan_list = array_column($kunjungan, 'id');
$obat_per_kunjungan = [];

if (!empty($id_kunjungan_list)) {
    $id_kunjungan_in = implode(',', array_map('intval', $id_kunjungan_list));

    $obat_rows = query("
        SELECT ko.id_kunjungan, o.nama_obat, ko.dosis, ko.jumlah
        FROM kunjungan_obat ko
        JOIN obat o ON ko.kode_obat = o.kode_obat
        WHERE ko.id_kunjungan IN ($id_kunjungan_in)
        ORDER BY ko.id_kunjungan, o.nama_obat
    ");

    foreach ($obat_rows as $obat_row) {
        $idk = $obat_row['id_kunjungan'];
        if (!isset($obat_per_kunjungan[$idk]))
            $obat_per_kunjungan[$idk] = [];
        $obat_per_kunjungan[$idk][] = $obat_row;
    }
}

// Tombol cari berdasarkan no_rm
if (isset($_POST["cari_rm"])) {
    $keyword = htmlspecialchars($_POST["keyword"]);
    $kunjungan = query("
        SELECT 
            kunjungan.*, 
            pasien.nik,
            pasien.nama AS nama_pasien, 
            pasien.jenis_kelamin,
            pasien.no_hp,
            pasien.tempat_lahir,
            pasien.tanggal_lahir,
            pasien.alamat,
            pasien.nik_bpjs,
            dokter.nama AS nama_dokter
        FROM kunjungan
        LEFT JOIN rekam_medis ON kunjungan.no_rm = rekam_medis.no_rm
        LEFT JOIN pasien ON rekam_medis.nik = pasien.nik
        LEFT JOIN dokter ON kunjungan.dokter_id = dokter.id
        WHERE kunjungan.no_rm LIKE '%$keyword%' 
          AND pasien.no_hp = '$no_hp_login' 
          AND pasien.nik = '$nik_login'
        ORDER BY kunjungan.tanggal_kunjungan DESC
    ");

    $id_kunjungan_list = array_column($kunjungan, 'id');
    $obat_per_kunjungan = [];

    if (!empty($id_kunjungan_list)) {
        $id_kunjungan_in = implode(',', array_map('intval', $id_kunjungan_list));

        $obat_rows = query("
            SELECT ko.id_kunjungan, o.nama_obat, ko.dosis, ko.jumlah
            FROM kunjungan_obat ko
            JOIN obat o ON ko.kode_obat = o.kode_obat
            WHERE ko.id_kunjungan IN ($id_kunjungan_in)
            ORDER BY ko.id_kunjungan, o.nama_obat
        ");

        foreach ($obat_rows as $obat_row) {
            $idk = $obat_row['id_kunjungan'];
            if (!isset($obat_per_kunjungan[$idk]))
                $obat_per_kunjungan[$idk] = [];
            $obat_per_kunjungan[$idk][] = $obat_row;
        }
    }
}
?>




<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard Antrian</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../../css/style.css" />
</head>

<style>
    @keyframes fadeIn {
        0% {
            opacity: 0;
        }

        100% {
            opacity: 1;
        }
    }

    @keyframes pulse {

        0%,
        100% {
            transform: scale(1);
            opacity: 0.6;
        }

        50% {
            transform: scale(1.2);
            opacity: 1;
        }
    }

    .fade-in {
        animation: fadeIn 1s ease-in-out;
    }

    .dot {
        animation: pulse 1.5s infinite ease-in-out;
    }

    .dot:nth-child(2) {
        animation-delay: 0.2s;
    }

    .dot:nth-child(3) {
        animation-delay: 0.4s;
    }

    /* Menambahkan style untuk header */
</style>
</head>

<body class="bg-gray-100">
    <script>
        function showLoading(event) {
            event.preventDefault();
            document.getElementById("loadingOverlay").classList.remove("hidden");

            // Ambil URL dari data-href di button
            let targetURL = event.target.getAttribute("data-href");

            setTimeout(() => {
                window.location.href = targetURL;
            }, 1000);
        }
    </script>

    <div id="loadingOverlay"
        class="fixed z-50 inset-0 bg-white bg-opacity-80 backdrop-blur-md justify-center place-items-center ease-in-out flex hidden">
        <div class="flex space-x-2">
            <div class="w-3 h-3 bg-[#297A2C] rounded-full dot"></div>
            <div class="w-3 h-3 bg-[#297A2C] rounded-full dot"></div>
            <div class="w-3 h-3 bg-[#297A2C] rounded-full dot"></div>
        </div>
    </div>

    <!-- Mobile Header Toggle Button -->
    <nav class="md:hidden bg-green-900 text-white p-3 flex justify-between items-center fixed top-0 left-0 w-full z-40">
        <button id="mobileMenuButton" class="text-white text-xl">
            <i class="fas fa-bars"></i>
        </button>
    </nav>

    <div class="flex flex-col md:flex-row h-screen">
        <!-- Memberikan padding-top untuk memberi ruang header -->
        <!-- Sidebar -->
        <aside id="sidebar"
            class="bg-green-950 text-white w-64 md:w-64 z-40 p-5 space-y-6 h-full fixed top-0 left-0 transition-all duration-300 transform -translate-x-full md:translate-x-0">
            <button id="closeSidebar" class="absolute top-4 right-4 text-white text-xl transition-all md:hidden">
                <i class="fas fa-times"></i>
            </button>

            <h1 class="text-2xl font-bold sidebar-text font-poppins mt-6">
                Daftar Online
            </h1>
            <nav>
                <div class="mb-4">
                    <a href="buat_kunjungan.php"
                        class="flex items-center gap-2 text-sm font-poppins p-2 hover:bg-gray-700 hover:bg-opacity-30 rounded">
                        <i class="fas fa-book"></i>
                        <span class="sidebar-text">Buat Kunjungan</span>
                    </a>
                </div>
                <div class="mb-4">
                    <a href="kunjungan.php"
                        class="flex items-center gap-2 text-sm font-poppins p-2 hover:bg-gray-700 hover:bg-opacity-30 rounded">
                        <i class="fas fa-clock"></i>
                        <span class="sidebar-text">Antrian Anda</span>
                    </a>
                </div>
                <div class="mb-4">
                    <a href="rekam_medis_pasien.php"
                        class="flex items-center gap-2 text-sm font-poppins p-2 hover:bg-gray-700 hover:bg-opacity-30 rounded">
                        <i class="fas fa-book-open"></i>
                        <span class="sidebar-text">Riwayat Kunjungan</span>
                    </a>
                </div>
            </nav>
            <button onclick="openLogoutModal();" data-href="logout_pasien.php"
                class="flex items-center space-x-2 p-2 w-full font-poppins text-sm text-left hover:bg-red-600 rounded mt-6">
                <i class="fas fa-sign-out-alt"></i>
                <span class="sidebar-text">Logout</span>
            </button>
        </aside>
        <!--Logout PopUp-->
        <div id="logout-modal"
            class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black bg-opacity-50 hidden">
            <div class="bg-white p-6 rounded-lg font-poppins shadow-lg w-80 text-center">
                <h2 class="text-lg font-semibold">Konfirmasi Logout</h2>
                <p class="text-sm text-gray-600 my-4">
                    Apakah Anda yakin ingin logout?
                </p>
                <div class="flex justify-center space-x-4">
                    <button onclick="confirmLogout();" class="bg-red-600 text-white px-4 py-2 rounded">
                        Logout
                    </button>
                    <button onclick="closeLogoutModal();" class="bg-gray-300 px-4 py-2 rounded">
                        Batal
                    </button>
                </div>
            </div>
        </div>

        <div id="loading"
            class="fixed z-50 inset-0 bg-white bg-opacity-90 backdrop-blur-sm flex flex-col justify-center items-center hidden">
            <div class="loader"></div>
            <p class="mt-2 text-[#010101] font-poppins">Logout...</p>
        </div>

        <style>
            @keyframes spin {
                0% {
                    transform: rotate(0deg);
                }

                100% {
                    transform: rotate(360deg);
                }
            }

            .loader {
                width: 40px;
                height: 40px;
                border: 4px solid rgba(41, 122, 44, 0.3);
                border-top: 4px solid #297a2c;
                border-radius: 50%;
                animation: spin 1s linear infinite;
            }
        </style>
        <script>
            function confirmLogout() {
                event.preventDefault(); // Mencegah submit langsung

                // Tampilkan loading dan sembunyikan form
                document.getElementById("loading").classList.remove("hidden");

                setTimeout(() => {
                    window.location.href = "logout_pasien.php"; // Redirect otomatis setelah 1 detik
                }, 1000);
            }
        </script>

        <script>
            function openLogoutModal() {
                document.getElementById("logout-modal").classList.remove("hidden");
            }

            function closeLogoutModal() {
                document.getElementById("logout-modal").classList.add("hidden");
            }
        </script>
        <!--Logout-->
        <style>
            #sidebar-overlay {
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.5);
                backdrop-filter: blur(5px);
                z-index: 30;
                display: none;
            }
        </style>

        <div id="sidebar-overlay"></div>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const sidebar = document.getElementById("sidebar");
                const overlay = document.getElementById("sidebar-overlay");
                const openBtn = document.getElementById("mobileMenuButton");
                const closeBtn = document.getElementById("closeSidebar");

                function openSidebar() {
                    sidebar.classList.remove("-translate-x-full");
                    overlay.style.display = "block";
                    document.body.classList.add("overflow-hidden");
                }

                function closeSidebar() {
                    sidebar.classList.add("-translate-x-full");
                    overlay.style.display = "none";
                    document.body.classList.remove("overflow-hidden");
                }

                openBtn.addEventListener("click", openSidebar);
                closeBtn.addEventListener("click", closeSidebar);
                overlay.addEventListener("click", closeSidebar);
            });
        </script>

        <!-- Main Content -->
        <main class="flex-1 p-8 md:ml-64 mt-20 md:mt-0 transition-all duration-300 font-poppins" id="mainContent">
            <h1 class="text-2xl font-bold">Data</h1>
            <p class="text-gray-600">Riwayat Kunjungan</p>

            <div class="bg-white shadow-md mt-4 rounded-lg p-4">
                <div class="flex justify-between items-center pb-2">
                    <!-- Kolom kiri: Form pencarian -->
                    <p class="font-bold font-poppins text-sm">Rekam Medis Anda</p>




                </div>
                <div id="container">
                    <table class="w-full border-collapse border border-gray-300">
                        <thead class="bg-gray-200">
                            <tr class="text-xs">
                                <th class="border p-2">No</th>
                                <th class="border p-2">No RM</th>
                                <th class="border p-2">Nama</th>
                                <th class="border p-2">Tanggal Kunjungan</th>
                                <th class="border p-2">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs">
                            <?php if (empty($kunjungan)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-gray-500 text-sm">
                                        Anda belum memiliki rekam medis<br>
                                        Silakan datang ke klinik untuk berkonsultasi jika anda sudah melakukan pendaftaran.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $i = 1; ?>
                                <?php foreach ($kunjungan as $row): ?>
                                    <tr>
                                        <td class="border p-2"><?= $i; ?></td>
                                        <td class="border p-2"><?= $row["no_rm"]; ?></td>
                                        <td class="border p-2"><?= $row["nama_pasien"]; ?></td>
                                        <td class="border p-2"><?= $row["tanggal_kunjungan"]; ?></td>
                                        <td class="border p-2 w-40">
                                            <div class="flex justify-end">
                                                <button onclick="lihatPasien('<?= $row['id']; ?>')"
                                                    class="bg-gray-500 hover:bg-gray-600 text-white px-2 py-1 rounded text-xs">
                                                    View Rekam Medis
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php $i++; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>



                <?php if (!empty($kunjungan)): ?>
                    <div class="pagination text-xs font-poppins mt-2 ml-1 text-gray-500">
                        <?php if ($HalamanAktif > 1): ?>
                            <a href="?halaman=<?= $HalamanAktif - 1; ?>" class="text-base ">&laquo;</a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $JumlahHalaman; $i++): ?>
                            <?php if ($i == $HalamanAktif): ?>
                                <a href="?halaman=<?= $i; ?>" class="font-bold text-green-950"><?= $i; ?></a>
                            <?php else: ?>
                                <a href="?halaman=<?= $i; ?>"><?= $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($HalamanAktif < $JumlahHalaman): ?>
                            <a href="?halaman=<?= $HalamanAktif + 1; ?>" class="text-base ">&raquo;</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>


            </div>
        </main>
    </div>

    <!--Logout-->
    <script src="script.js"></script>

    <script>
        //delete alert
        // Fungsi untuk pasang event Swal ke tombol delete
        function bindDeleteButtons() {
            var deleteLinks = container.querySelectorAll('.delete-link');

            deleteLinks.forEach(function (link) {
                // Hapus event listener lama sebelum menambahkan yang baru
                var newLink = link.cloneNode(true);
                link.parentNode.replaceChild(newLink, link);

                newLink.addEventListener('click', function (e) {
                    e.preventDefault(); // Cegah langsung hapus

                    Swal.fire({
                        title: 'Yakin ingin hapus?',
                        text: 'Data yang dihapus tidak dapat dikembalikan!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Hapus',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = newLink.href;
                        }
                    });
                });
            });
        }

        // Jalankan sekali saat halaman awal
        bindDeleteButtons();


    </script>


    <script>
        function toggleSidebar() {
            let sidebar = document.getElementById("sidebar");
            let mainContent = document.getElementById("mainContent");
            let sidebarTexts = document.querySelectorAll(".sidebar-text");
            let submenus = document.querySelectorAll(".submenu");
            let dropdownIcons = document.querySelectorAll(".submenu i");

            if (sidebar.classList.contains("w-64")) {
                sidebar.classList.replace("w-64", "w-16");
                mainContent.classList.replace("ml-64", "ml-16");
                sidebarTexts.forEach((text) => text.classList.add("hidden"));
                submenus.forEach((submenu) => submenu.classList.add("hidden"));
                dropdownIcons.forEach((icon) => icon.classList.add("hidden"));
            } else {
                sidebar.classList.replace("w-16", "w-64");
                mainContent.classList.replace("ml-16", "ml-64");
                sidebarTexts.forEach((text) => text.classList.remove("hidden"));
            }
        }

        function toggleMenu(menuId) {
            let menu = document.getElementById(menuId);
            if (!document.getElementById("sidebar").classList.contains("w-64"))
                return;
            menu.classList.toggle("hidden");
        }
    </script>


    <!-- Modal -->
    <div id="modal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 hidden ">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-xl p-6 sm:p-7">

            <!-- Header dengan Icon -->
            <div class="flex items-center mb-5 border-b pb-3 ">
                <!-- Icon Profil -->
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-clipboard w-5 h-5 text-blue-600"></i>

                </div>
                <!-- Judul -->
                <h2 class="font-poppins text-xl font-semibold text-gray-800">Rekam Medis Pasien</h2>

                <!-- Tombol Tutup -->
                <button onclick="closeModal()"
                    class="ml-auto text-gray-400 hover:text-red-600 text-2xl">&times;</button>
            </div>

            <!-- Konten Data Pasien -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-3 text-sm text-gray-700" id="modalContent">
                <!-- Konten dinamis diisi via JS -->
            </div>

            <!-- Footer -->

        </div>
    </div>


    <script>
        function lihatPasien(id) {
            fetch('view_rm.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    console.log(data)
                    let obatHtml = "-";
                    if (data.obat_list && data.obat_list.length > 0) {
                        obatHtml = "";
                        data.obat_list.forEach(function (obat) {
                            obatHtml += `${obat.nama_obat} (${obat.dosis}, ${obat.jumlah})<br>`;
                        });
                    }

                    const modal = document.getElementById('modalContent');
                    modal.innerHTML = `
         <div><span class="font-semibold font-poppins">No. RM:</span><br>${data.no_rm}</div>
        <div><span class="font-semibold font-poppins">Nama:</span><br>${data.nama}</div>
        <div><span class="font-semibold font-poppins">NIK:</span><br>${data.nik}</div>
        <div><span class="font-semibold font-poppins">Jenis Kelamin:</span><br>${data.jenis_kelamin}</div>
        <div><span class="font-semibold font-poppins">No. HP:</span><br>${data.no_hp}</div>
        <div><span class="font-semibold font-poppins">Tempat Lahir:</span><br>${data.tempat_lahir}</div>
        <div><span class="font-semibold font-poppins">Tanggal Lahir:</span><br>${data.tanggal_lahir}</div>
        <div><span class="font-semibold font-poppins">Alamat:</span><br>${data.alamat}</div>
        <div><span class="font-semibold font-poppins">Tanggal Kunjungan:</span><br>${data.tanggal_kunjungan}</div>
        <div><span class="font-semibold font-poppins">Keluhan:</span><br>${data.keluhan}</div>
        <div><span class="font-semibold font-poppins">Poli Tujuan:</span><br>${data.poli_tujuan}</div>
          <div><span class="font-semibold font-poppins">Jenis Pasien:</span><br>${data.jenis_pasien}</div>
        <div><span class="font-semibold font-poppins">NIK/BPJS:</span><br>${data.nik_bpjs}</div>
        <div><span class="font-semibold font-poppins">Denyut Nadi:</span><br>${data.denyut_nadi}</div>
          <div><span class="font-semibold font-poppins">Laju Pernapasan:</span><br>${data.laju_pernapasan}</div>
        <div><span class="font-semibold font-poppins">Diagnosa:</span><br>${data.diagnosa}</div>

          <div><span class="font-semibold font-poppins">Obat:</span><br>${data.detail_obat}</div>
            <div><span class="font-semibold font-poppins">Dokter:</span><br>${data.nama_dokter}</div>

    
      `;


                    document.getElementById('modal').classList.remove('hidden');
                })
                .catch(error => {
                    document.getElementById('modalContent').innerHTML = `<p class="text-red-600 col-span-2">Gagal memuat data pasien.</p>`;
                    document.getElementById('modal').classList.remove('hidden');
                });
        }

        function closeModal() {
            document.getElementById('modal').classList.add('hidden');
        }

    </script>

</body>

</html>