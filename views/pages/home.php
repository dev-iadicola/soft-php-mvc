<div class="fade-in-section mt-20 py-12">
    <div class="container mx-auto px-4">
        <div class="flex flex-col items-center">
            <?php foreach ($profiles as $profile) : ?>
                <div class="relative bg-gradient-to-br from-teal-400 to-teal-600 text-white p-8 rounded-lg shadow-xl max-w-md mx-auto mb-10">
                    <!-- Intestazione -->
                    <h1 class="text-4xl font-extrabold mb-2"><?php echo $profile->name; ?></h1>
                    <h2 class="text-2xl font-medium mb-4"><?php echo $profile->tagline; ?></h2>
                    
                    <!-- Messaggio di Benvenuto -->
                  <?php echo $profile->welcome_message; ?>

                    <!-- Decorazione -->
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-10 rounded-full"></div>
                    <div class="absolute bottom-0 left-0 w-32 h-32 bg-white opacity-10 rounded-full"></div>
                </div>
            <?php endforeach; ?>
        </div>
        <hr class="border-teal-300 my-10 mx-auto w-2/3">
    </div>
</div>




<div class="fade-in-section mt-20 py-10">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($skills as $skill) : ?>
                <div class="bg-gray-800 p-6 rounded-lg shadow-lg flex flex-col items-center">
                    <h1 class="text-2xl font-bold text-white mb-2"><?= $skill->title; ?></h1>
                    <p class="text-base text-gray-300 text-center">
                        <?= $skill->description; ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
        <hr class="border-gray-700 my-10 mx-auto w-3/4">
    </div>
</div>


<div class="fade-in-section mt-20  py-10">
    <div class="container mx-auto px-4">
        <?php foreach ($articles as $article) : ?>
            <div class="flex flex-col md:flex-row items-start mb-10 bg-gray-800 p-6 rounded-lg shadow-lg">
                <!-- Immagine in alto a destra -->
                <?php if (!empty($article->img)) : ?>
                    <div class="md:w-1/3 flex-shrink-0 mb-4 md:mb-0 md:mr-6">
                        <img src="<?php echo $article->img; ?>" class="rounded-lg shadow-md" alt="Immagine Articolo">
                    </div>
                <?php endif; ?>
                <!-- Contenuto testuale -->
                <div class="md:w-2/3">
                    <h1 class="text-3xl font-bold text-white mb-2"><?= $article->title ?></h1>
                    <?php if (!empty($article->subtitle)) : ?>
                        <h2 class="text-xl font-semibold text-gray-300 mb-4"><?php echo $article->subtitle; ?></h2>
                    <?php endif; ?>
                    <p class="text-base text-gray-200 mb-4 ">
                        <?php echo $article->overview; ?>
                    </p>
                    <?php if (!empty($article->link)) : ?>
                        <a href="<?php echo $article->link; ?>" 
                           class="text-blue-400 hover:text-blue-600 font-semibold"><?php echo $article->link; ?></a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

@include('pages.portfolio')




<a class="fa fa-arrow-up btn-arrow" id="btn-arrow" href="#top" aria-hidden="true"></a>