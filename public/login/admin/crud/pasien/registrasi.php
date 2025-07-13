<?php
session_start();
use LDAP\Result;

require '../../../../php/functions.php';


if (!isset($_SESSION["login"])) {
  header("location:../../../admin_login.php");
  exit;
}

$username = $_SESSION["username"];
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
  <!-- Import Font Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../../../css/style.css" />
</head>

<body class="bg-gray-100">
  <div class="flex h-screen">
    <!--Loading Overlay-->

    <style>
      body,
      .swal2-popup {
        font-family: 'Poppins', sans-serif;
      }
    </style>


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
        <div class="mb-4">
          <a href="../../crud/obat/manage.php"
            class="flex items-center gap-2 text-sm font-poppins p-2 hover:bg-gray-700 hover:bg-opacity-30 rounded">
            <i class="fas fa-pills"></i>
            <span class="sidebar-text -ml-1">Obat</span>
          </a>
        </div>

        <div class="mb-2">
          <a href="../../crud/rekammedis/manage.php"
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
            <a href="../../reset_pass_admin.php" class="flex items-center px-4 py-2 text-gray-500 hover:bg-gray-100">
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
        <!--Form Daftar-->
        <div class="bg-gray-100">
          <div class="max-w-6xl mx-auto pb-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
              <!-- Informasi Pendaftaran -->
              <div class="font-poppins" data-aos="fade-up" data-aos-duration="2000">
                <h2 class="text-green-700 font-bold mt-14 md:mt-7 text-sm">
                  PENDAFTARAN PASIEN RAWAT JALAN
                </h2>
                <h1 class="text-xl md:text-2xl font-bold">
                  Lengkapi Data Dengan Benar
                </h1>
                <img src="../../../../img/pas.png" alt="" class="w-11/12 mt-3 -ml- hidden md:block" />
              </div>
              <!-- Form Pendaftaran -->
              <div class="bg-white p-6 md:h-max rounded-lg shadow-md md:mt-2 font-poppins" data-aos="fade-up"
                data-aos-duration="2000">
                <h2 class="text-md font-bold mb-4">Form Data Diri</h2>
                <form action="../../../../php/proses_registrasi.php" method="POST"
                  class="space-y-3 md:space-y-3 text-xs">
                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <label class="block text-gray-700">Nama Pasien</label>
                      <input type="text" name="nama" required class="w-full border border-gray-300 rounded-md p-2" />
                    </div>
                    <div>
                      <label class="block text-gray-700">No. KTP</label>
                      <input type="text" name="nik" required pattern="\d{16}"
                        class="w-full border border-gray-300 rounded-md p-2" />
                    </div>
                  </div>
                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <label class="block text-gray-700">Jenis Kelamin</label>
                      <div class="flex space-x-4 mt-2">
                        <label class="flex items-center text-xs">
                          <input type="radio" name="jenis_kelamin" value="Laki-laki" required class="mr-2" />
                          Laki-laki
                        </label>
                        <label class="flex items-center text-xs">
                          <input type="radio" name="jenis_kelamin" value="Perempuan" required class="mr-2" />
                          Perempuan
                        </label>
                      </div>
                    </div>
                    <div>
                      <label class="block text-gray-700">No. HP</label>
                      <input type="text" name="no_hp" required pattern="\d{10,13}"
                        class="w-full border border-gray-300 rounded-md p-2" />
                    </div>
                  </div>
                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <label class="block text-gray-700">Tempat Lahir</label>
                      <input type="text" name="tempat_lahir" required
                        class="w-full border border-gray-300 rounded-md p-2" />
                    </div>
                    <div>
                      <label class="block text-gray-700">Tanggal Lahir</label>
                      <input type="date" name="tanggal_lahir" required
                        class="w-full border border-gray-300 rounded-md p-2" />
                    </div>
                  </div>
                  <div>
                    <label class="block text-gray-700">Alamat</label>
                    <input type="text" name="alamat" required class="w-full border border-gray-300 rounded-md p-2" />
                  </div>
                  <div>
                    <label class="block text-gray-700">NIK / No. BPJS</label>
                    <input type="text" name="nik_bpjs" required placeholder="NIK001 / BPJS001"
                      class="w-full border border-gray-300 rounded-md p-2" />
                    <div class="mt-1 text-xs ml-2 opacity-50">
                      <li>Masukkan NIK jika pasien umum contoh: NIK001...</li>
                      <li>Masukkan No. BPJS jika kepesertaan BPJS contoh: BPJS001...</li>
                    </div>
                  </div>
                  <button type="submit" class="w-full hover:bg-green-900 bg-green-700 text-white p-2 rounded-md">
                    Daftar
                  </button>
                </form>


                <!-- Tombol untuk buka modal -->
                <div class="flex items-center space-x-2 mt-3 justify-center">
                  <p class="text-gray-700 font-poppins text-xs">Sudah pernah berobat?
                    <a onclick="toggleModal()" class="text-gray-700 font-bold font-poppins cursor-pointer text-xs">
                      Daftar sebagai Pasien Lama
                    </a>
                  </p>
                </div>

              </div>
            </div>
          </div>
        </div>
        <!--Daftar-->
      </main>
    </div>

    <!-- Modal -->
    <!-- Modal Cek Pasien -->
    <div id="modalCekPasien"
      class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden justify-center font-poppins items-center z-40">
      <div class="bg-white p-6 rounded-lg w-96 shadow-lg relative">
        <h2 class="text-xl font-semibold mb-4">Cek Pasien Lama</h2>
        <form id="formCekPasien" action="cek_pasien.php" method="post">

          <!-- Label dan Input No. RM -->
          <label for="no_rm_cari" class="block text-sm font-medium">Masukkan No. RM</label>
          <input type="text" name="no_rm_cari" id="no_rm_cari"
            class="w-full p-2 border text-xs border-gray-300 rounded mt-2 mb-4" placeholder="Contoh: RM001" required>

          <!-- Tombol -->
          <div class="flex justify-end gap-2 text-sm">
            <button type="button" onclick="toggleModal()" class="px-4 py-2 bg-gray-300 rounded">Batal</button>
            <button id="submitBtn" type="submit" name="cek_pasien"
              class="px-4 py-2 bg-green-700 hover:bg-green-900 text-white rounded">Cari</button>
          </div>
        </form>
      </div>
    </div>


    <!-- Loading Overlay -->
    <div id="loadingOverlayy"
      class="hidden fixed inset-0 z-[9999] bg-black bg-opacity-50 backdrop-blur-md flex flex-col items-center justify-center">
      <div class="flex flex-col items-center">
        <svg class="animate-spin h-10 w-10 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
          viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        <p class="text-white text-base mt-4 ">Mencari data pasien...</p>
      </div>
    </div>


    <script>
      function toggleModal() {
        const modal = document.getElementById("modalCekPasien");
        modal.classList.toggle("hidden");
        modal.classList.toggle("flex");
      }

      document.getElementById("formCekPasien").addEventListener("submit", function (e) {
        e.preventDefault(); // Stop pengiriman form sementara

        // Tampilkan overlay loading
        const loadingOverlay = document.getElementById("loadingOverlayy");
        if (loadingOverlay) {
          loadingOverlay.classList.remove("hidden");
        }

        // Nonaktifkan tombol submit
        const submitBtn = document.getElementById("submitBtn");
        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.classList.add("opacity-50", "cursor-not-allowed");
        }

        // Tunggu 2 detik, lalu kirim form
        setTimeout(() => {
          e.target.submit(); // submit form manual
        }, 2000);
      });

      // ✅ Tangani saat user kembali ke halaman (misalnya pakai tombol Back)
      window.addEventListener("pageshow", function () {
        const loadingOverlay = document.getElementById("loadingOverlayy");
        if (loadingOverlay) {
          loadingOverlay.classList.add("hidden");
        }

        const submitBtn = document.getElementById("submitBtn");
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.classList.remove("opacity-50", "cursor-not-allowed");
        }
      });
    </script>





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