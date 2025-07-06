const editors = document.querySelectorAll('.editor');

if (editors.length === 0) {
    console.warn('⚠️ Nessun elemento .editor trovato nel DOM.');
}

editors.forEach(editorElement => {
    // CKEditor va in errore se l'elemento è nascosto o nullo
    if (!editorElement || !editorElement.offsetParent) {
        console.warn('Elemento editor non valido o non visibile:', editorElement);
        return;
    }

    ClassicEditor
        .create(editorElement, {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote']
        })
        .catch(error => {
            console.error('CKEditor initialization error:', error);
        });
});
