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
                            <div class="flex items-center mb-3 bg-white">
                                <h5><?= $certificato->title ?></h5>
                                <span class="ml-auto bg-blue-100 text-blue-800 text-sm font-medium mr-2 px-2.5 rounded"><?= htmlspecialchars($certificato->certified) ?></span>
                            </div>


                            <p class="text-base text-gray-700"><?= $certificato->ente ?></p>

                            <!-- Pulsante per mostrare/nascondere -->
                            <button id="leggi-cert-<?= $certificato->id ?>" onclick="toggleOverview(<?= $certificato->id ?>)"
                                class="text-blue-500 hover:text-blue-700 text-sm mt-2">
                                Leggi di più
                            </button> <br/>

                            <!-- Overview con ID specifico per il toggle -->
                            <div class="hidden bg-white" id="overview-<?= $certificato->id ?>">
                                <p class="overview-content text-gray-700 text-sm hidden">
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