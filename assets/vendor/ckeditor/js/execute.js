const syncEditors = (form) => {
    if (!form) {
        return;
    }

    // sync
    form.querySelectorAll('textarea.editor').forEach((ta) => {
        const editor = ta.nextElementSibling;
        if (editor && editor.classList.contains('quill-editor')) {
            if (editor.__quill) {
                ta.value = editor.__quill.root.innerHTML;
            } else {
                ta.value = editor.querySelector('.ql-editor')?.innerHTML ?? ta.value;
            }
        }
    });
};

const initEditors = (root = document) => {
    const textareas = root.querySelectorAll('textarea.editor');

    if (textareas.length === 0) {
        return;
    }

    textareas.forEach((textarea) => {
        if (!textarea || textarea.dataset.editorInitialized === 'true') {
            return;
        }

        if (!textarea.offsetParent) {
            return;
        }

        textarea.dataset.editorInitialized = 'true';

        const wrapper = document.createElement('div');
        wrapper.className = 'quill-editor';
        wrapper.style.minHeight = '140px';
        textarea.insertAdjacentElement('afterend', wrapper);
        textarea.style.display = 'none';

        const quill = new Quill(wrapper, {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ header: [1, 2, 3, false] }],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['link', 'blockquote', 'code-block'],
                    ['clean']
                ]
            }
        });
        wrapper.__quill = quill;

        if (textarea.value) {
            quill.root.innerHTML = textarea.value;
        }

        quill.on('text-change', () => {
            textarea.value = quill.root.innerHTML;
        });

        const form = textarea.closest('form');
        if (form && !form.dataset.editorSyncBound) {
            form.dataset.editorSyncBound = 'true';
            form.addEventListener('submit', () => syncEditors(form));
        }
    });
};

document.addEventListener('DOMContentLoaded', () => {
    initEditors();

    document.addEventListener('shown.bs.collapse', () => {
        initEditors();
    });
});

window.syncEditors = syncEditors;
