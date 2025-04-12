<?php
session_start();
use LDAP\Result;

require '../../php/functions.php';


if (!isset($_SESSION["login_user"])) {
  header("location:../user_login.php");
  exit;
}


$rekam_medis = [];
$pasien = [];


// Cek apakah ada data pasien di session
if (isset($_SESSION['pasien_lama'])) {
  $pasien = $_SESSION['pasien_lama'];
  $rekam_medis = $_SESSION['pasien_lama'];
} else {
  // Redirect jika data tidak ada di session
  header("Location: registrasi.php");
  exit();
}


?>


<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard Antrian</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
  <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
  <link rel="stylesheet" href="../../css/style.css" />
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
            <span class="sidebar-text">Kunjungan Anda</span>
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
    <main class="flex-1 p-8 ml-64 transition-all duration-300 font-poppins" id="mainContent">

      <div class="bg-gray-100">
        <div class="max-w-6xl mx-auto pb-10">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Informasi Pendaftaran -->
            <div class="font-poppins" data-aos="fade-up" data-aos-duration="2000">
              <h2 class="text-green-700 font-bold mt-14 md:mt-7 text-sm">
                PENDAFTARAN PASIEN RAWAT JALAN
              </h2>
              <h1 class="text-xl md:text-2xl font-bold">
                Lakukan Pendaftaran Untuk <br />
                Keluarga Tercinta Anda
              </h1>
              <img src="../../img/pendaftaran.PNG" alt="" class="w-11/12 mt-3 -ml- hidden md:block" />
            </div>
            <!-- Form Pendaftaran -->
            <div class="bg-white p-6 md:h-max rounded-lg shadow-md md:mt-2 font-poppins">
              <h2 class="text-md font-bold mb-4">Pendaftaran Pasien Lama</h2>
              <form action="../../php/proses.php" method="POST" class="space-y-3 md:space-y-2 text-xs">
                <div class="grid grid-cols-1 gap-4">
                  <div>
                    <label class="block text-gray-700">No. RM</label>
                    <input type="text" name="no_rm" required
                      class="w-full border border-gray-300 rounded-md p-2 text-gray-400"
                      value="<?= $rekam_medis['no_rm'] ?? '' ?>" readonly />
                  </div>

                </div>
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-gray-700">Nama Pasien</label>
                    <input type="text" name="nama" required
                      class="w-full border border-gray-300 rounded-md p-2 text-gray-400"
                      value="<?= $pasien['nama'] ?? '' ?>" readonly />
                  </div>
                  <div>
                    <label class="block text-gray-700">No. KTP</label>
                    <input type="text" name="nik" required pattern="\d{16}"
                      class="w-full border border-gray-300 rounded-md p-2 text-gray-400"
                      value="<?= $pasien['nik'] ?? '' ?>" readonly />
                  </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block  font-medium">Jenis Kelamin</label>
                    <input type="text" name="jenis_kelamin" class="w-full p-2 border rounded-md text-gray-400"
                      value="<?= $pasien['jenis_kelamin'] ?? '' ?>" readonly>
                  </div>
                  <div>
                    <label class="block text-gray-700">No. HP</label>
                    <input type="text" name="no_hp" required pattern="\d{10,13}"
                      class="w-full border border-gray-300 rounded-md p-2 text-gray-400"
                      value="<?= $pasien['no_hp'] ?? '' ?>" readonly />
                  </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-gray-700">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" required
                      class="w-full border border-gray-300 rounded-md p-2 text-gray-400"
                      value="<?= $pasien['tempat_lahir'] ?? '' ?>" readonly />
                  </div>
                  <div>
                    <label class="block text-gray-700">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" required
                      class="w-full border border-gray-300 rounded-md p-2 text-gray-400"
                      value="<?= $pasien['tanggal_lahir'] ?? '' ?>" readonly />
                  </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-gray-700">Alamat</label>
                    <input type="text" name="alamat" required
                      class="w-full border border-gray-300 rounded-md p-2 text-gray-400"
                      value="<?= $pasien['alamat'] ?? '' ?>" readonly />
                  </div>
                  <div>
                    <label class="block text-gray-700">Tanggal Kunjungan</label>
                    <input type="date" name="tanggal_kunjungan" required
                      class="w-full border border-gray-300 rounded-md p-2" />
                  </div>
                </div>
                <div>
                  <label class="block text-gray-700">Keluhan</label>
                  <textarea name="keluhan" required class="w-full border border-gray-300 rounded-md p-2"></textarea>
                </div>
                <div>
                  <label class="block text-gray-700">Poli Tujuan</label>
                  <select name="poli_tujuan" required class="w-full border border-gray-300 rounded-md p-2">
                    <option value="">...</option>
                    <option value="Poli Umum">Poli Umum</option>
                    <option value="Poli Gigi">Poli Gigi</option>
                  </select>
                </div>
                <div>
                  <label class="block text-gray-700">Jenis Pasien</label>
                  <select name="jenis_pasien" required class="w-full border border-gray-300 rounded-md p-2">
                    <option value="">...</option>
                    <option value="Umum">Umum</option>
                    <option value="BPJS">BPJS</option>
                  </select>
                </div>
                <div>
                  <label class="block text-gray-700">NIK / No. BPJS</label>
                  <input type="text" name="nik_bpjs" required placeholder="Masukkan NIK / No. BPJS"
                    class="w-full border border-gray-300 rounded-md p-2" />
                  <div class="mt-1 text-xs ml-2 opacity-50">
                    <li>Masukkan NIK Jika Pasien Umum</li>
                    <li>Masukkan No. BPJS Jika Kepesertaan BPJS</li>
                  </div>
                </div>
                <button type="submit" class="w-full hover:bg-green-900 bg-green-700 text-white p-2 rounded-md">
                  Daftar
                </button>
              </form>



            </div>
          </div>
        </div>
      </div>
    </main>
  </div>


  <!-- Modal Cek Pasien -->
  <div id="modalCekPasien"
    class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden justify-center font-poppins items-center z-40">
    <div class="bg-white p-6 rounded-lg w-96 shadow-lg relative">
      <h2 class="text-xl font-semibold mb-4">Pasien Lama</h2>
      <form id="formCekPasien" action="cek_pasien.php" method="post">
        <label for="nik" class="block text-sm font-medium">Masukkan NIK</label>
        <input type="text" name="nik_cari" class="w-full p-2 border text-xs border-gray-300 rounded mt-2 mb-4" required>
        <div class="flex justify-end gap-2 text-sm">
          <button type="button" onclick="toggleModal()" class="px-4 py-2 bg-gray-300 rounded">Batal</button>
          <button id="submitBtn" type="submit" name="cek_pasien"
            class="px-4 py-2 bg-green-700 hover:bg-green-900 text-white rounded">Cari</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Overlay Loading Blur -->
  <div id="loadingOverlayy"
    class="hidden fixed inset-0 backdrop-blur-sm bg-black bg-opacity-40 z-50 flex flex-col items-center justify-center">
    <svg class="animate-spin h-8 w-8 z-50 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
      viewBox="0 0 24 24">
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
    </svg>
    <p class="text-white text-sm mt-2">Mencari data pasien...</p>
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
      document.getElementById("loadingOverlayy").classList.remove("hidden");

      // Nonaktifkan tombol submit
      const submitBtn = document.getElementById("submitBtn");
      submitBtn.disabled = true;
      submitBtn.classList.add("opacity-50", "cursor-not-allowed");

      // Tunggu 2 detik, lalu kirim form
      setTimeout(() => {
        e.target.submit(); // submit form manual
      }, 2000);
    });
  </script>

  <script>
    document
      .getElementById("mobileMenuButton")
      .addEventListener("click", function () {
        let sidebar = document.getElementById("sidebar");
        sidebar.classList.toggle("-translate-x-full");
      });

    document
      .getElementById("closeSidebar")
      .addEventListener("click", function () {
        let sidebar = document.getElementById("sidebar");
        sidebar.classList.add("-translate-x-full");
      });

    // Pastikan halaman di-scroll ke atas setelah konten dimuat
    document.addEventListener("DOMContentLoaded", function () {
      window.scrollTo(0, 0);
    });
  </script>

  <!--AOS Animate scrool-->
  <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
  <script>
    AOS.init();
    AOS.init();

    // You can also pass an optional settings object
    // below listed default settings
    AOS.init({
      // Global settings:
      disable: false, // accepts following values: 'phone', 'tablet', 'mobile', boolean, expression or function
      startEvent: "DOMContentLoaded", // name of the event dispatched on the document, that AOS should initialize on
      initClassName: "aos-init", // class applied after initialization
      animatedClassName: "aos-animate", // class applied on animation
      useClassNames: false, // if true, will add content of `data-aos` as classes on scroll
      disableMutationObserver: false, // disables automatic mutations' detections (advanced)
      debounceDelay: 50, // the delay on debounce used while resizing window (advanced)
      throttleDelay: 99, // the delay on throttle used while scrolling the page (advanced)

      // Settings that can be overridden on per-element basis, by `data-aos-*` attributes:
      offset: 120, // offset (in px) from the original trigger point
      delay: 0, // values from 0 to 3000, with step 50ms
      duration: 400, // values from 0 to 3000, with step 50ms
      easing: "ease", // default easing for AOS animations
      once: false, // whether animation should happen only once - while scrolling down
      mirror: false, // whether elements should animate out while scrolling past them
      anchorPlacement: "top-bottom", // defines which position of the element regarding to window should trigger the animation
    });
  </script>
</body>

</html>