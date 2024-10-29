<div class="fade-in-section mt-20">
    <h3 class="text-4xl font-bold text-white mb-8 text-center">Projects</h3>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap -mx-4">
            <?php foreach ($projects as $project) : ?>
                <div class="w-full md:w-1/3 px-4 mb-8">
                    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                        <!-- Immagine del Progetto -->
                        <img src="<?= htmlspecialchars($project->img) ?>" class="w-full h-48 object-cover" alt="<?= htmlspecialchars($project->title) ?>">

                        <!-- Corpo della Card -->
                        <div class="p-6">
                            <h5 class="text-2xl font-bold text-gray-800 mb-4"><?= htmlspecialchars($project->title) ?></h5>
                            <p class="text-base text-gray-600 mb-4"><?= htmlspecialchars($project->overview) ?></p>
                            <a href="<?= htmlspecialchars($project->link) ?>"
                             class="inline-flex items-center bg-gradient-to-r from-teal-400 
                             to-cyan-500 text-white hover:bg-gradient-to-l focus:outline-none focus:ring-2 
                             focus:ring-cyan-500 rounded-lg px-4 py-2 transition duration-300" target="_blank">
                                View Project <i class="fa fa-github ml-2 text-xl"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
