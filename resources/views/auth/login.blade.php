<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dashboard Presisi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        /* Styling untuk posisi ikon password */
        .password-toggle-wrapper {
            position: relative;
        }
        .password-toggle-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #9CA3AF; /* Warna ikon diubah agar lebih cocok dengan input gelap */
            z-index: 10;
        }
    </style>
</head>
<body class="font-sans bg-gray-100 antialiased">
    <div class="flex flex-col lg:flex-row min-h-screen">

        {{-- Bagian sebelah kiri dengan gambar background --}}
        <div class="flex-1 flex justify-center items-center p-8 lg:p-12 order-2 lg:order-1" style="background-image: url('{{ asset('images/login-backgrounds.jpg') }}'); background-size: cover; background-position: center;">
            {{-- Konten di sisi kiri bisa ditambahkan di sini jika perlu --}}
        </div>

        {{-- PERUBAHAN DI SINI: Bagian sebelah kanan (Form Login) --}}
        <div class="flex-1 flex justify-center items-center p-8 lg:p-12 order-1 lg:order-2 bg-cyan-950">
            <div class="w-full max-w-md">
                <div class="text-center mb-8">
                    <img src="{{ asset('images/logo_RSbhayangkara.png') }}" alt="Logo Polri" class="h-20 w-20 mx-auto mb-4">
                    <h1 class="block text-2xl font-bold text-white">Login</h1>
                    <p class="mt-2 text-sm text-gray-300">
                        Silakan masukkan email dan password untuk login
                    </p>
                </div>

                <form action="{{ route('login.post') }}" method="POST">
                    @csrf
                    <div class="grid gap-y-4">
                        <div>
                            <label for="email" class="block text-sm mb-2 text-gray-200">Email</label>
                            <div class="relative">
                                {{-- Input field disesuaikan untuk background gelap --}}
                                <input type="email" id="email" name="email" class="bg-gray-800 border border-gray-600 text-white placeholder-gray-400 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="nama@email.com" required aria-describedby="email-error">
                                @error('email')
                                    <p class="mt-2 text-sm text-red-500" id="email-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div>
                            <label for="password" class="block text-sm mb-2 text-gray-200">Password</label>
                            <div class="relative password-toggle-wrapper">
                                {{-- Input field disesuaikan untuk background gelap --}}
                                <input type="password" id="password" name="password" class="bg-gray-800 border border-gray-600 text-white placeholder-gray-400 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="••••••••" required aria-describedby="password-error">
                                <i class="bi bi-eye-slash-fill password-toggle-icon" id="togglePassword"></i>
                                @error('password')
                                    <p class="mt-2 text-sm text-red-500" id="password-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <button type="submit" class="w-full text-white bg-yellow-500 hover:bg-yellow-600 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                            <i class="bi bi-box-arrow-in-right"></i> Sign In
                        </button>
                    </div>
                </form>
                <div class="mt-8 text-center text-gray-400 text-sm">
                    Copyright &copy; RS BHAYANGKARA 2025
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');

            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function () {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    this.classList.toggle('bi-eye-fill');
                    this.classList.toggle('bi-eye-slash-fill');
                });
            }
        });
    </script>
</body>
</html>
