<div class="fade-in-section mt-20">
    <h3 class="text-4xl font-bold text-white mb-8 text-center">Cloud Projects</h3>

    <div class="relative my-10">
        <div class="overflow-hidden">
            <div class="flex transition-transform duration-500 ease-in-out" id="carousel">
                <?php foreach ($projects as $index => $project) : ?>
                    <article class="min-w-full">
                        <div class="bg-white  shadow-lg rounded-lg overflow-hidden relative" 
                        style="background-image: url('<?= $project->img ?>');
                         background-size: cover; 
                         background-position: center;">
                            <!-- Corpo della Card -->
                            <div class="p-6 bg-black bg-opacity-50 "> <!-- Sfondo semi-trasparente per il testo -->
                                <h5 class="text-2xl font-bold text-white mb-4"><?= $project->title ?></h5>
                                <div class="bg-gray-100 text-white rounded-lg p-4 mb-4">
                                   <p> <?= $project->overview ?></p>
                                </div>
                                <a href="<?= $project->link ?>" 
                                class="my-10 inline-flex items-center bg-gradient-to-r 
                                from-teal-400 to-cyan-500 text-white hover:bg-gradient-to-l 
                                focus:outline-none focus:ring-2 focus:ring-cyan-500 rounded-lg 
                                px-4 py-2 transition duration-300 " target="_blank" rel="noopener noreferrer">
                                    View Project <i class="fa fa-link ml-2 text-xl"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Navigazione per dot (Indicatori) -->
        <div class="flex justify-center mt-4 space-x-2" id="dots-container">
            <?php foreach ($projects as $index => $project) : ?>
                <button onclick="goToSlide(<?= $index ?>)" class="dot w-3 h-3 rounded-full bg-gray-400" aria-label="Vai al progetto <?= $index + 1 ?>"></button>
            <?php endforeach; ?>
        </div>

        <button class="absolute top-1/2 left-0 transform -translate-y-1/2 bg-gray-700 text-white rounded-full p-2" onclick="prevSlide()">❮</button>
        <button class="absolute top-1/2 right-0 transform -translate-y-1/2 bg-gray-700 text-white rounded-full p-2" onclick="nextSlide()">❯</button>
    </div>
</div>



<?php if(isset($gits)): ?>
<div class="fade-in-section mt-20">
    <h3 class="text-4xl font-bold text-white mb-8 text-center">GitHub Projects</h3>
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($gits as $project) : ?>
                <div class="bg-gray-200 text-white shadow-lg rounded-lg overflow-hidden">
                    <!-- Immagine e titolo del progetto -->
                    <img src="<?= $project->img ?>" class="w-full h-48 object-cover" alt="<?= $project->title ?>">
                    <div class="p-6">
                        <h5 class="text-2xl font-bold mb-4"><?= htmlspecialchars($project->title) ?></h5>
                        <p><?= $project->overview ?></p>

                      

                        

                        <!-- Link al progetto su GitHub -->
                        <a href="<?= $project->link ?>" class="inline-flex items-center mt-6 bg-gradient-to-r from-teal-400 to-cyan-500 text-white hover:bg-gradient-to-l focus:outline-none focus:ring-2 focus:ring-cyan-500 rounded-lg px-4 py-2 transition duration-300" target="_blank" rel="noopener noreferrer">
                            View on GitHub <i class="fa fa-github ml-2 text-xl"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif ?>


<script>
    let currentSlide = 0;
const slides = document.querySelectorAll('#carousel > div');
const dots = document.querySelectorAll('#dots-container .dot');

function updateCarousel() {
    const carousel = document.getElementById('carousel');
    carousel.style.transform = 'translateX(-' + (currentSlide * 100) + '%)';
    updateDots();
}

function updateDots() {
    dots.forEach((dot, index) => {
        if (index === currentSlide) {
            dot.classList.remove('bg-gray-400');
            dot.classList.add('bg-teal-500');
        } else {
            dot.classList.remove('bg-teal-500');
            dot.classList.add('bg-gray-400');
        }
    });
}

function goToSlide(index) {
    currentSlide = index;
    updateCarousel();
}

function nextSlide() {
    currentSlide = (currentSlide + 1) % slides.length;
    updateCarousel();
}

function prevSlide() {
    currentSlide = (currentSlide - 1 + slides.length) % slides.length;
    updateCarousel();
}

// Avvio con il dot selezionato corretto
updateDots();

</script>
