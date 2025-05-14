
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


    window.onload = async function () {
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
