import './../utils';

const forms = document.querySelectorAll('form');
const danger = document.getElementById('toast-danger');
const success = document.getElementById('toast-success');
const logo = document.getElementById('carrier_logo');
let src = null;

logo.addEventListener('change', (event) => {
    event.preventDefault();
    const image = event.target.files[0];
    const reader = new FileReader();
    const preview = document.getElementById('carrier_preview');
    const attach = document.getElementById('carrier_attach');
    const media = document.getElementById('carrier_media');

    reader.onload = function (e) {
        src = e.target.result;
        preview.classList.remove('hidden');
        preview.setAttribute('src', src);
        attach.value = src;
        media.value = JSON.stringify({
            name: image.name,
            size: image.size,
            mime: image.type,
        });
    }
    reader.readAsDataURL(image);
});

[...forms].forEach((form) => {
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const inputs = bindForm(form);
        const target = form.getAttribute('name');
        const body = {};
        body[target] = inputs[target];

        const response = await fetch(form.getAttribute('action'), {
            method: 'POST',
            body: JSON.stringify(body),
            headers: new Headers({'Content-Type': 'application/json'}),
        });

        const data = await response.json();

        if (data.success === false) {
            showToast(danger, data.error);
        }

        if (data.success === true) {
            showToast(success, data.message);
            form.reset();
            form.querySelector('button[type="reset"]').click();
        }
    });
});