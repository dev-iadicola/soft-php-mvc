<?php if (isset($certificati)): ?>
    <div class="fade-in-section mt-20">
        <h3 class="text-4xl font-extrabold text-white mb-12 text-center">Professional Courses Timeline</h3>

        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative border-l-4 border-blue-500">
                <?php foreach ($certificati as $index => $certificato) : ?>
                    <div class="mb-12 ml-6"> 
                        <!-- Indicatore della tappa sulla linea temporale -->
                        <div class="absolute w-6 h-6 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-full mt-1.5 -left-3.5 border-2 border-white"></div>
                        
                        <!-- Contenuto della tappa -->
                        <div class="p-6 bg-white shadow-xl rounded-lg border-l-4 border-blue-500">
                            <div class="flex items-center mb-3">
                                <h5 class="text-2xl font-semibold text-gray-800"><?= $certificato->title ?></h5>
                                <span class="ml-auto bg-blue-100 text-blue-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded"><?= htmlspecialchars($certificato->certified) ?></span>
                            </div>
                            <p class="text-base text-gray-700"><?= $certificato->ente ?></p>
                           
                            <div class="bg-gray-900 m-10 p-10">
                                <p class="mt-3 text-gray-300 m-20">
                                    <?= $certificato->overview ?>
                                </p>
                            </div>
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

<style>
    /* Stili per garantire la responsività */
    @media (max-width: 768px) {
        .fade-in-section h3 {
            font-size: 2rem; /* Ridimensiona il titolo per gli schermi più piccoli */
        }
        .bg-gray-900 {
            margin: 5px; /* Riduci il margine per i contenitori di background */
            padding: 5px; /* Riduci il padding per i contenitori di background */
        }
        .text-gray-700 {
            font-size: 0.875rem; /* Font più piccolo per il testo del corso */
        }
        .text-base {
            font-size: 0.875rem; /* Font più piccolo per il testo di descrizione */
        }
        .rounded-lg {
            border-radius: 0.375rem; /* Riduci il border-radius per adattarlo meglio */
        }
    }
</style>
