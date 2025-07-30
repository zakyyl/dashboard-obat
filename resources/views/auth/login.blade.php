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
        <div class="flex-1 flex justify-center items-center p-8 lg:p-12 bg-white shadow-md order-2 lg:order-1">
            <div class="text-center w-full max-w-lg">
                <img src="{{ asset('images/news.png') }}" alt="Transformasi Polri Presisi" class="max-w-full h-auto mx-auto mb-4">
            </div>
        </div>

        <div class="flex-1 flex justify-center items-center p-8 lg:p-12 order-1 lg:order-2 min-h-screen lg:min-h-0">
            <div class="w-full max-w-md">
                <div class="text-center mb-8">
                   
                    <h1 class="block text-2xl font-bold text-black-800 dark:text-dark">LOGIN</h1>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Silakan masukkan email dan password untuk login
                    </p>
                </div>

                <form action="{{ route('login.post') }}" method="POST">
                    @csrf {{-- Laravel CSRF token --}}
                    <div class="grid gap-y-4">
                        <div>
                            <label for="email" class="block text-sm mb-2 text-dark-700 dark:text-dark-200">Email</label>
                            <div class="relative">
                                <input type="email" id="email" name="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="nama@email.com" required aria-describedby="email-error">
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-500" id="email-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div>
                            <label for="password" class="block text-sm mb-2 text-dark-700 dark:text-dark-200">Password</label>
                            <div class="relative password-toggle-wrapper"> {{-- Tambahkan wrapper relative --}}
                                <input type="password" id="password" name="password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="••••••••" required aria-describedby="password-error">
                                <i class="bi bi-eye-slash-fill password-toggle-icon" id="togglePassword"></i> {{-- Ikon mata --}}
                                @error('password')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-500" id="password-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <button type="submit" class="w-full text-white bg-yellow-500 hover:bg-yellow-600 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-yellow-600 dark:hover:bg-yellow-700 dark:focus:ring-yellow-800">
                            <i class="bi bi-box-arrow-in-right"></i> Sign In
                        </button>
                    </div>
                </form>
                <div class="mt-2 text-center text-gray-500 text-sm">
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