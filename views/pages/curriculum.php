<?php if (isset($curriculum)): ?>
    <div class="fade-in-section mt-20">
        <h3 class="text-3xl font-bold text-white mb-6">Curriculum</h3>

        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-center items-center mt-4">
                <div class="mb-3">
                    <a href="<?= $curriculum->img ?>" 
                       download="<?= htmlspecialchars($curriculum->title, ENT_QUOTES, 'UTF-8') ?>" 
                       class="flex items-center bg-white border border-blue-500 text-blue-500 hover:bg-blue-100 rounded-lg px-4 py-2 transition duration-300" 
                       onclick="downloadFunction('<?= $curriculum->id ?>')">
                        <i class="fas fa-file-pdf fa-2x mr-2 text-red-600"></i>
                        <?= $curriculum->title ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
