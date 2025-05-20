<?php
session_start();
use LDAP\Result;

require '../../php/functions.php';

// Set zona waktu lokal agar date('Y-m-d') sesuai
date_default_timezone_set('Asia/Makassar');

if (!isset($_SESSION["login_user"])) {
  header("location:../user_login.php");
  exit;
}

// Fungsi normalisasi no HP
function normalize_hp($no_hp)
{
  $no_hp = preg_replace('/[^0-9]/', '', $no_hp);
  if (substr($no_hp, 0, 1) === '0') {
    $no_hp = '62' . substr($no_hp, 1);
  }
  return $no_hp;
}

// Cek apakah user sudah login
if (!isset($_SESSION['no_hp'])) {
  echo "Anda belum login.";
  exit;
}

$no_hp = normalize_hp($_SESSION['no_hp']);
$row = null;
$notif = null;
$today = date('Y-m-d'); // hasil akan sesuai zona waktu lokal

// Cek koneksi database
if (!$conn) {
  die("Koneksi database gagal: " . mysqli_connect_error());
}

// Query ke tabel antrian dan join ke pasien
$stmt = mysqli_prepare($conn, "
  SELECT 
    pasien.nama, pasien.nik, pasien.no_hp, pasien.jenis_kelamin, pasien.tanggal_lahir, pasien.alamat, 
    antrian.tanggal_antrian, antrian.poli_tujuan, antrian.status_antrian, antrian.no_antrian
  FROM antrian
  JOIN pasien ON antrian.pasien_id = pasien.id
  WHERE pasien.no_hp = ? AND DATE(antrian.tanggal_antrian) = ?
  ORDER BY antrian.id DESC
  LIMIT 1
");

if ($stmt) {
  mysqli_stmt_bind_param($stmt, 'ss', $no_hp, $today);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);

  if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
  } else {
    $notif = "Anda belum melakukan pendaftaran hari ini.";
  }

  mysqli_stmt_close($stmt);
} else {
  $notif = "Gagal menyiapkan statement SQL.";
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
    <main class="flex-1 p-4 transition-all md:ml-48 duration-300" id="mainContent">
      <!--Antrian-->
      <div class="bg-gray-100">
        <div class="max-w-6xl mx-auto pb-10">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Informasi Pendaftaran -->
            <div class="font-poppins hidden md:block" data-aos="fade-up" data-aos-duration="2000">
              <h2 class="text-green-700 font-bold md:mt-7 text-sm">
                PENDAFTARAN PASIEN RAWAT JALAN
              </h2>
              <h1 class="text-xl md:text-2xl font-bold">
                Diharapkan Menuju Klinik Setelah <br />
                Melakukan Pendaftaran
              </h1>
              <img class="w-11/12" src="../../img/pas.png" alt="" />
            </div>
            <!-- Form Pendaftaran -->
            <div class="md:mt-3 mt-10 ">



              <?php if ($notif): ?>
                <style>
                  @keyframes fadeInUp {
                    from {
                      opacity: 0;
                      transform: translateY(20px);
                    }

                    to {
                      opacity: 1;
                      transform: translateY(0);
                    }
                  }

                  .animate-fade-in {
                    animation: fadeInUp 0.6s ease-out forwards;
                  }
                </style>

                <div class="relative z-10 max-w-2xl mx-auto mt-12 px-6 pb-4">
                  <!-- Background kuning kedip -->
                  <div class="absolute inset-0 bg-yellow-100/80 blur-xl opacity-80 rounded-3xl animate-pulse"></div>

                  <!-- Notification Card -->
                  <div
                    class="relative backdrop-blur-md bg-yellow-50/90 border border-yellow-300 text-yellow-900 p-6 rounded-3xl shadow-2xl flex items-start space-x-4 animate-fade-in">

                    <!-- Icon medical -->
                    <div class="flex-shrink-0 mt-1">
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-yellow-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-3-3v6m0-10a9 9 0 100 18 9 9 0 000-18z" />
                      </svg>
                    </div>

                    <!-- Isi konten notifikasi -->
                    <div class="flex-1">
                      <h3 class="font-bold text-xl mb-1">🔔 Klinik Pradnya Usadha</h3>
                      <p class="text-base leading-relaxed"><?= htmlspecialchars($notif); ?></p>

                      <!-- Tombol CTA -->
                      <div class="mt-4">
                        <a href="buat_kunjungan.php"
                          class="inline-flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white px-5 py-2 rounded-xl font-semibold shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-200">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                          </svg>
                          Daftar Sekarang
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endif; ?>





              <?php if ($row): ?>
                <style>
                  @keyframes fadeInUp {
                    from {
                      opacity: 0;
                      transform: translateY(20px);
                    }

                    to {
                      opacity: 1;
                      transform: translateY(0);
                    }
                  }

                  .animate-fade-in {
                    animation: fadeInUp 0.6s ease-out forwards;
                  }
                </style>

                <div class="relative z-10 max-w-3xl mx-auto mt-8  md:mt-0 px-3 pb-6 font-poppins">
                  <!-- Background lembut -->
                  <div class="absolute inset-0 bg-green-100/40 blur-xl opacity-60 rounded-3xl animate-fade-in"></div>

                  <!-- Data Card -->
                  <div
                    class="relative backdrop-blur-md bg-white/80 border border-blue-200 p-6 rounded-2xl shadow-2xl animate-fade-in space-y-5">
                    <h2 class="text-xl font-bold text-green-900 flex items-center gap-2">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5.121 17.804A9 9 0 1118.88 6.196 9 9 0 015.121 17.804z" />
                      </svg>
                      Pendaftaran Anda
                    </h2>

                    <!-- Grid Data -->
                    <div class="space-y-4 text-sm text-gray-800">
                      <?php
                      $dataPasien = [
                        'Nama Pasien' => $row["nama"],
                        'No. KTP' => $row["nik"],
                        'Jenis Kelamin' => $row["jenis_kelamin"],
                        'No. HP' => $row["no_hp"],
                        'Tanggal Lahir' => $row["tanggal_lahir"],
                        'Alamat' => $row["alamat"],
                        'Tanggal Kunjungan' => $row["tanggal_antrian"],
                        'Poli Tujuan' => $row["poli_tujuan"],
                        'Status Antrian' => $row["status_antrian"],
                      ];

                      foreach ($dataPasien as $label => $value):
                        // Khusus status antrian, tambahkan class warna
                        $valueClass = '';
                        if ($label === 'Status Antrian') {
                          if ($value === 'dipanggil') {
                            $valueClass = 'text-blue-600';
                          } elseif ($value === 'selesai') {
                            $valueClass = 'text-green-600';
                          }
                        }
                        ?>
                        <div class="grid grid-cols-3 gap-2 items-start border-b pb-2">
                          <span class="text-green-800 font-semibold col-span-1"><?= $label; ?></span>
                          <span class="col-span-2 <?= $valueClass ?>"><?= htmlspecialchars($value); ?></span>
                        </div>
                      <?php endforeach; ?>

                    </div>

                    <!-- Antrian -->
                    <div class="mt-6 p-4 bg-green-700 text-white text-center rounded-2xl shadow-lg">
                      <h3 class="text-sm font-semibold tracking-wide">Nomor Antrian Anda</h3>
                      <p class="text-3xl font-bold mt-2"><?= $row["no_antrian"]; ?></p>
                    </div>
                  </div>
                </div>
              <?php endif; ?>

            </div>
          </div>
        </div>


        <!--Antrian-->
    </main>
  </div>

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