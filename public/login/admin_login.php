<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" />
  <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../css/style.css" />
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
</head>

<body class="flex items-center justify-center h-screen bg-gray-100">
  <div class="bg-white p-8 rounded-lg shadow-lg w-96">
    <img src="../img/profil.png" alt="" class="w-20 pb-3 mx-auto" />
    <h2 class="text-xl font-poppins font-bold mb-4 text-center">
      Login Admin
    </h2>
    <form onsubmit="handleLoading(event)">
      <label class="block text-xs mb-2">Username</label>
      <input type="text" id="username" class="w-full text-xs px-3 py-2 border rounded mb-4" required />
      <label class="block text-xs mb-2">Password</label>
      <input type="password" id="password" class="w-full px-3 text-xs py-2 border rounded mb-4" required />
      <button type="submit" class="w-full text-xs bg-green-600 hover:bg-green-800 text-white py-2 rounded">
        Login
      </button>
      <p class="text-center text-xs text-gray-600 mt-4 font-poppins">
        Kembali ke halaman Website?
        <a onclick="showLoading(event)" data-href="../index.html"
          class="text-green-700 hover:text-emerald-800 font-bold text-xs">klik Disini</a>
      </p>
    </form>

    <!--Loading Akun-->
    <div id="loading"
      class="fixed z-50 inset-0 bg-white bg-opacity-70 backdrop-blur-sm flex flex-col justify-center items-center hidden">
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
        border-top: 4px solid #297a2c;
        border-radius: 50%;
        animation: spin 1s linear infinite;
      }
    </style>

    <script>
      function handleLoading(event) {
        event.preventDefault(); // Mencegah submit langsung

        const form = event.target;
        if (!form.checkValidity()) {
          form.reportValidity(); // Menampilkan pesan error bawaan browser
          return;
        }

        const nikInput = document.getElementById("username").value;
        const passwordInput = document.getElementById("password").value;

        // Dummy Data NIK dan Password
        const validUser = {
          nik: "admin",
          password: "admin123",
        };

        if (
          nikInput === validUser.nik &&
          passwordInput === validUser.password
        ) {
          // Tampilkan loading dan sembunyikan form
          document.getElementById("loading").classList.remove("hidden");

          setTimeout(() => {
            window.location.href = "admin/dashboard.php"; // Redirect otomatis setelah 1 detik
          }, 1000);
        } else {
          alert("Username dan Password tidak ditemukan!");
        }
      }
    </script>
    <!--Form Login-->
    <!--Loading akun-->
  </div>
  <div id="loadingOverlay"
    class="fixed z-50 inset-0 bg-white bg-opacity-80 backdrop-blur-md justify-center place-items-center ease-in-out flex hidden">
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
</body>

</html>