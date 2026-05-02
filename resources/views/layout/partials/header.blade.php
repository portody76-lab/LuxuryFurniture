<div class="bg-white p-5 sm:p-8 rounded-2xl mb-6 sm:mb-8 shadow-md border border-[#e7ddcf]">
    <h2 class="text-xl sm:text-3xl font-bold text-gray-800">
        Halo, {{ auth()->user()->username }}
    </h2>
    <p class="text-[#8b7a66] text-sm sm:text-base mt-2">
        Selamat datang di Dashboard {{ ucfirst(auth()->user()->role->role_name) }} Luxury Furniture
    </p>
</div>