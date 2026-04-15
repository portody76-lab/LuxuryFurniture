@extends('layout.content')

@section('title', 'Manajemen Produk')

@section('content')
<div class="flex items-center justify-center min-h-[60vh]">
    <div class="text-center">
        <i class="fas fa-hard-hat text-6xl text-amber-500 mb-4"></i>
        <h1 class="text-2xl font-bold text-gray-700">Halaman Sedang Dibangun</h1>
        <p class="text-gray-500 mt-2">Fitur Manajemen Produk akan segera tersedia.</p>
        <a href="{{ url()->previous() }}" class="inline-block mt-6 bg-amber-600 text-white px-6 py-2 rounded-xl hover:bg-amber-700 transition">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>
</div>
@endsection