<?php
session_start();
use LDAP\Result;

require '../../../../php/functions.php';

if (!isset($_SESSION["login"])) {
  header("location:../../../admin_login.php");
  exit;
}

//pagination table
$limit = 5;
$page = isset($_GET["halaman"]) ? (int) $_GET["halaman"] : 1;
$offset = ($page - 1) * $limit;

$HalamanAktif = $page;
$JumlahData = count(query("SELECT * FROM kunjungan"));
$JumlahHalaman = ceil($JumlahData / $limit);

$kunjungan = query("SELECT 
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
ORDER BY kunjungan.tanggal_kunjungan DESC
  LIMIT $limit OFFSET $offset;
");

// Ambil semua id_kunjungan yang tampil di halaman ini
$id_kunjungan_list = array_column($kunjungan, 'id');
$obat_per_kunjungan = [];

if (!empty($id_kunjungan_list)) {
  $id_kunjungan_in = implode(',', array_map('intval', $id_kunjungan_list));

  // Ambil data obat untuk semua kunjungan yang tampil (sekali query)
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

//tombol cari
if (isset($_POST["cari_rm"])) {
  $kunjungan = cari_rm($_POST["keyword"]);

  // Jika pencarian, ambil ulang id_kunjungan dan data obat
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

          <div
            class="flex items-center justify-between text-sm font-poppins cursor-pointer p-2 hover:bg-gray-700 hover:bg-opacity-30 rounded"
            onclick="toggleMenuPasien('pasienMenu', 'iconPasien')">
            <div class="flex items-center gap-2">
              <i class="fas fa-user"></i>
              <span class="sidebar-text font-poppins">Pasien</span>
            </div>
            <i class="fas fa-chevron-down sidebar-text transition-transform duration-300" id="iconPasien"></i>
          </div>

          <div id="pasienMenu"
            class="ml-10 font-poppins text-xs space-y-2 overflow-hidden transition-all duration-500 ease-in-out"
            style="max-height: 0; visibility: visible;">
            <a href="../pasien/registrasi.php" class="block cursor-pointer hover:text-gray-300">Registrasi</a>
            <a href="../pasien/manage.php" class="block cursor-pointer hover:text-gray-300">Manage Pasien</a>
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
          <div
            class="flex items-center justify-between text-sm font-poppins cursor-pointer p-2 hover:bg-gray-700 hover:bg-opacity-30 rounded"
            onclick="toggleMenuDokter('dokterMenu', 'iconDokter')">
            <div class="flex items-center gap-2">
              <i class="fas fa-user-md"></i>
              <span class="sidebar-text">Dokter</span>
            </div>
            <i class="fas fa-chevron-down sidebar-text transition-transform duration-300" id="iconDokter"></i>
          </div>
          <div id="dokterMenu"
            class="ml-10 font-poppins text-xs space-y-2 overflow-hidden transition-all duration-500 ease-in-out"
            style="max-height: 0; visibility: visible;">
            <a href="../dokter/tambah.php" class="block cursor-pointer hover:text-gray-300">Tambah Dokter</a>
            <a href="../dokter/manage.php" class="block cursor-pointer hover:text-gray-300">Manage Dokter</a>
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
        <div class="mb-2">
          <div
            class="flex items-center justify-between text-sm font-poppins cursor-pointer p-2 hover:bg-gray-700 hover:bg-opacity-30 rounded"
            onclick="toggleMenuObat('obatMenu', 'iconObat')">
            <div class="flex items-center gap-1">
              <i class="fas fa-pills"></i>
              <span class="sidebar-text">Obat</span>
            </div>
            <i class="fas fa-chevron-down sidebar-text transition-transform duration-300" id="iconObat"></i>
          </div>
          <div id="obatMenu"
            class="ml-10 text-xs font-poppins space-y-2 overflow-hidden transition-all duration-500 ease-in-out"
            style="max-height: 0; visibility: visible;">
            <a href="../obat/tambah.php" class="block cursor-pointer hover:text-gray-300">Tambah Obat</a>
            <a href="../obat/manage.php" class="block cursor-pointer hover:text-gray-300">Manage Obat</a>
          </div>

          <script>
            function toggleMenuObat(obatMenu, iconObat) {
              const menu = document.getElementById(obatMenu);
              const icon = document.getElementById(iconObat);

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
          <div
            class="flex items-center justify-between text-sm font-poppins cursor-pointer p-2 hover:bg-gray-700 hover:bg-opacity-30 rounded"
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
            <a href="../rekammedis/tambah.php" class="block cursor-pointer hover:text-gray-300">Tambah Rekam
              Medis</a>
            <a href="../rekammedis/manage.php" class="block cursor-pointer hover:text-gray-300">Manage Rekam
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
      <!-- Header -->
      <header class="bg-gray-100 p-4 shadow-md flex justify-between items-center sticky top-0">
        <div class="relative cursor-pointer ml-auto">
          <div class="flex items-center space-x-2">
            <!-- Ikon Profil Modern dan Teks Admin -->
            <i class="fas fa-user-circle text-gray-600 text-2xl"></i>
            <span id="dropdownButton" class="text-sm font-medium text-gray-700">Admin</span>

          </div>
          <!-- Dropdown menu -->
          <div id="dropdownMenu"
            class="hidden absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200">
            <div class="p-4 border-b">
              <p class="text-gray-800 font-semibold">Admin Panel</p>
              <p class="text-sm text-gray-500">Klinik Pradnya Usadha</p>
            </div>
            <a href="../../reset_pass_admin.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100">
              <i class="fas fa-lock text-gray-600 text-base pr-2"></i>
              Akun
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
        <h1 class="text-2xl font-bold">Rekam Medis</h1>
        <p class="text-gray-600">Manage Rekam Medis</p>

        <div class="bg-white shadow-md mt-4 rounded-lg p-4">
          <div class="flex justify-between items-center pb-2">
            <!-- Kolom kiri: Form pencarian -->
            <form action="" method="post" class="relative w-full max-w-xs">
              <input type="text" name="keyword" id="keyword" autocomplete="off" autofocus placeholder="Cari data..."
                class="w-full pl-8 pr-3 py-1.5 text-xs rounded-md border border-gray-300 focus:outline-none focus:ring-1 focus:ring-blue-400 focus:border-blue-400 transition placeholder-gray-400" />

              <!-- Ikon pencarian -->
              <div class="absolute inset-y-0 left-0 flex items-center pl-2 pointer-events-none">
                <svg class="w-3.5 h-3.5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                  viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M21 21l-4.35-4.35M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16z" />
                </svg>
              </div>
            </form>

            <!-- Kolom kanan: Tombol export -->
            <!-- Kolom kanan: Tombol export -->
            <button onclick="window.location.href='export_exel.php'"
              class="bg-[#18c161] hover:bg-[#1e623b] text-white text-xs py-2 px-3 rounded-md flex items-center gap-2 shadow-sm transition">
              <!-- Ikon Excel (customized for Excel brand) -->
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current text-white" viewBox="0 0 24 24">
                <path
                  d="M19 2H8a2 2 0 0 0-2 2v3H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h1v3a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2ZM8 4h11v16H8v-3h6a1 1 0 1 0 0-2H5V9h9a1 1 0 1 0 0-2H8V4Zm1.67 8.66 1.26 2.67a.75.75 0 0 1-1.34.68L9 13.34l-.59 1.26a.75.75 0 1 1-1.34-.68l1.26-2.67-1.26-2.66a.75.75 0 1 1 1.34-.68L9 10.66l.59-1.26a.75.75 0 0 1 1.34.68l-1.26 2.66Z" />
              </svg>
              <span class="font-medium">Download</span>
            </button>


          </div>
          <div id="container">


          </div>




        </div>
      </main>
    </div>

    <!--Logout-->
    <!--Script JS-->
    <script>
      const keyword = document.getElementById('keyword');
      const container = document.getElementById('container');

      function loadTable(page = 1) {
        const search = keyword?.value.trim() || '';
        container.innerHTML = '<div class="text-center p-4 text-gray-500">Memuat data...</div>';

        const xhr = new XMLHttpRequest();
        xhr.open('GET', `ajax/rm.php?keyword=${encodeURIComponent(search)}&halaman=${page}`, true);
        xhr.onreadystatechange = function () {
          if (xhr.readyState === 4 && xhr.status === 200) {
            container.innerHTML = xhr.responseText;
            setupPaginationEvents(); // Penting!
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
    <div id="modal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 hidden">
      <div class="bg-white rounded-xl shadow-2xl w-full max-w-xl p-6 sm:p-7">

        <!-- Header dengan Icon -->
        <div class="flex items-center mb-5 border-b pb-3">
          <!-- Icon Profil -->
          <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-3">
            <i class="fas fa-clipboard w-5 h-5 text-blue-600"></i>

          </div>
          <!-- Judul -->
          <h2 class="font-poppins text-xl font-semibold text-gray-800">Rekam Medis Pasien</h2>

          <!-- Tombol Tutup -->
          <button onclick="closeModal()" class="ml-auto text-gray-400 hover:text-red-600 text-2xl">&times;</button>
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
        fetch('view_pasien.php?id=' + id)
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

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
      function konfirmasiHapus(id) {
        Swal.fire({
          title: 'Yakin ingin menghapus data ini?',
          text: 'Data yang dihapus tidak dapat dikembalikan!',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Iya, hapus!',
          cancelButtonText: 'Batal'
        }).then((result) => {
          if (result.isConfirmed) {
            // Redirect ke delete.php dengan id
            window.location.href = 'delete.php?id=' + id;
          }
        });
      }
    </script>


</body>

</html>