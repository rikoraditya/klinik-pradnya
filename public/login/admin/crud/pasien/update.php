<?php
session_start();
use LDAP\Result;

require '../../../../php/functions.php';


if (!isset($_SESSION["login"])) {
    header("location:../../../admin_login.php");
    exit;
}

$username = $_SESSION["username"];

$id = $_GET["id"];
$id = $_GET['id'];

$pasien = query("SELECT 
                  pasien.*,
                  antrian.no_antrian,
                  kunjungan.keluhan,
                  kunjungan.poli_tujuan,
                  pasien.jenis_kelamin,
                  kunjungan.tanggal_kunjungan,
                  kunjungan.jenis_pasien
                FROM pasien
                LEFT JOIN antrian ON antrian.pasien_id = pasien.id
                LEFT JOIN kunjungan ON kunjungan.id = pasien.id
                WHERE pasien.id = $id
                ORDER BY kunjungan.tanggal_kunjungan DESC
                LIMIT 1")[0];


echo "<!DOCTYPE html><html><head>
<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
</head><body>";



if (isset($_POST["submit"])) {
    $result = updatePasien($_POST);

    if ($result >= 0) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Data Berhasil Disimpan',
                text: '" . ($result > 0 ? "Perubahan telah dilakukan." : "Tidak ada data yang berubah.") . "',
                confirmButtonText: 'Kembali'
            }).then(() => {
                window.location.href = 'manage.php';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Data Gagal Diubah',
                confirmButtonText: 'Kembali'
            }).then(() => {
                window.location.href = 'manage.php';
            });
        </script>";
    }
}

