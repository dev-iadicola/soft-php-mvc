<article class="fade-in-section mt-5 py-5">
    <div class="container">
        <div>
            <?php foreach ($profiles as $profile) : ?>
                <div class=" p-5 shadow-lg mb-4 bg-dark rounded">
                    <!-- Intestazione -->
                    <div class="d-flex flex-row justify-content-center mb-5">



                        <h1 class="display-4 mb-2 fw-bold text-center dodgerblu w-100" id="name"> 
                            <i class="fa fa-code text-white" style="font-size:3rem" aria-hidden="true"></i>
                            <?php echo $profile->name; ?>
                        </h1>

                    </div>
                    <!-- Messaggio di Benvenuto -->
                    <div class="d-flex flex-row justify-content-center">
                        <i class="fa fa-terminal" style="font-size: 2.2rem; color:mediumseagreen" aria-hidden="true"></i>
                        <h2 class="h3 mb-4 text-white" id="tagline"><?php echo $profile->tagline; ?> </h2>

                        <p class="h3 text-white" id="welcome"><?php echo $profile->welcome_message; ?> </p> <span id='cursor-text' class="h4 text-white fs-bold"> |</span>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>
        <hr class="my-5 border-teal-300">
    </div>
</article>

<script>
    const tagline = document.getElementById("tagline");
    const welcome = document.getElementById("welcome");
    const cursor = document.getElementById("cursor-text");
    const resultWriting = document.getElementById("result-writing");

    const blinkCursor = (cursor, time = 600) => {
        setInterval(() => {
            cursor.classList.toggle('cursor-hidden');
        }, time);
    };

    const pause = (ms) => new Promise(resolve => setTimeout(resolve, ms));

    // Scrittura che sovrascrive ad ogni frame
    const writeText = (elem, text, speed = 100) => {
        return new Promise((resolve) => {
            let i = 0;
            const interval = setInterval(() => {
                elem.textContent = text.substring(0, i + 1);
                i++;
                if (i >= text.length) {

                    clearInterval(interval);
                    pause(1000).then(() => {
                        deleteTextWithCursor(elem).then(resolve);
                    });
                }
            }, speed);
        });
    };

    // Utility per cancellare subito
    const deleteText = (elem) => {
        return elem.textContent = '';
    };

    const deleteTextWithCursor = (elem, time = 100) => {
        return new Promise((resolve) => {
            let text = elem.textContent;
            let i = text.length;

            const interval = setInterval(() => {
                elem.textContent = text.substring(0, i - 1);
                i--;

                if (i <= 0) {
                    clearInterval(interval);
                    resolve();
                }
            }, time);
        });
    };


    window.onload = async function() {
        const taglineText = tagline.textContent.trim();
        const welcomeText = welcome.textContent.trim();

        deleteText(tagline);
        deleteText(welcome);
        blinkCursor(cursor);

        while (true) {
            await writeText(tagline, taglineText); // scrive e poi cancella

            await writeText(welcome, welcomeText); // scrive e poi cancella
        }
    };
</script>


@include('pages.progetti')

<div class="bg-white rounded-lg shadow-lg">
    <div class="fade-in-section mt-5 py-5">
        <h1 class="text-dark text-4xl font-weight-bold mb-5 text-center">Scopri le mie Skills</h1>
        <p class="text-center text-lg text-muted mb-4">
            Sono appassionato di tecnologia e sempre aggiornato sulle ultime tendenze. Ecco le competenze che posso mettere al servizio del tuo progetto!
        </p>
        <div class="container">
            <div class="row">
                <?php foreach ($skills as $skill) : ?>
                    <article class="col-md-4 mb-4">
                        <div class="bg-dark text-white p-4 rounded-lg shadow-md text-center">
                            <h2 class="h4 font-weight-bold mb-2"><?= $skill->title; ?></h2>
                            <p class="text-muted"><?= $skill->description; ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>


<!-- Scroll to top button -->
<a class="fa fa-arrow-up btn-arrow" id="btn-arrow" href="#top" aria-hidden="true"></a>