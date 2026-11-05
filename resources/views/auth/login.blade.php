<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MaterialApp</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-r from-indigo-100 via-white to-indigo-100 flex items-center justify-center px-4">

    <div class="max-w-md w-full bg-white shadow-lg rounded-xl p-8 space-y-6">
        <!-- Logo -->
        <div class="text-center">


            <h2 class="mt-4 text-2xl font-bold text-gray-800">Selamat Datang</h2>
            <p class="mt-1 text-gray-500 text-sm">Masuk untuk mengelola material lapangan</p>
        </div>

        <!-- Session Status -->
        @if(session('status'))
            <div class="text-sm text-green-600 bg-green-100 p-2 rounded">{{ session('status') }}</div>
        @endif

        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                       placeholder="Masukkan email Anda">
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                       placeholder="Masukkan password Anda">
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between">
                <label class="inline-flex items-center text-sm text-gray-600">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    <span class="ml-2">Remember me</span>
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:text-indigo-800">Forgot your password?</a>
                @endif
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit"
                        class="w-full py-2 bg-gradient-to-r from-gray-500 to-gray-700 hover:from-gray-600 hover:to-gray-800 text-white font-semibold rounded-lg shadow-md transition duration-200">
                    Log in
                </button>
            </div>
        </form>

        <!-- Footer -->
        <div class="text-center text-gray-500 text-sm mt-4">
            &copy; {{ date('Y') }} MaterialApp. All rights reserved.
        </div>
    </div>

</body>
</html>