echo "</body></html>";
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard Antrian</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
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
                    window.location.href = "admin.php";
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
            <h1 class="text-2xl font-bold font-poppins sidebar-text mt-6">Admin</h1>
            <nav class="">
                <div class="mb-2 mt-6">
                    <div class="mb-4">
                        <a href="../../dashboard.php"
                            class="flex items-center gap-2 text-sm font-poppins p-2 hover:bg-gray-700 hover:bg-opacity-30 rounded">
                            <i class="fas fa-chart-line"></i>
                            <span class="sidebar-text -ml-1">Dashboard</span>
                        </a>
                    </div>

                    <div class="flex items-center justify-between text-sm font-poppins cursor-pointer p-2 hover:bg-gray-700 hover:bg-opacity-30 rounded"
                        onclick="toggleMenuPasien('pasienMenu', 'iconPasien')">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-user"></i>
                            <span class="sidebar-text font-poppins">Pasien</span>
                        </div>
                        <i class="fas fa-chevron-down sidebar-text transition-transform duration-300"
                            id="iconPasien"></i>
                    </div>

                    <div id="pasienMenu"
                        class="ml-10 font-poppins text-xs space-y-2 overflow-hidden transition-all duration-500 ease-in-out"
                        style="max-height: 0; visibility: visible;">
                        <a href="../pasien/registrasi.php"
                            class="block cursor-pointer hover:text-gray-300">Registrasi</a>
                        <a href="../pasien/manage.php" class="block cursor-pointer hover:text-gray-300">Manage
                            Pasien</a>
                    </div>



                    <script>
                        function toggleMenuPasien(pasienMenu, iconPasien) {
                            const menu = document.getElementById(pasienMenu);
                            const icon = document.getElementById(iconPasien);

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
                <div class="mb-2">
                    <div class="flex items-center justify-between text-sm font-poppins cursor-pointer p-2 hover:bg-gray-700 hover:bg-opacity-30 rounded"
                        onclick="toggleMenuDokter('dokterMenu', 'iconDokter')">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-user-md"></i>
                            <span class="sidebar-text">Dokter</span>
                        </div>
                        <i class="fas fa-chevron-down sidebar-text transition-transform duration-300"
                            id="iconDokter"></i>
                    </div>
                    <div id="dokterMenu"
                        class="ml-10 font-poppins text-xs space-y-2 overflow-hidden transition-all duration-500 ease-in-out"
                        style="max-height: 0; visibility: visible;">
                        <a href="../dokter/tambah.php" class="block cursor-pointer hover:text-gray-300">Tambah
                            Dokter</a>
                        <a href="../dokter/manage.php" class="block cursor-pointer hover:text-gray-300">Manage
                            Dokter</a>
                    </div>

                    <script>
                        function toggleMenuDokter(dokterMenu, iconDokter) {
                            const menu = document.getElementById(dokterMenu);
                            const icon = document.getElementById(iconDokter);

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
                <div class="mb-4">
                    <a href="crud/obat/manage.php"
                        class="flex items-center gap-2 text-sm font-poppins p-2 hover:bg-gray-700 hover:bg-opacity-30 rounded">
                        <i class="fas fa-pills"></i>
                        <span class="sidebar-text -ml-1">Obat</span>
                    </a>
                </div>

                <div class="mb-2">
                    <a href="crud/rekammedis/manage.php"
                        class="flex items-center gap-2 text-sm font-poppins p-2 hover:bg-gray-700 hover:bg-opacity-30 rounded">
                        <i class="fas fa-clipboard-list"></i>
                        <span class="sidebar-text ml-1">Rekam Medis</span>
                    </a>
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
                        <a href="../../reset_pass_admin.php"
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

            <script>
                function tambahForm() {
                    let formAsli = document.getElementById("formRekamMedis");
                    let formBaru = formAsli.cloneNode(true);
                    formBaru.id = "";
                    formBaru.querySelectorAll("input, select").forEach(input => {
                        input.value = input.value;
                    });
                    document.getElementById("formContainer").appendChild(formBaru);
                }
            </script>

            <!-- Main Content -->
            <main class="flex-1 p-8 ml-64 transition-all duration-300 font-poppins" id="mainContent">
                <h1 class="text-2xl font-bold">Data</h1>
                <p class="text-gray-600">Update Data</p>
                <div class="max-w-7xl bg-white p-6 rounded-lg shadow-md mt-4">
                    <h4 class="text-lg font-semibold mb-4">Pasien</h4>
                    <div id="formContainer text-xs">



                        <form action="" method="POST" class="space-y-4 text-xs text-gray-600">
                            <input type="hidden" name="id" value="<?= $pasien['id']; ?>">

                            <div class="grid grid-cols-3 gap-4">

                                <div>
                                    <label class="block font-medium">Nama Pasien</label>
                                    <input type="text" name="nama" id="nama" class="w-full p-2 border rounded-md"
                                        value="<?= htmlspecialchars($pasien['nama'] ?? '') ?>" required>
                                </div>

                                <div>
                                    <label class="block font-medium">No. KTP</label>
                                    <input type="text" name="nik" id="nik" class="w-full p-2 border rounded-md"
                                        value="<?= htmlspecialchars($pasien['nik'] ?? '') ?>" required pattern="\d{16}">
                                </div>

                                <?php $jk = isset($pasien['jenis_kelamin']) ? $pasien['jenis_kelamin'] : ''; ?>
                                <div>
                                    <label class="block font-medium">Jenis Kelamin</label>
                                    <select name="jenis_kelamin" id="jenis_kelamin" class="w-full p-2 border rounded-md"
                                        required>

                                        <option value="Laki-laki" <?= $jk === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki
                                        </option>
                                        <option value="Perempuan" <?= $jk === 'Perempuan' ? 'selected' : '' ?>>Perempuan
                                        </option>
                                    </select>
                                </div>


                            </div>

                            <div class="grid grid-cols-3 gap-4">

                                <div>
                                    <label class="block font-medium">No. HP</label>
                                    <input type="text" name="no_hp" id="no_hp" class="w-full p-2 border rounded-md"
                                        value="<?= htmlspecialchars($pasien['no_hp'] ?? '') ?>" required
                                        pattern="\d{10,13}">
                                </div>

                                <div>
                                    <label class="block font-medium">Alamat</label>
                                    <input type="text" name="alamat" id="alamat" class="w-full p-2 border rounded-md"
                                        value="<?= htmlspecialchars($pasien['alamat'] ?? '') ?>" required>
                                </div>

                                <div>
                                    <label class="block font-medium">Tempat Lahir</label>
                                    <input type="text" name="tempat_lahir" id="tempat_lahir"
                                        class="w-full p-2 border rounded-md"
                                        value="<?= htmlspecialchars($pasien['tempat_lahir'] ?? '') ?>" required>
                                </div>

                            </div>

                            <div class="grid grid-cols-3 gap-4">

                                <div>
                                    <label class="block font-medium">Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir" id="tanggal_lahir"
                                        class="w-full p-2 border rounded-md"
                                        value="<?= htmlspecialchars($pasien['tanggal_lahir'] ?? '') ?>" required>
                                </div>

                                <div>
                                    <label class="block font-medium">NIK / No. BPJS</label>
                                    <input type="text" name="nik_bpjs" id="nik_bpjs"
                                        class="w-full p-2 border rounded-md"
                                        value="<?= htmlspecialchars($pasien['nik_bpjs'] ?? '') ?>" required>
                                </div>

                                <div></div> <!-- Kosong untuk kolom ketiga -->

                            </div>

                            <button type="submit" name="submit"
                                class="mt-4 bg-green-800 hover:bg-green-900 text-white py-2 px-3 rounded-md text-xs">Update</button>

                            <a href="manage.php"
                                class="bg-red-700 hover:bg-red-900 text-white py-2 px-3 rounded-md text-xs relative inline-block">
                                Kembali
                            </a>
                        </form>

                    </div>

                </div>

            </main>
        </div>

        <!--Logout-->

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
</body>

</html>