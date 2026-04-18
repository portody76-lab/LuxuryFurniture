<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Login - Luxury Furniture</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        input:focus {
            outline: none;
            ring: 2px solid #D2B473;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-animate {
            animation: fadeIn 0.3s ease-out forwards;
        }
    </style>
</head>

<body class="bg-gray-200 flex items-center justify-center min-h-screen relative p-4 overflow-x-hidden">

    <div class="flex flex-col md:flex-row bg-white rounded-2xl shadow-xl overflow-hidden w-full max-w-4xl mx-auto">

        {{-- SISI KIRI: LOGO --}}
        <div class="w-full md:w-1/2 bg-[#D2B473] flex items-center justify-center p-6 sm:p-10">
            <img src="{{ asset('images/Logo LF.png') }}" alt="Logo" class="w-40 sm:w-52 md:w-64 lg:w-72">
        </div>

        {{-- SISI KANAN: FORM LOGIN --}}
        <div class="w-full md:w-1/2 p-6 sm:p-8 md:p-10">
            <h2 class="text-lg sm:text-xl font-semibold mb-4 sm:mb-6">Masuk ke akun</h2>

            {{-- ========================================== --}}
            {{-- ALERT NOTIFIKASI (DI BAWAH JUDUL) --}}
            {{-- ========================================== --}}
            
            {{-- ERROR VALIDASI (dari controller) --}}
            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 rounded-lg alert-animate">
                    <div class="flex items-start gap-2">
                        <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
                        <div class="text-sm text-red-700">
                            <p class="font-semibold mb-1">Login gagal!</p>
<p>{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- SUKSES LOGOUT (dari controller) --}}
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-50 border-l-4 border-green-500 rounded-lg alert-animate">
                    <div class="flex items-start gap-2">
                        <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                        <div class="text-sm text-green-700">
                            <p class="font-semibold">Berhasil!</p>
                            <p>{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ERROR UMUM (jika ada) --}}
            @if(session('error'))
                <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 rounded-lg alert-animate">
                    <div class="flex items-start gap-2">
                        <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
                        <div class="text-sm text-red-700">
                            <p class="font-semibold">Terjadi kesalahan!</p>
                            <p>{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- FORM LOGIN --}}
            <form method="POST" action="/">
                @csrf

                <label class="block text-sm mb-1">Username</label>
                <div class="flex items-center bg-gray-100 rounded-lg mb-4 overflow-hidden">
                    <div class="bg-[#D2B473] p-3 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="white" class="w-4 h-4 sm:w-5 sm:h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </div>
                    <input type="text" name="username" value="{{ old('username') }}" placeholder="Masukkan Username"
                        class="w-full p-3 bg-gray-100 outline-none text-sm">
                </div>

                <label class="block text-sm mb-1">Password</label>
                <div class="flex items-center bg-gray-100 rounded-lg mb-6 overflow-hidden">
                    <div class="bg-[#D2B473] p-3 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="white" class="w-4 h-4 sm:w-5 sm:h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                    </div>
                    <div class="relative flex-1">
                        <input type="password" id="password" name="password" placeholder="Masukkan Password"
                            class="w-full p-3 bg-gray-100 outline-none text-sm">
                        <button type="button" onclick="togglePassword()"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-[#D2B473] transition">
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

        // Auto hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert-animate');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    if (alert.parentElement) alert.remove();
                }, 300);
            });
        }, 5000);
    </script>

</body>

</html>