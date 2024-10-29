<section class="container my-5 py-5">
    <form method="POST" action="/token/change-password" class="max-w-md mx-auto bg-gray-800 text-white rounded-lg shadow-lg p-6">
        <div class="mb-6">
            <label for="password" class="block text-lg font-medium text-gray-300 mb-2">Inserisci la nuova password</label>

            <h3 class="text-red-400 mb-4">{{message}}</h3>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-300 mb-1">Password *</label>
                <input type="password" name="password" minlength="8" class="w-full px-3 py-2 border border-gray-600 rounded-lg bg-gray-900 text-white focus:outline-none focus:ring-2 focus:ring-blue-500" id="password" placeholder="Password *" required>
            </div>

            <div class="mb-6">
                <label for="confirmed" class="block text-sm font-medium text-gray-300 mb-1">Ripeti Password *</label>
                <input type="password" name="confirmed" minlength="8" class="w-full px-3 py-2 border border-gray-600 rounded-lg bg-gray-900 text-white focus:outline-none focus:ring-2 focus:ring-blue-500" id="confirmed" placeholder="Ripeti Password *" required>
            </div>

            <input type="text" hidden name="token" value="{{token}}" readonly>

            <button type="submit" class="w-full py-2 px-4 bg-blue-500 text-white font-medium rounded-lg shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Invia</button>
        </div>
    </form>
</section>
