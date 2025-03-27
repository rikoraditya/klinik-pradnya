<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard Antrian</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <link
      rel="stylesheet"
      href="https://unpkg.com/swiper/swiper-bundle.min.css"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap"
      rel="stylesheet"
    />
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

    <div
      id="loadingOverlay"
      class="fixed z-50 inset-0 bg-white bg-opacity-80 backdrop-blur-md justify-center place-items-center ease-in-out flex hidden"
    >
      <div class="flex space-x-2">
        <div class="w-3 h-3 bg-[#297A2C] rounded-full dot"></div>
        <div class="w-3 h-3 bg-[#297A2C] rounded-full dot"></div>
        <div class="w-3 h-3 bg-[#297A2C] rounded-full dot"></div>
      </div>
    </div>

    <!-- Mobile Header Toggle Button -->
    <nav
      class="md:hidden bg-green-900 text-white p-3 flex justify-between items-center fixed top-0 left-0 w-full z-40"
    >
      <button id="mobileMenuButton" class="text-white text-xl">
        <i class="fas fa-bars"></i>
      </button>
    </nav>

    <div class="flex flex-col md:flex-row h-screen">
      <!-- Memberikan padding-top untuk memberi ruang header -->
      <!-- Sidebar -->
      <aside
        id="sidebar"
        class="bg-green-950 text-white w-64 md:w-64 z-40 p-5 space-y-6 h-full fixed top-0 left-0 transition-all duration-300 transform -translate-x-full md:translate-x-0"
      >
        <button
          id="closeSidebar"
          class="absolute top-4 right-4 text-white text-xl transition-all md:hidden"
        >
          <i class="fas fa-times"></i>
        </button>

        <h1 class="text-2xl font-bold sidebar-text font-poppins mt-6">
          Daftar Online
        </h1>
        <nav>
          <div class="mb-4">
            <a
              href="buat_kunjungan.html"
              class="flex items-center gap-2 text-sm font-poppins p-2 hover:bg-gray-700 hover:bg-opacity-30 rounded"
            >
              <i class="fas fa-book"></i>
              <span class="sidebar-text">Buat Kunjungan</span>
            </a>
          </div>
          <div class="mb-4">
            <a
              href="kunjungan.html"
              class="flex items-center gap-2 text-sm font-poppins p-2 hover:bg-gray-700 hover:bg-opacity-30 rounded"
            >
              <i class="fas fa-clock"></i>
              <span class="sidebar-text">Kunjungan Anda</span>
            </a>
          </div>
        </nav>
        <button
          onclick="openLogoutModal();"
          data-href="../user_login.html"
          class="flex items-center space-x-2 p-2 w-full font-poppins text-sm text-left hover:bg-red-600 rounded mt-6"
        >
          <i class="fas fa-sign-out-alt"></i>
          <span class="sidebar-text">Logout</span>
        </button>
      </aside>
      <!--Logout PopUp-->
      <div
        id="logout-modal"
        class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black bg-opacity-50 hidden"
      >
        <div
          class="bg-white p-6 rounded-lg font-poppins shadow-lg w-80 text-center"
        >
          <h2 class="text-lg font-semibold">Konfirmasi Logout</h2>
          <p class="text-sm text-gray-600 my-4">
            Apakah Anda yakin ingin logout?
          </p>
          <div class="flex justify-center space-x-4">
            <button
              onclick="confirmLogout();"
              class="bg-red-600 text-white px-4 py-2 rounded"
            >
              Logout
            </button>
            <button
              onclick="closeLogoutModal();"
              class="bg-gray-300 px-4 py-2 rounded"
            >
              Batal
            </button>
          </div>
        </div>
      </div>

      <div
        id="loading"
        class="fixed z-50 inset-0 bg-white bg-opacity-90 backdrop-blur-sm flex flex-col justify-center items-center hidden"
      >
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
            window.location.href = "../user_login.html"; // Redirect otomatis setelah 1 detik
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
      <main
        class="flex-1 p-4 transition-all md:ml-48 duration-300"
        id="mainContent"
      >
        <!--Antrian-->
        <div class="bg-gray-100">
          <div class="max-w-6xl mx-auto pb-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
              <!-- Informasi Pendaftaran -->
              <div
                class="font-poppins hidden md:block"
                data-aos="fade-up"
                data-aos-duration="2000"
              >
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
              <div
                class="max-w-lg w-full md:mt-3 mt-14 bg-white p-5 rounded-2xl text-xs shadow-xl"
              >
                <h2
                  class="text-lg font-bold font-poppins text-gray-800 text-center mb-6"
                >
                  Pendaftaran Anda
                </h2>
                <div class="space-y-4 font-poppins">
                  <div class="grid grid-cols-2 gap-2 border-b pb-2">
                    <span class="text-gray-600 font-extrabold"
                      >Nama Pasien</span
                    >
                    <span class="font-poppins text-gray-800" id="namaPasien"
                      >I Komang Riko raditya</span
                    >
                  </div>
                  <div class="grid grid-cols-2 gap-2 border-b pb-2">
                    <span class="text-gray-600 font-extrabold">No. KTP</span>
                    <span class="font-medium text-gray-800" id="noKTP"
                      >1234567890123456</span
                    >
                  </div>
                  <div class="grid grid-cols-2 gap-2 border-b pb-2">
                    <span class="text-gray-600 font-extrabold"
                      >Jenis Kelamin</span
                    >
                    <span class="font-medium text-gray-800" id="jenisKelamin"
                      >Laki-laki</span
                    >
                  </div>
                  <div class="grid grid-cols-2 gap-2 border-b pb-2">
                    <span class="text-gray-600 font-extrabold">No. HP</span>
                    <span class="font-medium text-gray-800" id="noHP"
                      >081234567890</span
                    >
                  </div>
                  <div class="grid grid-cols-2 gap-2 border-b pb-2">
                    <span class="text-gray-600 font-extrabold"
                      >Tanggal Lahir</span
                    >
                    <span class="font-medium text-gray-800" id="ttl"
                      >01/01/1990</span
                    >
                  </div>
                  <div class="grid grid-cols-2 gap-2 border-b pb-2">
                    <span class="text-gray-600 font-extrabold">Alamat</span>
                    <span class="font-medium text-gray-800" id="alamat"
                      >Sengkiding</span
                    >
                  </div>
                  <div class="grid grid-cols-2 gap-2 border-b pb-2">
                    <span class="text-gray-600 font-extrabold">Keluhan</span>
                    <span class="font-medium text-gray-800" id="keluhan"
                      >Demam dan batuk</span
                    >
                  </div>
                  <div class="grid grid-cols-2 gap-2 border-b pb-2">
                    <span class="text-gray-600 font-extrabold"
                      >Tanggal Kunjungan</span
                    >
                    <span
                      class="font-medium text-gray-800"
                      id="tanggalKunjungan"
                      >20/03/2025</span
                    >
                  </div>
                  <div class="grid grid-cols-2 gap-2 border-b pb-2">
                    <span class="text-gray-600 font-extrabold"
                      >Poli Tujuan</span
                    >
                    <span class="font-medium text-gray-800" id="poliTujuan"
                      >Poli Umum</span
                    >
                  </div>
                  <div class="grid grid-cols-2 gap-2 border-b pb-2">
                    <span class="text-gray-600 font-extrabold"
                      >Jenis Pasien</span
                    >
                    <span class="font-medium text-gray-800" id="jenisPasien"
                      >BPJS</span
                    >
                  </div>
                  <div class="grid grid-cols-2 gap-2 border-b pb-2">
                    <span class="text-gray-600 font-extrabold"
                      >NIK / No. BPJS</span
                    >
                    <span class="font-medium text-gray-800" id="nikBPJS"
                      >9876543210987654</span
                    >
                  </div>
                </div>

                <div
                  class="mt-4 p-3 bg-green-800 text-white text-center rounded-xl shadow-md"
                >
                  <h3 class="text-sm font-semibold">Nomor Antrian Anda</h3>
                  <p class="text-2xl font-bold mt-2" id="nomorAntrian">A-001</p>
                </div>
              </div>
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
