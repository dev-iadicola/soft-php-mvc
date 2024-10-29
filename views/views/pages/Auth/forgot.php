<form action="/forgot" method="POST">
   <div class="container py-5 h-screen flex justify-center items-center">
      <div class="w-full max-w-md">
        <div class="bg-gray-800 text-white rounded-lg shadow-lg">
          <div class="p-6 text-center">
            
            <div class="mb-8">
              <h2 class="text-2xl font-bold mb-2 uppercase">Reset Password</h2>
              <p class="text-gray-400 mb-4">Inserisci la tua Email</p>
              <p class="text-gray-400 mb-4">{{message}}</p>
              
              <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Email</label>
                <input type="email" name="email" id="email" class="w-full px-3 py-2 border border-gray-600 rounded-lg bg-gray-900 text-white focus:outline-none focus:ring-2 focus:ring-blue-500" />
              </div>

              <p class="text-sm mb-4"><a href="/login" class="text-gray-400 hover:text-white">Hai la password? Accedi</a></p>
              
              <button type="submit" class="w-full py-2 px-4 border border-transparent rounded-lg shadow-sm text-base font-medium text-gray-900 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Reset Password</button>
            </div>
            
          </div>
        </div>
      </div>
    </div>
</form>
