<x-botique>
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white rounded-2xl shadow-lg flex flex-col sm:flex-row max-w-3xl w-full overflow-hidden">

        
        <!-- Left side: Login Form -->
        <div class="relative w-full md:w-1/2 p-8 flex flex-col justify-center">
            <!-- Logo -->
            <img src="{{ asset('storage/images/sewing.png') }}" 
                 alt="Boutique" 
                 class="h-12 w-auto object-contain absolute top-4 left-4 rounded-2xl">

            <!-- Title -->
            <div class="text-2xl font-bold mb-6 text-center mt-12">Login</div>
@if ($errors->any())
    <div class="p-4 mb-4 text-sm text-red-800 bg-white rounded-lg bg-red-50 dark:text-red-400" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="mt-1 block w-full border-gray-300 rounded-sm shadow-sm p-2">
                </div>

                <!-- Password -->
                <div class="mb-4 relative">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <div class="relative">
                        <input id="password" type="password" name="password" required
                            class="mt-1 block w-full border-gray-300 rounded-sm shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        
                        <!-- Eye icon -->
                        <i id="toggleEye"
                           class="fa-solid fa-eye-slash absolute inset-y-0 right-3 flex items-center cursor-pointer text-gray-500 pt-2"></i>
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="mr-2">
                        Remember me
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm text-red-500">Forgot Password?</a>
                </div>

                <!-- Login Button -->
                <button type="submit" id="login-btn" class="w-full bg-green-500 text-white py-2 rounded-lg hover:bg-green-600 flex items-center justify-center">
                    <span id="login-text">Login</span>
                    <span id="login-loader" class="hidden loading loading-spinner loading-sm ml-2"></span>
                </button>
            </form>
        </div>

        <!-- Right side: Image (hidden on small screens) -->
        <div class="hidden sm:flex w-full md:w-1/2 justify-center items-center p-8">
            <img src="{{ asset('storage/images/sewing.png') }}" alt="Boutique" 
                 class="max-w-xs w-full object-contain rounded-lg shadow">
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const loginBtn = document.getElementById('login-btn');
    const loginText = document.getElementById('login-text');
    const loginLoader = document.getElementById('login-loader');

    form.addEventListener('submit', function() {
        // Disable button
        loginBtn.disabled = true;

        // Show loader and hide text
        loginText.classList.add('hidden');
        loginLoader.classList.remove('hidden');

        // Change button text to loading
        loginText.textContent = 'Logging in...';
    });
});
</script>
</x-botique>
