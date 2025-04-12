<?php
session_start();
use LDAP\Result;

require '../../php/functions.php';


if (!isset($_SESSION["login"])) {
  header("location:../admin_login.php");
  exit;
}

$pasien = query("SELECT * FROM pasien");


//tombol cari
if (isset($_POST["cari"])) {
  $pasien = cari($_POST["keyword"]);
}
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
  <link rel="stylesheet" href="../../css/style.css" />
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
      <h1 class="text-2xl font-bold sidebar-text mt-6">Admin</h1>
      <nav class="">
        <div class="mb-2 mt-6">
          <div class="mb-4">
            <a href="dashboard.php"
              class="flex items-center gap-2 text-sm font-poppins p-2 hover:bg-gray-700 hover:bg-opacity-30 rounded">
              <i class="fas fa-chart-line"></i>
              <span class="sidebar-text -ml-1">Dashboard</span>
            </a>
          </div>

          <div
            class="flex items-center justify-between text-sm font-poppins cursor-pointer p-2 hover:bg-gray-700 hover:bg-opacity-30 rounded"
            onclick="toggleMenu('pasienMenu')">
            <div class="flex items-center gap-2">
              <i class="fas fa-user"></i>
              <span class="sidebar-text font-poppins">Pasien</span>
            </div>
            <i class="fas fa-chevron-down sidebar-text" id="icon-pasien"></i>
          </div>
          <div id="pasienMenu" class="ml-10 font-poppins text-xs space-y-2 hidden submenu">
            <a href="crud/pasien/registrasi.php" class="block cursor-pointer hover:text-gray-300">Registrasi</a>
            <a href="crud/pasien/manage.php" class="block cursor-pointer hover:text-gray-300">Manage Pasien</a>
          </div>
        </div>
        <div class="mb-2">
          <div
            class="flex items-center justify-between text-sm font-poppins cursor-pointer p-2 hover:bg-gray-700 hover:bg-opacity-30 rounded"
            onclick="toggleMenu('dokterMenu')">
            <div class="flex items-center gap-2">
              <i class="fas fa-user-md"></i>
              <span class="sidebar-text">Dokter</span>
            </div>
            <i class="fas fa-chevron-down sidebar-text" id="icon-dokter"></i>
          </div>
          <div id="dokterMenu" class="ml-10 font-poppins text-xs space-y-2 hidden submenu">
            <a href="crud/dokter/tambah.php" class="block cursor-pointer hover:text-gray-300">Tambah Dokter</a>
            <a href="crud/dokter/manage.php" class="block cursor-pointer hover:text-gray-300">Manage Dokter</a>
          </div>
        </div>
        <div class="mb-2">
          <div
            class="flex items-center justify-between text-sm font-poppins cursor-pointer p-2 hover:bg-gray-700 hover:bg-opacity-30 rounded"
            onclick="toggleMenu('obatMenu')">
            <div class="flex items-center gap-1">
              <i class="fas fa-pills"></i>
              <span class="sidebar-text">Obat</span>
            </div>
            <i class="fas fa-chevron-down sidebar-text" id="icon-obat"></i>
          </div>
          <div id="obatMenu" class="ml-10 text-xs font-poppins space-y-2 hidden submenu">
            <a href="crud/obat/tambah.php" class="block cursor-pointer hover:text-gray-300">Tambah Obat</a>
            <a href="crud/obat/manage.php" class="block cursor-pointer hover:text-gray-300">Manage Obat</a>
          </div>
        </div>
        <div class="mb-2">
          <div
            class="flex items-center justify-between text-sm font-poppins cursor-pointer p-2 hover:bg-gray-700 hover:bg-opacity-30 rounded"
            onclick="toggleMenu('rekamMedisMenu')">
            <div class="flex items-center gap-2">
              <i class="fas fa-clipboard-list"></i>
              <span class="sidebar-text">Rekam Medis</span>
            </div>
            <i class="fas fa-chevron-down sidebar-text" id="icon-rekamMedis"></i>
          </div>
          <div id="rekamMedisMenu" class="ml-10 text-xs font-poppins space-y-2 hidden submenu">
            <a href="crud/rekammedis/tambah.php" class="block cursor-pointer hover:text-gray-300">Tambah Rekam
              Medis</a>
            <a href="crud/rekammedis/manage.php" class="block cursor-pointer hover:text-gray-300">Manage Rekam
              Medis</a>
          </div>
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
          window.location.href = "../logout.php"; // Redirect otomatis setelah 1 detik
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
            <span id="dropdownButton" class="text-sm font-medium text-gray-700">Admin</span>

          </div>
          <!-- Dropdown menu -->
          <div id="dropdownMenu"
            class="hidden absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200">
            <div class="p-4 border-b">
              <p class="text-gray-800 font-semibold">Admin Panel</p>
              <p class="text-sm text-gray-500">Klinik Pradnya Usadha</p>
            </div>
            <a href="reset_pass_admin.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100">
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
        <h1 class="text-2xl font-bold">Dashboard</h1>
        <p class="text-gray-600">Laporan Kunjungan Rawat Jalan</p>

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
            <div class="flex items-center">
              <span class="fa-stack fa-2x">
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

        <div class="bg-white shadow-md rounded-lg p-4">

          <form action="" method="post" class="pb-2">
            <input type="text" name="keyword" size="30" placeholder="masukkan keywoard pencarian.." autocomplete="off"
              autofocus
              class="border-2 border-gray-600 rounded-md text-xs py-0.5 pl-1 placeholder:text-xs placeholder:pl-1">
            <button type="submit" name="cari" class="bg-gray-500 text-xs rounded-md px-1 py-1 text-white">cari</button>
          </form>


          <table class="w-full border-collapse border border-gray-300">
            <thead class="bg-gray-200">
              <tr class="text-xs">
                <th class="border p-2">No</th>
                <th class="border p-2">No Antrian</th>
                <th class="border p-2">Nama</th>
                <th class="border p-2">NIK</th>
                <th class="border p-2">Jenis Kelamin</th>
                <th class="border p-2">No HP</th>
                <th class="border p-2">Keluhan</th>
                <th class="border p-2">Poli Tujuan</th>
                <th class="border p-2">Tanggal Kunjungan</th>
                <th class="border p-2">Status Antrian</th>
                <th class="border p-2">Action</th>
              </tr>
            </thead>
            <tbody class="text-xs">


              <?php $i = 1; ?>
              <?php foreach ($pasien as $row)
              : ?>
                <tr>
                  <td class="border p-2 md"><?= $i; ?></td>
                  <td class="border p-2 w-8 md"><?= $row["no_antrian"]; ?></td>
                  <td class="border p-2 truncate w-20 md">
                    <?= $row["nama"]; ?>
                  </td>
                  <td class="border p-2 truncate w-20 md">
                    <?= strlen($row['nik']) > 10 ? substr($row['nik'], 0, 10) . '...' : $row["nik"]; ?>
                  </td>
                  <td class="border p-2 w-8 md"><?= $row["jenis_kelamin"]; ?></td>
                  <td class="border p-2 truncate w-20 md">
                    <?= strlen($row['no_hp']) > 10 ? substr($row['no_hp'], 0, 10) . '...' : $row["no_hp"]; ?>
                  </td>
                  <td class="border p-2 truncate  md">
                    <?= strlen($row['keluhan']) > 15 ? substr($row['keluhan'], 0, 15) . '...' : $row["keluhan"]; ?>
                  </td>
                  <td class="border p-2 truncate w-20 md"><?= $row["poli_tujuan"]; ?></td>
                  <td class="border p-2 md w-28"><?= $row["tanggal_kunjungan"]; ?></td>
                  <td class="border p-2 w-20 md"><?= $row["status_antrian"]; ?></td>
                  <td class="border p-2 space-x-1">

                    <button onclick="lihatPasien('<?= $row['id']; ?>')"
                      class="bg-gray-500 text-white px-2 py-1 rounded text-xs">
                      View
                    </button>

                    <form action="../../php/functions.php" method="POST" style="display: inline;">
                      <input type="hidden" name="id" value="<?= $row['id']; ?>">
                      <button type="submit" name="panggil" class="bg-blue-500 text-white px-2 py-1 rounded text-xs">
                        Panggil
                      </button>
                    </form>
                    <form action="../../php/functions.php" method="POST" style="display: inline;">
                      <input type="hidden" name="id" value="<?= $row['id']; ?>">
                      <button type="submit" name="selesai" class="bg-green-500 text-white px-2 py-1 rounded text-xs">
                        Selesai
                      </button>
                    </form>
                  </td>
                </tr>

                <?php $i++; ?>
              <?php endforeach; ?>

            </tbody>
          </table>
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




    <!-- Modal -->
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
          <button onclick="closeModal()" class="ml-auto text-gray-400 hover:text-red-600 text-2xl">&times;</button>
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
        fetch('view_pasien.php?id=' + id)
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
        <div><span class="font-semibold font-poppins">Keluhan:</span><br>${data.keluhan}</div>
        <div><span class="font-semibold font-poppins">Poli Tujuan:</span><br>${data.poli_tujuan}</div>
        <div><span class="font-semibold font-poppins">Tanggal Kunjungan:</span><br>${data.tanggal_kunjungan}</div>
        <div><span class="font-semibold font-poppins">Jenis Pasien:</span><br>${data.jenis_pasien}</div>
        <div><span class="font-semibold font-poppins">NIK/BPJS:</span><br>${data.nik_bpjs}</div>
    
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