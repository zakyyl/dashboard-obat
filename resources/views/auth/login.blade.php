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
        /* Optional: sedikit styling untuk posisi ikon */
        .password-toggle-wrapper {
            position: relative;
        }
        .password-toggle-icon {
            position: absolute;
            right: 12px; /* Sesuaikan jarak dari kanan */
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6B7280; /* Warna ikon default (gray-500) */
            z-index: 10; /* Pastikan ikon di atas input */
        }
        .dark .password-toggle-icon {
            color: #9CA3AF; /* Warna ikon di dark mode (gray-400) */
        }
    </style>
</head>
<body class="font-sans bg-gray-100 antialiased">
    <div class="flex flex-col lg:flex-row min-h-screen">
        {{-- Sisi Kiri / Atas: Banner Presisi --}}
        <div class="w-full lg:flex-1 flex justify-center items-center p-6 sm:p-8 lg:p-12 order-1" style="background-color: #f0f4f8;">
            <div class="text-center w-full max-w-xs sm:max-w-md lg:max-w-lg">
                <img src="{{ asset('images/news.png') }}" alt="Transformasi Polri Presisi" class="max-w-full max-h-48 sm:max-h-64 lg:max-h-none h-auto mx-auto object-contain">
            </div>
        </div>

        {{-- Sisi Kanan / Bawah: Form Login --}}
        <div class="w-full lg:flex-1 flex justify-center items-center p-6 sm:p-8 lg:p-12 order-2" style="background-color: #0f5386;">
            <div class="w-full max-w-md">
                <div class="text-center mb-6 sm:mb-8">
                    <h1 class="block text-2xl sm:text-3xl font-extrabold text-yellow-400 tracking-wide">LOGIN</h1>
                    <p class="mt-2 text-center text-gray-200 text-xs sm:text-sm">
                        Silakan masukkan email / ID dan password untuk login
                    </p>
                </div>

                <form action="{{ route('login.post') }}" method="POST">
                    @csrf {{-- Laravel CSRF token --}}
                    <div class="grid gap-y-4">
                        <div>
                            <label for="email" class="block text-sm font-semibold mb-1 text-yellow-400">Email / ID</label>
                            <div class="relative">
                                <input type="text" id="email" name="email" value="{{ old('email') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Masukkan email atau ID" required aria-describedby="email-error">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-300" id="email-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-semibold mb-1 text-yellow-400">Password</label>
                            <div class="relative password-toggle-wrapper">
                                <input type="password" id="password" name="password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="••••••••" required aria-describedby="password-error">
                                <i class="bi bi-eye-slash-fill password-toggle-icon" id="togglePassword"></i>
                                @error('password')
                                    <p class="mt-1 text-sm text-red-300" id="password-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <button type="submit" class="w-full text-white bg-yellow-500 hover:bg-yellow-600 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-semibold rounded-lg text-sm px-5 py-3 text-center transition-colors shadow-md">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
                        </button>
                    </div>
                </form>
                <div class="mt-4 text-center text-gray-300 text-xs">
                    Copyright &copy; RS BHAYANGKARA JAMBI 2025
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
                    // Toggle the type attribute
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);

                    // Toggle the eye icon
                    this.classList.toggle('bi-eye-fill');
                    this.classList.toggle('bi-eye-slash-fill');
                });
            }
        });
    </script>
</body>
</html>