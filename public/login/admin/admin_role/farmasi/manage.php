<?php
session_start();
use LDAP\Result;

require '../../../../php/functions.php';


if (!isset($_SESSION["login"])) {
  header("location:../../../admin_login.php");
  exit;
}

$username = $_SESSION["username"];

//pagination table
$limit = 5;
$page = isset($_GET["halaman"]) ? (int) $_GET["halaman"] : 1;
$offset = ($page - 1) * $limit;

$HalamanAktif = $page;
$JumlahData = count(query("SELECT * FROM obat"));
$JumlahHalaman = ceil($JumlahData / $limit);



$obat = query("SELECT * FROM obat LIMIT $limit OFFSET $offset;");



//tombol cari
if (isset($_POST["cari_obat"])) {
  $obat = cari_obat($_POST["keyword"]);
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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
      <h1 class="text-2xl font-bold font-poppins sidebar-text mt-6">Farmasi</h1>
      <nav class="">


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
            <a href="tambah.php" class="block cursor-pointer hover:text-gray-300">Tambah Obat</a>
            <a href="manage.php" class="block cursor-pointer hover:text-gray-300">Manage Obat</a>
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
            <a href="reset_pass_admin.php" class="flex items-center px-4 py-2 text-gray-500 hover:bg-gray-100">
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
        <h1 class="text-2xl font-bold">Data</h1>
        <p class="text-gray-600">Manage Obat</p>

        <div class="bg-white mt-4 shadow-md rounded-lg p-4">
          <form action="" method="post" class="relative w-full max-w-xs pb-2">
            <input type="text" name="keyword" id="keyword" autocomplete="off" autofocus placeholder="Cari data..."
              class="w-full pl-8 pr-3 py-1.5 text-xs rounded-md border border-gray-300 focus:outline-none focus:ring-1 focus:ring-blue-400 focus:border-blue-400 transition placeholder-gray-400" />

            <!-- Ikon pencarian -->
            <div class="absolute inset-y-0 left-0 flex items-center pl-2 pointer-events-none pb-2">
              <svg class="w-3.5 h-3.5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M21 21l-4.35-4.35M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16z" />
              </svg>
            </div>
          </form>

          <div id="container">

          </div>

      </main>
    </div>

    <!--Logout-->

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

    <!--Script JS-->
    <script>
      const keyword = document.getElementById('keyword');
      const container = document.getElementById('container');

      function loadTable(page = 1) {
        const search = keyword?.value.trim() || '';
        container.innerHTML = '<div class="text-center p-4 text-gray-500">Memuat data...</div>';

        const xhr = new XMLHttpRequest();
        xhr.open('GET', `ajax/obat.php?keyword=${encodeURIComponent(search)}&halaman=${page}`, true);
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