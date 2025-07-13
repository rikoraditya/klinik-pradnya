<?php
session_start();
require '../../../../php/functions.php';

if (!isset($_SESSION["login"])) {
    header("location:../../../admin_login.php");
    exit;
}

$username = $_SESSION["username"];

$poli_umum = query("SELECT COUNT(*) AS total FROM antrian WHERE poli_tujuan = 'Poli Umum'")[0]['total'];
$poli_gigi = query("SELECT COUNT(*) AS total FROM antrian WHERE poli_tujuan = 'Poli Gigi'")[0]['total'];

$limit = 5;
$page = isset($_GET["halaman"]) ? (int) $_GET["halaman"] : 1;
$offset = ($page - 1) * $limit;

$HalamanAktif = $page;
$JumlahData = count(query("SELECT * FROM antrian"));
$JumlahHalaman = ceil($JumlahData / $limit);


$antrian = query("
  SELECT 
    antrian.id,
    antrian.no_antrian,
    pasien.nama,
    pasien.jenis_kelamin,
    pasien.no_hp,
    pasien.nik,
    antrian.poli_tujuan,
    antrian.tanggal_antrian,
    antrian.status_antrian
  FROM antrian
  INNER JOIN pasien ON antrian.pasien_id = pasien.id
  ORDER BY antrian.id DESC
  LIMIT $limit OFFSET $offset;
");

?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard Antrian</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../../../../css/style.css" />
</head>

<body class="bg-gray-100">
    <div class="flex h-screen">
        <!--Loading Overlay-->
        <div id="loadingOverlay"
            class="fixed z-50 inset-0 bg-white bg-opacity-80 backdrop-blur-md justify-center place-items-center ease-in-out flex hidden">
            <div class="flex space-x-2">
                <div class="w-3 h-3 bg-[#297A2C] rounded-full dot"></div>
                <div class="w-3 h-3 bg-[#297A2C] rounded-full dot"></div>
                <div class="w-3 h-3 bg-[#297A2C] rounded-full dot"></div>
            </div>
        </div>

        <script>
            function showLoading(event) {
                event.preventDefault();
                document.getElementById("loadingOverlay").classList.remove("hidden");

                setTimeout(() => {
                    window.location.href = "admin.html";
                }, 2000);
            }
        </script>

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
        </style>

        <!-- Sidebar -->
        <aside id="sidebar"
            class="bg-green-950 text-white w-64 z-40 p-5 space-y-6 h-full fixed top-0 left-0 transition-all duration-300">
            <button id="toggleSidebar" class="absolute top-4 right-4 text-white text-xl transition-all"
                onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Dashboard -->
            <h1 class="text-2xl font-bold font-poppins sidebar-text mt-6">Dokter</h1>
            <nav class="">
                <div class="mb-2 mt-6">
                    <div class="mb-4">
                        <a href="dashboard.php"
                            class="flex items-center gap-2 text-sm font-poppins p-2 hover:bg-gray-700 hover:bg-opacity-30 rounded">
                            <i class="fas fa-chart-line"></i>
                            <span class="sidebar-text -ml-1">Dashboard</span>
                        </a>
                    </div>
                </div>


                <div class="mb-2">
                    <div class="flex items-center justify-between text-sm font-poppins cursor-pointer p-2 hover:bg-gray-700 hover:bg-opacity-30 rounded"
                        onclick="toggleMenuRM('rmMenu', 'iconRm')">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-clipboard-list"></i>
                            <span class="sidebar-text">Rekam Medis</span>
                        </div>
                        <i class="fas fa-chevron-down sidebar-text" id="iconRm"></i>
                    </div>
                    <div id="rmMenu"
                        class="ml-10 text-xs font-poppins space-y-2 overflow-hidden transition-all duration-500 ease-in-out"
                        style="max-height: 0; visibility: visible;">
                        <a href="../dokter/rm/tambah.php" class="block cursor-pointer hover:text-gray-300">Tambah
                            Rekam
                            Medis</a>
                        <a href="../dokter/rm/manage.php" class="block cursor-pointer hover:text-gray-300">Manage
                            Rekam
                            Medis</a>
                    </div>

                    <script>
                        function toggleMenuRM(rmMenu, iconRm) {
                            const menu = document.getElementById(rmMenu);
                            const icon = document.getElementById(iconRm);

                            if (menu.style.maxHeight && menu.style.maxHeight !== "0px") {
                                menu.style.maxHeight = "0px";
                                icon.classList.remove('rotate-180');
                            } else {
                                // Reset height dulu biar scrollHeight bisa dibaca
                                menu.style.maxHeight = "0px";
                                // Pakai timeout kecil biar animasi kebaca
                                setTimeout(() => {
                                    menu.style.maxHeight = menu.scrollHeight + "px";
                                }, 10);
                                icon.classList.add('rotate-180');
                            }
                        }
                    </script>

                </div>
            </nav>
            <button onclick="openLogoutModal();" data-href="../logout.php"
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
                    window.location.href = "../../../logout.php"; // Redirect otomatis setelah 1 detik
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

        <!-- Content -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <header class="bg-gray-100 p-4 shadow-md flex justify-between items-center sticky top-0">

                <div class="relative cursor-pointer ml-auto">
                    <div class="flex items-center space-x-2">
                        <!-- Ikon Profil Modern dan Teks Admin -->
                        <i class="fas fa-user-circle text-gray-600 text-2xl"></i>
                        <span id="dropdownButton" class="text-sm font-medium text-gray-700">
                            <?php echo htmlspecialchars($username); ?>
                        </span>


                    </div>
                    <!-- Dropdown menu -->
                    <div id="dropdownMenu"
                        class="hidden absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200">
                        <div class="p-4 border-b">
                            <p class="text-gray-800 font-semibold">Manage Akun</p>
                            <p class="text-sm text-gray-500">Klinik Pradnya Usadha</p>
                        </div>
                        <a href="reset_pass_admin.php"
                            class="flex items-center px-4 py-2 text-gray-500 hover:bg-gray-100">
                            <i class=" text-gray-600 text-sm"></i>
                            Pengaturan
                        </a>
                    </div>

            </header>

            <script>
                document.getElementById('dropdownButton').addEventListener('click', function () {
                    document.getElementById('dropdownMenu').classList.toggle('hidden');
                });
            </script>

            <script>
                function toggleSidebar() {
                    document
                        .getElementById("sidebar")
                        .classList.toggle("-translate-x-full");
                }
                function toggleDropdown(id) {
                    const dropdown = document.getElementById(id);
                    dropdown.classList.toggle("hidden");
                    dropdown.classList.toggle("h-auto");
                }
                lucide.createIcons();
            </script>
            <!--Akhir header-->

            <!-- Main Content -->
            <main class="flex-1 p-8 ml-64 transition-all duration-300 font-poppins" id="mainContent">
                <h1 class="text-2xl font-bold">Dashboard</h1>
                <p class="text-gray-600">Antrian Kunjungan Rawat Jalan</p>

                <div class="grid grid-cols-3 gap-6 my-4">
                    <div class="bg-green-800 text-white p-4 rounded-lg flex justify-between items-center">
                        <div class="flex items-center">
                            <span class="fa-stack fa-2x">
                                <i class="fas fa-circle fa-stack-2x" style="color: #ffffff"></i>
                                <!-- Warna lingkaran -->
                                <i class="fas fa-users fa-stack-1x" style="color: rgb(5, 86, 32)"></i>
                                <!-- Warna ikon -->
                            </span>
                            <span class="text-lg font-semibold">Poli Umum</span>
                        </div>
                        <div class="text-2xl font-bold"><?= $poli_umum; ?></div>
                    </div>
                    <div class="bg-blue-900 text-white p-4 rounded-lg flex justify-between items-center">
                        <div class="flex items-center z-0 ">
                            <span class="fa-stack fa-2x z-0">
                                <i class="fas fa-circle fa-stack-2x" style="color: #ff5733"></i>
                                <!-- Warna lingkaran -->
                                <i class="fas fa-tooth fa-stack-1x" style="color: white"></i>
                                <!-- Warna ikon -->
                            </span>
                            <span class="text-lg font-semibold">Poli Gigi</span>
                        </div>
                        <div class="text-2xl font-bold"><?= $poli_gigi; ?></div>
                    </div>
                </div>

                <div class="bg-white shadow-md rounded-lg p-4 space-y-4">
                    <!-- Bar atas: Pencarian + filter -->
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-2">
                        <!-- Kolom kiri: Form pencarian + tanggal + tampil semua -->
                        <div class="flex flex-col md:flex-row gap-2 w-full md:w-auto">
                            <!-- Pencarian -->
                            <div class="relative w-full md:w-64">
                                <input type="text" name="keyword" id="keyword" autocomplete="off" autofocus
                                    placeholder="Cari data..."
                                    class="w-full pl-8 pr-3 py-1.5 text-xs rounded-md border border-gray-300 focus:outline-none focus:ring-1 focus:ring-blue-400 focus:border-blue-400 transition placeholder-gray-400" />
                                <div class="absolute inset-y-0 left-0 flex items-center pl-2 pointer-events-none">
                                    <svg class="w-3.5 h-3.5 text-gray-500" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M21 21l-4.35-4.35M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16z" />
                                    </svg>
                                </div>
                            </div>

                            <!-- Filter tanggal -->
                            <input type="date" id="tanggalFilter" name="tanggal" value="<?= date('Y-m-d') ?>"
                                class="border border-gray-300 text-xs px-2 py-1 rounded" />

                            <!-- Switch tampil semua -->
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" id="tampilkanSemua" class="sr-only peer" />
                                <div
                                    class="w-10 h-5 bg-gray-300 rounded-full relative transition-colors duration-300 peer-checked:bg-green-500 before:content-[''] before:absolute before:top-0.5 before:left-0.5 before:w-4 before:h-4 before:bg-white before:rounded-full before:transition-all peer-checked:before:translate-x-5">
                                </div>
                                <span class="ml-3 text-xs text-gray-700">Tampilkan Semua</span>
                            </label>
                        </div>
                    </div>

                    <!-- Container hasil AJAX -->
                    <div id="container" class="w-full">
                        <!-- Data AJAX akan muncul di sini -->
                    </div>
                </div>


            </main>
        </div>

        <!--Script JS-->
        <script>
            const keyword = document.getElementById('keyword');
            const container = document.getElementById('container');

            function loadTable(page = 1) {
                const search = keyword?.value.trim() || '';
                const tanggalInput = document.getElementById('tanggalFilter');
                const tampilkanSemua = document.getElementById('tampilkanSemua')?.checked;

                const tanggal = tampilkanSemua ? '' : (tanggalInput?.value || '');

                container.innerHTML = '<div class="text-center p-4 text-gray-500">Memuat data...</div>';

                const xhr = new XMLHttpRequest();
                xhr.open(
                    'GET',
                    `../../../../js/ajax/pasien.php?keyword=${encodeURIComponent(search)}&tanggal=${encodeURIComponent(tanggal)}&halaman=${page}`,
                    true
                );
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        container.innerHTML = xhr.responseText;
                        setupPaginationEvents(); // Pasang ulang event pagination
                    }
                };
                xhr.send();
            }


            // Saat halaman dimuat, langsung ambil data awal
            window.addEventListener('DOMContentLoaded', () => {
                loadTable(1);
            });

            // Event pencarian otomatis
            keyword?.addEventListener('keyup', () => loadTable(1));
            // Event filter tanggal
            document.getElementById('tanggalFilter')?.addEventListener('change', () => loadTable(1));
            // Event tampilkan semua
            document.getElementById('tampilkanSemua')?.addEventListener('change', function () {
                if (this.checked) {
                    document.getElementById('tanggalFilter').value = '';
                } else {
                    document.getElementById('tanggalFilter').value = '<?= date('Y-m-d') ?>';
                }
                loadTable(1);
            });


            // Pasang ulang event tombol pagination setelah konten baru di-load
            function setupPaginationEvents() {
                const buttons = container.querySelectorAll('.pagination button[data-page]');
                buttons.forEach(button => {
                    button.addEventListener('click', function () {
                        const page = parseInt(this.dataset.page);
                        if (!isNaN(page)) {
                            loadTable(page);
                        }
                    });
                });
            }
        </script>


        <!--suara panggil-->
        <script>
            function panggilPasien(id, noAntrian, poli) {
                const pesan = `Nomor antrian. ${noAntrian.replace(/-/g, '').split('').join(' ')}. Silakan menuju ke ${poli}, Terima kasih`;
                const statusEl = document.getElementById('status-antrian-' + id);

                statusEl.innerHTML = `<div class="flex justify-center items-center h-full">
        <span class="w-5 h-5 border-4 border-gray-300 border-t-transparent rounded-full animate-spin"></span>
    </div>`;

                const data = new URLSearchParams();
                data.append('id', id);
                data.append('panggil', 'true');

                fetch('../../panggil.php', {
                    method: 'POST',
                    body: data
                })
                    .then(res => res.text())
                    .then(response => {
                        setTimeout(() => {
                            if (response.trim() === 'ok') {
                                statusEl.textContent = 'dipanggil';
                                statusEl.className = 'text-blue-600 border p-2';
                                window.speechSynthesis.cancel();
                                const ucap = new SpeechSynthesisUtterance(pesan);
                                ucap.lang = 'id-ID';
                                ucap.rate = 0.9;
                                window.speechSynthesis.speak(ucap);
                            } else {
                                statusEl.textContent = 'Gagal';
                                statusEl.className = 'text-red-600 border p-2';
                                alert('Gagal memanggil pasien. (' + response + ')');
                            }
                        }, 1500);
                    });
            }

            function selesaikanPasien(id) {
                const statusEl = document.getElementById('status-antrian-' + id);

                statusEl.innerHTML = `<div class="flex justify-center items-center h-full">
        <span class="w-5 h-5 border-4 border-gray-300 border-t-transparent rounded-full animate-spin"></span>
    </div>`;

                const data = new URLSearchParams();
                data.append('id', id);
                data.append('selesai', 'true');

                fetch('../../panggil.php', {
                    method: 'POST',
                    body: data
                })
                    .then(res => res.text())
                    .then(response => {
                        setTimeout(() => {
                            if (response.trim() === 'ok') {
                                statusEl.textContent = 'selesai';
                                statusEl.className = 'text-green-600 border p-2';
                            } else {
                                statusEl.textContent = 'Gagal';
                                statusEl.className = 'text-red-600 border p-2';
                                alert('Gagal menyelesaikan pasien. (' + response + ')');
                            }
                        }, 1500);
                    });
            }
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
        <div id="modal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 hidden">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-xl p-6 sm:p-7">

                <!-- Header dengan Icon -->
                <div class="flex items-center mb-5 border-b pb-3">
                    <!-- Icon Profil -->
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 12c2.7 0 4.9-2.2 4.9-4.9S14.7 2.2 12 2.2 7.1 4.4 7.1 7.1 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.9V22h19.2v-2.7c0-3.3-6.4-4.9-9.6-4.9z" />
                        </svg>
                    </div>
                    <!-- Judul -->
                    <h2 class="font-poppins text-xl font-semibold text-gray-800">Data Pasien</h2>

                    <!-- Tombol Tutup -->
                    <button onclick="closeModal()"
                        class="ml-auto text-gray-400 hover:text-red-600 text-2xl">&times;</button>
                </div>

                <!-- Konten Data Pasien -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-3 text-sm text-gray-700" id="modalContent">
                    <!-- Konten dinamis diisi via JS -->
                </div>

                <!-- Footer -->
                <hr class="flex justify-end mt-6 pt-3 border-t">
                <div class="mt-3 font-poppins p-3 bg-green-800 text-white text-center rounded-xl shadow-md">
                    <h3 class="text-sm font-semibold">Nomor Antrian Pasien</h3>
                    <p class="text-2xl font-bold mt-2" id="nomorAntrian"></p>

                </div>
            </div>
        </div>

        <script>
            function lihatPasien(id) {
                fetch('../../view_pasien.php?id=' + id)
                    .then(response => response.json())
                    .then(data => {
                        const modal = document.getElementById('modalContent');
                        modal.innerHTML = `
     
        <div><span class="font-semibold font-poppins">Nama:</span><br>${data.nama}</div>
        <div><span class="font-semibold font-poppins">NIK:</span><br>${data.nik}</div>
        <div><span class="font-semibold font-poppins">Jenis Kelamin:</span><br>${data.jenis_kelamin}</div>
        <div><span class="font-semibold font-poppins">No. HP:</span><br>${data.no_hp}</div>
        <div><span class="font-semibold font-poppins">Tempat Lahir:</span><br>${data.tempat_lahir}</div>
        <div><span class="font-semibold font-poppins">Tanggal Lahir:</span><br>${data.tanggal_lahir}</div>
        <div><span class="font-semibold font-poppins">Alamat:</span><br>${data.alamat}</div>
        <div><span class="font-semibold font-poppins">Poli Tujuan:</span><br>${data.poli_tujuan}</div>
        <div><span class="font-semibold font-poppins">Tanggal Kunjungan:</span><br>${data.tanggal_antrian}</div>
    
      `;
                        document.getElementById('nomorAntrian').textContent = data.no_antrian;

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