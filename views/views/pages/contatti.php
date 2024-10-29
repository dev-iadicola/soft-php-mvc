<section class="container mx-auto px-4 sm:px-6 lg:px-8 mt-5">
    <h2 class="text-4xl font-bold text-gray-100 mb-8 text-center">Contattami</h2>
    
    <form action="/contatti" method="post" class="bg-gray-800 p-8 rounded-lg shadow-lg">
        <div class="mb-6">
            <label for="nome" class="block text-lg font-semibold text-blue-400 mb-2">Nome e Cognome <span class="text-red-400">*</span></label>
            <input type="text" id="nome" name="nome" class="form-control bg-gray-200 border border-gray-400 text-gray-800 placeholder-gray-500 rounded-lg py-2 px-4 w-full focus:ring-2 focus:ring-blue-500" placeholder="Mario" maxlength="100" required>
        </div>

        <div class="mb-6">
            <label for="email" class="block text-lg font-semibold text-blue-400 mb-2">Email <span class="text-red-400">*</span></label>
            <input type="email" id="email" name="email" class="form-control bg-gray-200 border border-gray-400 text-gray-800 placeholder-gray-500 rounded-lg py-2 px-4 w-full focus:ring-2 focus:ring-blue-500" placeholder="mybest@mail.it" maxlength="100" required>
        </div>

        <div class="mb-6">
            <label for="typologie" class="block text-lg font-semibold text-blue-400 mb-2">Chi sei? <span class="text-red-400">*</span></label>
            <select name="typologie" id="typologie" class="form-select bg-gray-200 border border-gray-400 text-gray-800 rounded-lg py-2 px-4 w-full focus:ring-2 focus:ring-blue-500" required>
                <option disabled selected value="">Seleziona</option>
                <option value="Privato">Privato</option>
                <option value="Azienda">Azienda</option>
                <option value="Developer">Developer</option>
            </select>
        </div>

        <div class="mb-6">
            <label for="messaggio" class="block text-lg font-semibold text-blue-400 mb-2">Messaggio <span class="text-red-400">*</span></label>
            <textarea id="messaggio" name="messaggio" class="form-control bg-gray-200 border border-gray-400 text-gray-800 placeholder-gray-500 rounded-lg py-2 px-4 w-full focus:ring-2 focus:ring-blue-500" rows="5" minlength="20" required></textarea>
        </div>

        <div class="mb-6 flex items-center">
            <input class="form-check-input bg-gray-800 border-gray-400 text-blue-400 rounded focus:ring-blue-500" type="checkbox" value="" id="flexCheckDefault" required>
            <label for="flexCheckDefault" class="ml-2 text-lg font-semibold text-blue-400">Accetta <a href="/laws" class="text-blue-300 underline">Termini e Condizioni</a> <span class="text-red-400">*</span></label>
        </div>

        <div class="text-center">
            <button type="submit" class="bg-blue-500 text-white font-bold py-2 px-6 rounded-lg shadow-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">Invia</button>
        </div>
    </form>
</section>
