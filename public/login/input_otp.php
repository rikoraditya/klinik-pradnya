<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

?>

<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Normalize CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Klinik Pradnya Usadha</title>
</head>

<body class="bg-gray-100">
    <!--Loading Page-->
    <div id="loading-overlay"
        class="fixed z-50 inset-0 bg-white bg-opacity-80 backdrop-blur-md flex justify-center items-center ease-in-out hidden">
        <div class="flex space-x-2">
            <div class="w-3 h-3 bg-[#297A2C] rounded-full dot"></div>
            <div class="w-3 h-3 bg-[#297A2C] rounded-full dot"></div>
            <div class="w-3 h-3 bg-[#297A2C] rounded-full dot"></div>
        </div>
    </div>

    <script>
        function showLoading(event) {
            event.preventDefault(); // Mencegah pindah halaman langsung
            let overlay = document.getElementById('loading-overlay');

            if (overlay) {
                overlay.classList.remove('hidden'); // Tampilkan spinner
            }

            // Tunggu sebentar, lalu pindah halaman
            setTimeout(() => {
                window.location.href = event.target.href; // Redirect ke halaman tujuan
            }, 1000); // Delay 1 detik agar efek loading terlihat
        }
        // Tangani tombol back agar overlay tidak tetap muncul
        window.addEventListener("pageshow", function () {
            overlay.classList.add('hidden');
        });


        // Menambahkan event listener ke semua link dalam menu mobile dan desktop
        document.addEventListener("DOMContentLoaded", function () {
            let allLinks = document.querySelectorAll('a[href]'); // Ambil semua link dalam halaman
            allLinks.forEach(link => {
                link.addEventListener("click", showLoading);
            });
        });
    </script>
    <!--Script Loading-->

    <!--Loading Page-->
    <div id="loading-overlay"
        class="fixed z-50 inset-0 bg-white bg-opacity-80 backdrop-blur-md flex justify-center items-center ease-in-out hidden">
        <div class="flex space-x-2">
            <div class="w-3 h-3 bg-[#297A2C] rounded-full dot"></div>
            <div class="w-3 h-3 bg-[#297A2C] rounded-full dot"></div>
            <div class="w-3 h-3 bg-[#297A2C] rounded-full dot"></div>
        </div>
    </div>
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
    <!--Loading Page Akhir-->
    <div class="py-3 bg-white ">
        <img class="mx-auto" src="../img/logo.JPG" width="100px" alt="">
    </div>
    <!--nav-->
    <div class="font-poppins sticky top-0 z-40">
        <nav class="relative px-4 py-1 flex justify-between items-center bg-[#297A2C] ">

            <div class="lg:hidden">
                <button id="menuButton"
                    class="text-white hover:text-emerald-100 focus:outline-none flex items-center p-3">
                    <svg class="block h-4 w-4 fill-current" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <title>Mobile menu</title>
                        <path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"></path>
                    </svg>
                </button>
            </div>
            <ul
                class="menu hidden absolute top-1/2 left-1/2 transform -translate-y-1/2 -translate-x-1/2 lg:mx-auto lg:flex lg:items-center lg:w-auto lg:space-x-6">
                <li><a class="text-xs text-white hover:text-gray-200 transition" onclick="showLoading(event)"
                        href="../index.html">Home</a></li>

                </li>
                <li><a class="text-xs mx-2 text-white hover:text-gray-200  transition" onclick="showLoading(event)"
                        href="../about.html">Tentang Kami</a></li>

                <li><a class="nav-item text-xs mx-2 text-white hover:text-gray-200 transition"
                        onclick="showLoading(event)" href="../dokter.html">Dokter</a></li>

                <li><a class="nav-item text-xs mx-2 text-white hover:text-gray-200 transition"
                        onclick="showLoading(event)" href="../pelayanan.html">Pelayanan Poli</a></li>

                <li><a class="nav-item text-xs mx-2 text-white hover:text-gray-200 transition"
                        onclick="showLoading(event)" href="../fasilitas.html">Fasilitas</a></li>

                <li><a class="nav-item text-xs mx-2 text-white hover:text-gray-200 transition"
                        onclick="showLoading(event)" href="../hubungi.html">Hubungi Kami</a></li>

                <li><a class="nav-item text-xs mx-2 text-white hover:text-gray-200 transition"
                        onclick="showLoading(event)" href="user_login.html">Pendaftaran Online</a></li>
            </ul>
            <!--Login Amnin-->
            <div class="ml-auto hidden md:block">
                <div class="relative inline-block text-left">
                    <button id="dropdownButton" class="flex items-center px-4 py-2 text-white rounded-lg">
                        <i class="fas fa-user-circle text-white text-sm mr-2"></i>
                        <span class="text-white text-xs">System Administrator</span>
                        <i class="fas fa-chevron-down text-white text-sm ml-2"></i>
                    </button>
                    <div id="dropdownMenu"
                        class="hidden absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200">
                        <div class="p-4 border-b">
                            <p class="text-gray-800 font-semibold">Admin Panel</p>
                            <p class="text-sm text-gray-500">Klinik Pradnya Usadha</p>
                        </div>
                        <a href="admin_login.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100">
                            <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"></path>
                            </svg>
                            Login Admin
                        </a>
                    </div>
                </div>
            </div>
            <script>
                document.getElementById('dropdownButton').addEventListener('click', function () {
                    document.getElementById('dropdownMenu').classList.toggle('hidden');
                });
            </script>
            <!--Akhir Admin-->
        </nav>
    </div>
    <!--akhir nav-->

    <!--Script Nav-->




    <!--Script Nav-->

    <!--nav mobile-->
    <!-- Tombol untuk membuka sidebar -->

    <!-- Sidebar -->
    <div class="font-poppins">
        <div class="navbar-menu relative z-40 ">
            <!-- Overlay Blur -->
            <div id="overlay"
                class="fixed inset-0 bg-black bg-opacity-30 backdrop-blur-md opacity-0 invisible transition-opacity duration-300">
            </div>
            <div id="sidebar"
                class="fixed top-0 left-0 bottom-0 w-5/6 max-w-sm bg-white border-r font-poppins py-6 px-6 shadow-lg transform -translate-x-full transition-transform duration-300 ease-in-out z-50">
                <div class="p-4 flex justify-between items-center">
                    <img class="-ml-1" src="../img/logo.JPG" width="100px" alt="">
                    <button id="closeButton" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div>
                    <ul class="mobile-menu">
                        <li class="mb-1 nav-item">
                            <a class="block p-4 text-sm font-semibold text-gray-400 hover:bg-gray-100 hover:shadow-md hover:text-emerald-900 rounded transition"
                                onclick="showLoading(event)" href="../index.html">Home</a>
                        </li>
                        <li class="mb-1 nav-item">
                            <a class="block p-4 text-sm font-semibold text-gray-400 hover:bg-gray-100 hover:shadow-md hover:text-emerald-900 rounded transition"
                                onclick="showLoading(event)" href="../about.html">Tentang kami</a>
                        </li>
                        <li class="mb-1 nav-item">
                            <a class="block p-4 text-sm font-semibold text-gray-400 hover:bg-gray-100 hover:shadow-md hover:text-emerald-900 rounded transition"
                                onclick="showLoading(event)" href="../dokter.html">Dokter</a>
                        </li>
                        <li class="mb-1 nav-item">
                            <a class="block p-4 text-sm font-semibold text-gray-400 hover:bg-gray-100 hover:shadow-md hover:text-emerald-900 rounded transition"
                                onclick="showLoading(event)" href="../pelayanan.html">Pelayanan Poli</a>
                        </li>
                        <li class="mb-1 nav-item">
                            <a class="block p-4 text-sm font-semibold text-gray-400 hover:bg-gray-100 hover:shadow-md hover:text-emerald-900 rounded transition"
                                onclick="showLoading(event)" href="../fasilitas.html">Fasilitas</a>
                        </li>
                        <li class="mb-1 nav-item">
                            <a class="block p-4 text-sm font-semibold text-gray-400 hover:bg-gray-100 hover:shadow-md hover:text-emerald-900 rounded transition"
                                onclick="showLoading(event)" href="../hubungi.html">Hubungi kami</a>
                        </li>
                        <li class="mb-1 nav-item">
                            <a class="block p-4 text-sm font-semibold text-gray-400 hover:bg-gray-100 hover:shadow-md hover:text-emerald-900 rounded transition"
                                onclick="showLoading(event)" href="user_login.html">Pendaftaran Online</a>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
    <!--akhir nav mobile-->

    <!--Blur BG Sidebar-->
    <script>
        const sidebar = document.getElementById("sidebar");
        const overlay = document.getElementById("overlay");
        const menuButton = document.getElementById("menuButton");
        const closeButton = document.getElementById("closeButton");

        menuButton.addEventListener("click", () => {
            sidebar.classList.remove("-translate-x-full");
            overlay.classList.remove("opacity-0", "invisible");
            document.body.classList.add("overflow-hidden"); // Hilangkan scroll
        });

        function closeMenu() {
            sidebar.classList.add("-translate-x-full");
            overlay.classList.add("opacity-0", "invisible");
            document.body.classList.remove("overflow-hidden"); // Aktifkan scroll kembali
        }

        closeButton.addEventListener("click", closeMenu);
        overlay.addEventListener("click", closeMenu);
    </script>

    <!--Animasi Sidebar klik-->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const sidebar = document.getElementById("sidebar");
            const menuButton = document.getElementById("menuButton");
            const closeButton = document.getElementById("closeButton");

            menuButton.addEventListener("click", function () {
                sidebar.classList.remove("-translate-x-full");
            });

            closeButton.addEventListener("click", function () {
                sidebar.classList.add("-translate-x-full");
            });
        });
    </script>


    <!--Akhir Animasi Sidebar Klik-->

    <!--Content 1-->

    <div class="bg-gray-100 p-4 h-screen">
        <div class="max-w-6xl mx-auto mt-10 pb-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Informasi Pendaftaran -->
                <div class="font-poppins" data-aos="fade-up" data-aos-duration="2000">
                    <h2 class="text-green-700 font-bold text-sm">PENDAFTARAN PASIEN RAWAT JALAN</h2>
                    <h1 class="text-2xl font-bold mt-2">Silakan Login Untuk <br> Melakukan Pendaftaran</h1>
                    <img src="img/pendaftaran.PNG" alt="" class="w-full mt-6 -ml-14 hidden md:block">
                </div>
                <!-- Form Pendaftaran -->
                <div class="bg-white p-6 md:max-w-md md:ml-32 rounded-lg shadow-md font-poppins">

                    <!-- Form Registrasi -->

                    <div class="flex justify-center mb-4" id="modalCekPasien">

                    </div>
                    <img src="../img/profil.png" alt="" class="w-20 pb-3 mx-auto">
                    <h2 class="text-xl font-poppins font-bold mb-4 text-center">Login</h2>

                    <!--Form Login-->
                    <h2 class="text-xl text-center justify-center font-bold text-gray-500 mb-4 md:mt-4">Verifikasi Kode
                        OTP</h2>
                    <p class="text-gray-500 text-sm justify-center text-center mb-6">Masukkan 6 digit kode yang telah
                        dikirim</p>

                    <form id="formCekPasien" method="post" action="verify_otp.php" onsubmit="gabungkanOTP(event)">
                        <div class="flex justify-center pb-2 text-sm md:gap-3 gap-2">
                            <input type="text" maxlength="1"
                                class="otp-input md:w-11 md:h-11 w-10 h-10 text-center text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                            <input type="text" maxlength="1"
                                class="otp-input md:w-11 md:h-11 w-10 h-10 text-center text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                            <input type="text" maxlength="1"
                                class="otp-input md:w-11 md:h-11 w-10 h-10 text-center text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                            <input type="text" maxlength="1"
                                class="otp-input md:w-11 md:h-11 w-10 h-10 text-center text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                            <input type="text" maxlength="1"
                                class="otp-input md:w-11 md:h-11 w-10 h-10 text-center text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                            <input type="text" maxlength="1"
                                class="otp-input md:w-11 md:h-11 w-10 h-10 text-center text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                        </div>


                        <!-- Input tersembunyi untuk gabungan OTP -->
                        <input type="hidden" name="otp" id="otpInput">
                        <!-- Token CSRF -->
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

                        <button id="submitBtn" type="submit" onclick="toggleModal()"
                            class="w-full bg-[#297A2C] text-xs pt-2 text-white px-4 py-2 mt-1 rounded hover:bg-green-900">
                            Verifikasi
                        </button>
                    </form>



                    <div id="loadingOverlayy"
                        class="hidden fixed inset-0 z-[9999] bg-black bg-opacity-50 backdrop-blur-md flex flex-col items-center justify-center">
                        <div class="flex flex-col items-center">
                            <svg class="animate-spin h-10 w-10 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                                </path>
                            </svg>
                            <p class="text-white text-base mt-4 ">Validasi kode...</p>
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





                    <script>
                        // Gabungkan input OTP sebelum submit
                        function gabungkanOTP(event) {
                            const inputEls = document.querySelectorAll('.otp-input');
                            let gabungan = '';
                            inputEls.forEach(el => gabungan += el.value);

                            // Isi ke input hidden
                            document.getElementById('otpInput').value = gabungan;

                            // Validasi jika tidak 6 digit
                            if (gabungan.length !== 6) {
                                event.preventDefault();
                                alert("Kode OTP harus 6 digit.");
                            }
                        }

                        // Script pindah input tetap dipakai
                        const inputs = document.querySelectorAll('.otp-input');
                        inputs.forEach((input, i) => {
                            input.addEventListener('input', () => {
                                if (input.value.length === 1 && i < inputs.length - 1) {
                                    inputs[i + 1].focus();
                                }
                            });
                            input.addEventListener('keydown', (e) => {
                                if (e.key === 'Backspace' && !input.value && i > 0) {
                                    inputs[i - 1].focus();
                                }
                            });
                        });
                    </script>



                    <div id="loading"
                        class="fixed z-50 inset-0 bg-white bg-opacity-90 backdrop-blur-sm  flex flex-col justify-center items-center hidden">
                        <div class="loader"></div>
                        <p class="mt-2 text-[#010101] font-medium">Mencari Akun...</p>
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
                            border-top: 4px solid #297A2C;
                            border-radius: 50%;
                            animation: spin 1s linear infinite;
                        }
                    </style>




                    <p class="text-center text-xs text-gray-600 mt-4">Masukkan kode yang sudah dikirim melalui
                        Whatsapp</p>
                </div>
            </div>
        </div>
    </div>



    <!--Akhir Content 1-->

    <!--Footer-->
    <div class=" bg-green-900  text-white font-poppins">
        <div class="mx-auto pt-8 pb-6 md:pb-4">
            <footer>
                <div class="px-1 grid grid-cols-1 md:grid-cols-3 gap-8 text-center md:text-justify">
                    <!-- Kolom 1 -->
                    <div class="md:ml-24">
                        <img src="../img/logo.JPG" width="100px" alt="Pradnya Usadha" class="mx-auto md:mx-0 mb-4">
                        <p class="text-xs text-[#5d8d75]">Menjadi Pelayanan Kesehatan yang selalu berinovasi dan
                            mengedepankan peningkatan kualitas hidup pasien dengan motto "We Care With Cencerity"</p>
                    </div>

                    <!-- Kolom 2 -->
                    <div class="md:ml-24">
                        <h2 class="text-lg font-bold mb-2 text-[#709f55]">Jam Operasional</h2>
                        <p class="text-[#5d8d75] text-xs">Beroperasi setiap hari 24 Jam</p>
                    </div>

                    <!-- Kolom 3 -->
                    <div class="md:ml-24">
                        <h2 class="text-lg font-bold mb-2 text-[#709f55]">Kontak Kami</h2>
                        <p class="text-[#5d8d75] text-xs">Jl. Flamboyan No. 55 Klungkung</p>
                        <p class="text-[#5d8d75] text-xs">Telp. (0366) 23792</p>
                        <p class="text-[#5d8d75] text-xs">Email: <a href="mailto:pradnyausadha@yahoo.com"
                                class="underline text-xs text-[#5d8d75]">pradnyausadha@yahoo.com</a></p>
                    </div>
                </div>

                <!-- Garis Pemisah -->
                <hr class="border-t border-[#2e654a] my-4">

                <!-- Copyright -->
                <p class="text-center text-[#5d8d75] text-xs">2025 Klinik Pradnya Usadha</p>
            </footer>
        </div>
    </div>
    <!--Akhir Footer-->


    <script>
        // Burger menus
        document.addEventListener('DOMContentLoaded', function () {
            // open
            const burger = document.querySelectorAll('.navbar-burger');
            const menu = document.querySelectorAll('.navbar-menu');

            if (burger.length && menu.length) {
                for (var i = 0; i < burger.length; i++) {
                    burger[i].addEventListener('click', function () {
                        for (var j = 0; j < menu.length; j++) {
                            menu[j].classList.toggle('hidden');
                        }
                    });
                }
            }

            // close
            const close = document.querySelectorAll('.navbar-close');
            const backdrop = document.querySelectorAll('.navbar-backdrop');

            if (close.length) {
                for (var i = 0; i < close.length; i++) {
                    close[i].addEventListener('click', function () {
                        for (var j = 0; j < menu.length; j++) {
                            menu[j].classList.toggle('hidden');
                        }
                    });
                }
            }

            if (backdrop.length) {
                for (var i = 0; i < backdrop.length; i++) {
                    backdrop[i].addEventListener('click', function () {
                        for (var j = 0; j < menu.length; j++) {
                            menu[j].classList.toggle('hidden');
                        }
                    });
                }
            }
        });


    </script>
    <!--Nav-->

    </div>

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
            startEvent: 'DOMContentLoaded', // name of the event dispatched on the document, that AOS should initialize on
            initClassName: 'aos-init', // class applied after initialization
            animatedClassName: 'aos-animate', // class applied on animation
            useClassNames: false, // if true, will add content of `data-aos` as classes on scroll
            disableMutationObserver: false, // disables automatic mutations' detections (advanced)
            debounceDelay: 50, // the delay on debounce used while resizing window (advanced)
            throttleDelay: 99, // the delay on throttle used while scrolling the page (advanced)


            // Settings that can be overridden on per-element basis, by `data-aos-*` attributes:
            offset: 120, // offset (in px) from the original trigger point
            delay: 0, // values from 0 to 3000, with step 50ms
            duration: 400, // values from 0 to 3000, with step 50ms
            easing: 'ease', // default easing for AOS animations
            once: false, // whether animation should happen only once - while scrolling down
            mirror: false, // whether elements should animate out while scrolling past them
            anchorPlacement: 'top-bottom', // defines which position of the element regarding to window should trigger the animation

        });
    </script>
</body>

</html>