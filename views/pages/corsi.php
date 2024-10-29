<?php if (isset($certificati)): ?>
    <div class="fade-in-section mt-20">
        <h3 class="text-4xl font-extrabold text-white mb-12 text-center">Professional Courses Timeline</h3>

        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative border-l-4 border-blue-500">
                <?php foreach ($certificati as $index => $certificato) : ?>
                    <div class="mb-12 ml-6"> <!-- Rimosso group e classi di transizione -->
                        <!-- Indicatore della tappa sulla linea temporale -->
                        <div class="absolute w-6 h-6 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-full mt-1.5 -left-3.5 border-2 border-white"></div>
                        
                        <!-- Contenuto della tappa -->
                        <div class="p-6 bg-white shadow-xl rounded-lg border-l-4 border-blue-500"> <!-- Rimosso group-hover -->
                            <div class="flex items-center mb-3">
                                <h5 class="text-2xl font-semibold text-gray-800"><?= $certificato->title ?></h5>
                                <span class="ml-auto bg-blue-100 text-blue-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded"><?= htmlspecialchars($certificato->certified) ?></span>
                            </div>
                            <p class="text-base text-gray-700"><?= $certificato->ente ?></p>
                            <p class="mt-3 text-gray-600"><?= $certificato->overview ?></p>
                            <a href="<?= $certificato->link ?>" class="text-blue-600 hover:text-blue-800 mt-4 inline-flex items-center" target="_blank">
                                <i class="fa fa-external-link mr-2"></i>View Certificate
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>
