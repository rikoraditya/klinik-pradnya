<?php
ini_set('session.cookie_path', '/');
session_start();

if (isset($_SESSION["login"])) {
  header("location: admin/dashboard.php");
  exit;
}



require '../php/functions.php';

if (isset($_POST["login"])) {
  $username = $_POST["username"];
  $password = $_POST["password"];

  // Query tanpa spasi yang salah
  $result = mysqli_query($conn, "SELECT * FROM admin WHERE username = '$username'");

  if (mysqli_num_rows($result) === 1) {
    $row = mysqli_fetch_assoc($result);

    // Bandingkan password langsung (karena tidak pakai password_hash)
    if ($password === $row["password"]) {
      $_SESSION["login"] = true;
      $_SESSION["username"] = $row["username"];
      header("Location: admin/dashboard.php");
      exit;
    } else {
      $error = 'Password salah!';
      echo "<script>alert('$error');</script>";
    }
  } else {
    $error = 'Username tidak ditemukan!';
    echo "<script>alert('$error');</script>";
  }
}
?>




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
      event.preventDefault(); // Mencegah pindah halaman langsung
      let overlay = document.getElementById('loading-overlay');

      if (overlay) {
        overlay.classList.remove('hidden'); // Tampilkan spinner
      }

      let targetUrl = event.target.href; // Simpan URL yang akan dikunjungi
      // Pastikan jika URL relative, tambahkan location.origin
      if (!targetUrl.startsWith('http')) {
        targetUrl = location.origin + '/' + targetUrl; // Menggunakan location.origin untuk absolute path
      }

      history.pushState({ path: targetUrl }, '', targetUrl); // Simpan URL dalam state history

      // Tunggu sebentar, lalu pindah halaman
      setTimeout(() => {
        window.location.href = targetUrl; // Redirect ke halaman tujuan
      }, 1000); // Delay 1 detik agar efek loading terlihat
    }

  </script>
</head>

<body class="flex items-center justify-center h-screen bg-gray-100">
  <div class="bg-white p-8 rounded-lg shadow-lg w-96">
    <img src="../img/profil.png" alt="" class="w-20 pb-3 mx-auto" />
    <h2 class="text-xl font-poppins font-bold mb-4 text-center">
      Login Admin
    </h2>
    <form id="formAdmin" method="post" action="">
      <label class="block text-xs mb-1">Username</label>
      <input type="text" name="username" id="username" class="w-full text-xs px-3 py-2 border rounded mb-2" required />
      <label class="block text-xs mb-1">Password</label>
      <input type="password" name="password" id="password" class="w-full px-3 text-xs py-2 border rounded mb-4"
        required />
      <button type="submit" id="submitBtn" name="login" formaction=""
        class="w-full text-xs bg-green-600 hover:bg-green-800 text-white py-2 rounded">
        Login
      </button>
      <p class="text-center text-xs text-gray-600 mt-4 font-poppins">
        Kembali ke halaman Website?
        <a href="../index.html" class="text-green-700 hover:text-emerald-800 font-bold text-xs cursor-pointer">klik
          Disini</a>
      </p>
    </form>
  </div>
  <!--Loading Akun-->
  <div id="loadingAdmin"
    class="hidden fixed inset-0 z-[9999] bg-black bg-opacity-50 backdrop-blur-md flex flex-col items-center justify-center">
    <div class="flex flex-col items-center">
      <svg class="animate-spin h-10 w-10 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
        </path>
      </svg>
      <p class="text-white text-base mt-4">Loading...</p>
    </div>
  </div>

  <script>
    document.getElementById("formAdmin").addEventListener("submit", function (e) {
      e.preventDefault();

      document.getElementById("loadingAdmin").classList.remove("hidden");
      const submitBtn = document.getElementById("submitBtn");
      submitBtn.disabled = true;
      submitBtn.classList.add("opacity-50", "cursor-not-allowed");

      const hiddenLogin = document.createElement('input');
      hiddenLogin.type = 'hidden';
      hiddenLogin.name = 'login';
      hiddenLogin.value = '1';
      this.appendChild(hiddenLogin); // Pastikan nilai "login" dikirim ke PHP

      setTimeout(() => {
        this.submit();
      }, 2000);
    });


  </script>


  <!--Form Login-->
  <!--Loading akun-->

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