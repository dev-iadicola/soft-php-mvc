<form action="/login" method="POST" class="bg-gray-900 py-5 h-screen flex items-center justify-center">
    <div class="w-full max-w-md bg-gray-800 text-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold mb-4 text-center uppercase">Login</h2>
        <p class="text-gray-400 mb-6 text-center">Please enter your login and password!</p>

        <!-- Messaggio di errore o avviso -->
        {{message}}

        <div class="mb-4">
            <label for="email" class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" id="email" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your email" required />
        </div>

        <div class="mb-6">
            <label for="password" class="block text-sm font-medium mb-1">Password</label>
            <input type="password" name="password" id="password" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your password" required />
        </div>

        <p class="text-sm text-gray-400 mb-4">
            <a href="/forgot" class="hover:underline">Forgot password?</a>
        </p>
        <p class="text-sm text-gray-400 mb-6">
            <a href="/sign-up" class="hover:underline">Sei il primo? Registrati</a>
        </p>

        <button type="submit" class="w-full py-2 px-4 bg-blue-500 text-white font-semibold rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
            Login
        </button>
    </div>
</form>
