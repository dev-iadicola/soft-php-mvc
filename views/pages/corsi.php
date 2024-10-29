<?php if (isset($certificati)): ?>
    <div class="fade-in-section mt-20">
        <h3 class="text-3xl font-bold text-white mb-6">Professional Courses</h3>

        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap -mx-4">
                <?php foreach ($certificati as $certificato) : ?>
                    <div class="w-full md:w-1/3 px-4 mb-6">
                        <div class="bg-white shadow-lg rounded-lg p-6">
                            <!-- Corpo della Card -->
                            <div class="card-body">
                                <h5 class="text-2xl font-semibold text-gray-800 mb-4"><?= $certificato->title ?></h5>
                                <p class="text-base text-gray-700 mb-4"><?= $certificato->ente ?></p>
                                <p class="text-base text-gray-700 mb-4"><?= $certificato->overview ?></p>
                                <a href="<?= $certificato->link ?>" class="text-blue-500 hover:text-blue-700" target="_blank">
                                    Apri il link <i class="fa fa-file-text-o" aria-hidden="true"></i>
                                </a>
                                <p class="text-sm text-gray-500 mt-2">Data rilascio: <?=  $certificato->certified ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>