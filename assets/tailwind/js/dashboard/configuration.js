import './../utils';
import Swal from "sweetalert2";
import i18next from "i18next";
import customCss from "../customCss";

const forms = document.querySelectorAll('form');
const danger = document.getElementById('toast-danger');
const success = document.getElementById('toast-success');
const logo = document.getElementById('carrier_logo');
const remove = document.querySelectorAll('.rm');
const change = document.querySelectorAll('.change');


[...change].forEach((element) => {
    const target = element.getAttribute('data-target');
    const url = element.getAttribute('data-url');
    const form = document.querySelector(`form[name="${target}"]`);

    element.addEventListener('click', async (e) => {
        e.preventDefault();
        const response = await fetch(url);
        const data = await response.json();
        const {elements} = form;

        form.setAttribute('action', data.pop());
        form.setAttribute('method', 'put');

        for (const [key, value] of Object.entries(data.shift())) {
            const field = elements.namedItem(`${target}[${key}]`);
            if (field && field.getAttribute('type') === 'checkbox') {
                field.checked = !field.checked;
            } else {
                field && (field.value = value);
            }
        }
        form.querySelector('button[type="submit"]').addEventListener('click', async (e) => {
            e.preventDefault();
            const inputs = bindForm(form);
            const body = {};
            body[target] = inputs[target];

            const response = await fetch(url, {
                method: 'PUT',
                body: JSON.stringify(body),
                headers: new Headers({'Content-Type': 'application/json'}),
            });

            const data = await response.json();
            console.log(data);
        });
    });

});
[...remove].forEach((element) => {
    element.addEventListener('click', (e) => {
        const target = element.getAttribute('data-target');
        const id = element.getAttribute('data-id');
        const url = element.getAttribute('data-url');
        e.preventDefault();
        Swal.fire({
            text: i18next.t('question'),
            showCancelButton: true,
            showConfirmButton: true,
            customClass: customCss,
            confirmButtonText: i18next.t('proceed'),
            denyButtonText: i18next.t('cancel'),
            icon: "question"
        }).then(async (result) => {
            if (result.isConfirmed) {
                const response = await fetch(url, {
                    method: 'DELETE',
                    headers: new Headers({'Content-Type': 'application/json'})
                });

                const data = await response.json();

                if (data.success === true) {
                    showToast(success, data.message);
                    document.querySelector(`table[data-for="${target}"]`).querySelector(`tr[id="row-${id}"]`).remove();
                }

                if (data.success === false) {
                    showToast(danger, data.error);
                }
            }
        });
    });
});

logo.addEventListener('change', (event) => {
    event.preventDefault();
    const image = event.target.files[0];
    const reader = new FileReader();
    const preview = document.getElementById('carrier_preview');
    const attach = document.getElementById('carrier_attach');
    const media = document.getElementById('carrier_media');

    reader.onload = function (e) {
        const src = e.target.result;
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
            const tbody = document.querySelector(`table[data-for="${target}"]`).getElementsByTagName('tbody')[0];
            const row = tbody.insertRow(tbody.rows.length);
            row.innerHTML = data.template;

            showToast(success, data.message);

            form.reset();
            form.querySelector('button[type="reset"]').click();
        }
    });
});