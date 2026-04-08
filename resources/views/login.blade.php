<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Login</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-200 flex items-center justify-center min-h-screen">

  <div class="flex bg-white rounded-2xl shadow-xl overflow-hidden w-200 scale-125">

    <div class="w-1/2 bg-[#D2B473] flex items-center justify-center p-10">
      <img src="{{ asset('images/Logo LF.png') }}" alt="Logo" class="w-72">
    </div>

    <div class="w-1/2 p-10">
      <h2 class="text-xl font-semibold mb-6">Masuk ke akun</h2>

      <form method="POST" action="/login">
        @csrf
@if(session('error'))
  <div class="flex items-center gap-2 bg-red-100 border border-red-300 text-red-700 px-4 py-3 mb-4 rounded-lg text-sm">

    <!-- Icon -->
    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z" />
    </svg>

    <!-- Text -->
    <span>{{ session('error') }}</span>

  </div>
@endif
        <label class="block text-sm mb-1">Username</label>
        <div class="flex items-center bg-gray-100 rounded-lg mb-4 overflow-hidden">

          <div class="bg-[#D2B473] p-3 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="white"
              class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>
          </div>

          <input type="text" name="username" placeholder="Masukkan Username"
            class="w-full p-3 bg-gray-100 outline-none text-sm">
        </div>


        <label class="block text-sm mb-1">Password</label>
        <div class="flex items-center bg-gray-100 rounded-lg mb-6 overflow-hidden">

          <div class="bg-[#D2B473] p-3 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="white"
              class="w-5">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
            </svg>

          </div>

          <input type="password" name="password" placeholder="Masukkan Password"
            class="w-full p-3 bg-gray-100 outline-none text-sm">
        </div>

        <button type="submit"
          class="w-full bg-[#D2B473] text-white py-3 rounded-lg font-semibold hover:opacity-90 transition">
          Login
        </button>
      </form>
    </div>

  </div>

</body>

</html>