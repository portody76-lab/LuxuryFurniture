<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
  <title>Login - Luxury Furniture</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  
  <style>
    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translate(-50%, -20px);
      }
      to {
        opacity: 1;
        transform: translate(-50%, 0);
      }
    }
    
    @keyframes slideUp {
      from {
        opacity: 1;
        transform: translate(-50%, 0);
      }
      to {
        opacity: 0;
        transform: translate(-50%, -20px);
        visibility: hidden;
      }
    }
    
    .toast-notification {
      position: fixed;
      top: 20px;
      left: 50%;
      transform: translateX(-50%);
      z-index: 9999;
      width: 100%;
      max-width: 400px;
      animation: slideDown 0.3s ease-out forwards;
    }
    
    .toast-notification.hide {
      animation: slideUp 0.3s ease-in forwards;
    }
    
    .toast-content {
      background: white;
      border-radius: 16px;
      padding: 16px;
      margin: 0 16px;
      display: flex;
      align-items: center;
      gap: 12px;
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    .toast-error {
      background-color: #ef4444;
      color: white;
    }
    
    .toast-success {
      background-color: #22c55e;
      color: white;
    }
    
    .toast-icon {
      width: 40px;
      height: 40px;
      background-color: rgba(255, 255, 255, 0.2);
      border-radius: 9999px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .toast-icon i {
      font-size: 18px;
    }
    
    .toast-text {
      flex: 1;
    }
    
    .toast-title {
      font-weight: 600;
      font-size: 14px;
      margin-bottom: 2px;
    }
    
    .toast-message {
      font-size: 12px;
      opacity: 0.9;
    }
    
    .toast-close {
      color: rgba(255, 255, 255, 0.7);
      background: transparent;
      border: none;
      cursor: pointer;
      font-size: 16px;
      transition: color 0.2s;
    }
    
    .toast-close:hover {
      color: white;
    }
    
    input:focus {
      outline: none;
      ring: 2px solid #D2B473;
    }
  </style>
</head>

<body class="bg-gray-200 flex items-center justify-center min-h-screen relative p-4 overflow-x-hidden">

  <!-- NOTIFIKASI ERROR LOGIN -->
  @if($errors->any())
  <div id="errorToast" class="toast-notification">
    <div class="toast-content toast-error">
      <div class="toast-icon">
        <i class="fas fa-exclamation-triangle"></i>
      </div>
      <div class="toast-text">
        <p class="toast-title">Login Gagal!</p>
        <p class="toast-message">{{ $errors->first() }}</p>
      </div>
      <button onclick="closeToast('errorToast')" class="toast-close">
        <i class="fas fa-times"></i>
      </button>
    </div>
  </div>
  @endif

  <!-- NOTIFIKASI LOGOUT SUKSES -->
  @if(session('success'))
  <div id="successToast" class="toast-notification">
    <div class="toast-content toast-success">
      <div class="toast-icon">
        <i class="fas fa-check-circle"></i>
      </div>
      <div class="toast-text">
        <p class="toast-title">Berhasil!</p>
        <p class="toast-message">{{ session('success') }}</p>
      </div>
      <button onclick="closeToast('successToast')" class="toast-close">
        <i class="fas fa-times"></i>
      </button>
    </div>
  </div>
  @endif

  {{-- CARD LOGIN UTAMA - RESPONSIF --}}
  <div class="flex flex-col md:flex-row bg-white rounded-2xl shadow-xl overflow-hidden w-full max-w-4xl mx-auto">

    {{-- SISI KIRI: LOGO (di mobile tampil di atas) --}}
    <div class="w-full md:w-1/2 bg-[#D2B473] flex items-center justify-center p-6 sm:p-10">
      <img src="{{ asset('images/Logo LF.png') }}" alt="Logo" class="w-40 sm:w-52 md:w-64 lg:w-72">
    </div>

    {{-- SISI KANAN: FORM LOGIN --}}
    <div class="w-full md:w-1/2 p-6 sm:p-8 md:p-10">
      <h2 class="text-lg sm:text-xl font-semibold mb-4 sm:mb-6">Masuk ke akun</h2>

      <form method="POST" action="/">
        @csrf

        <label class="block text-sm mb-1">Username</label>
        <div class="flex items-center bg-gray-100 rounded-lg mb-4 overflow-hidden">
          <div class="bg-[#D2B473] p-3 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="white"
              class="w-4 h-4 sm:w-5 sm:h-5">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>
          </div>
          <input type="text" name="username" value="{{ old('username') }}" placeholder="Masukkan Username"
            class="w-full p-3 bg-gray-100 outline-none text-sm @error('username') ring-2 ring-red-400 @enderror">
        </div>

        <label class="block text-sm mb-1">Password</label>
        <div class="flex items-center bg-gray-100 rounded-lg mb-6 overflow-hidden">
          <div class="bg-[#D2B473] p-3 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="white"
              class="w-4 h-4 sm:w-5 sm:h-5">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
            </svg>
          </div>
          <div class="relative flex-1">
            <input type="password" id="password" name="password" placeholder="Masukkan Password"
              class="w-full p-3 bg-gray-100 outline-none text-sm @error('username') ring-2 ring-red-400 @enderror">
            <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-[#D2B473] transition">
              <i id="passwordIcon" class="fas fa-eye text-sm sm:text-base"></i>
            </button>
          </div>
        </div>

        <button type="submit"
          class="w-full bg-[#D2B473] text-white py-3 rounded-lg font-semibold hover:opacity-90 transition text-sm sm:text-base">
          Login
        </button>
      </form>
    </div>
  </div>

  <script>
    function togglePassword() {
      const passwordInput = document.getElementById('password');
      const icon = document.getElementById('passwordIcon');
      
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    }
    
    function closeToast(toastId) {
      const toast = document.getElementById(toastId);
      if (toast) {
        toast.classList.add('hide');
        setTimeout(() => {
          toast.style.display = 'none';
        }, 300);
      }
    }
    
    setTimeout(() => {
      const errorToast = document.getElementById('errorToast');
      const successToast = document.getElementById('successToast');
      
      if (errorToast) {
        closeToast('errorToast');
      }
      if (successToast) {
        closeToast('successToast');
      }
    }, 5000);
    
    const inputs = document.querySelectorAll('input');
    inputs.forEach(input => {
      input.addEventListener('focus', function() {
        this.classList.remove('ring-2', 'ring-red-400');
      });
    });
  </script>

</body>

</html>