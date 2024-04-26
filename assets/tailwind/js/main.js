import Swal from "sweetalert2";
import i18next from "i18next"
import './utils';

const formData = new FormData();
const headers = {'Content-type': 'application/json; charset=utf-8'};
const eventOptions = {
    capture: true,
    once: true
};

const elements = {
    uploadInput: document.querySelector('input[type="file"]'),
    confirmDelete: document.getElementsByClassName('confirm-delete'),
    confirmChange: document.getElementsByClassName('confirm-change'),
    toastSuccess: document.getElementById('toast-success'),
    toastDanger: document.getElementById('toast-danger'),
    loadCategories: document.getElementById('load-categories'),
    deleteEntry: document.querySelectorAll('.delete-entry'),
    tabList: document.querySelector('ul[role="tablist"]'),
    addEntry: document.querySelectorAll('.add-entry')
};

if (elements.addEntry && elements.addEntry.length > 0) {
    elements.addEntry.forEach((el, i) => {
        el.addEventListener('click', async (event) => {
            event.preventDefault();
            let url = el.getAttribute('data-url');
            let xhr = el.getAttribute('data-xhr');
            let owner = el.getAttribute('data-modal-target');
            let option = document.createElement('option');
            let form = document.getElementById('form[' + owner + ']');
            let input = form.querySelector('input[type="text"]');
            input.value = null

            if (xhr !== null) {
                fetch(xhr)
                    .then(data => data.json())
                    .then(data => {
                        let countries = data.countries;
                        Object.keys(countries).forEach((key) => {
                            form.querySelector('#suppler_country').appendChild(new Option(countries[key], key));
                        });
                    });
            }

            form.querySelector('button[type="submit"]').addEventListener('click', async (ev) => {
                ev.preventDefault();
                const response = await fetch(url, {method: 'POST', body: new FormData(form)});
                const data = await response.json();
                let json = data.json;
                option.value = json.option.id;
                option.text = json.option.name;
                option.selected = true;
                document.querySelector(data.json.id).append(option);
                setTimeout(() => {
                    form.querySelector('button[data-modal-name="modal"]').click();
                }, 200);
            });
        });
    });

}


if (elements.tabList) {
    Array.from(elements.tabList.children).forEach((el) => {
        Array.from(el.children).forEach((handler) => {
            handler.addEventListener('click', () => {
                let location = handler.getAttribute('aria-controls');
                window.history.replaceState({}, '', location);
            });
        });
    });
}

if (elements.deleteEntry) {
    Array.from(elements.deleteEntry).forEach((entry) => {
        entry.addEventListener('click', () => {
            let url = entry.getAttribute('data-url');
            let token = entry.getAttribute('data-token');
            Swal.fire({
                text: i18next.t('question'),
                showCancelButton: true,
                confirmButtonText: i18next.t('proceed'),
                denyButtonText: i18next.t('cancel'),
                icon: "question",
                showLoaderOnConfirm: true
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const response = await fetch(url, {
                            method: 'POST',
                            body: JSON.stringify({'_token': token}),
                            headers: headers
                        }
                    );
                    const data = await response.json();
                    document.location.href = data.redirect;
                }
            });
        });
    });
}

if (elements.loadCategories) {
    elements.loadCategories.addEventListener('click', () => {
        let children = elements.loadCategories.parentElement.children;
        for (let el of Array.from(children)) {
            el.classList.remove('sr-only');
        }
        elements.loadCategories.remove();
    });
}

if (elements.toastSuccess) {
    let flash = document.querySelector('input[name="flash"]');
    if (flash) {
        let messages = JSON.parse(flash.value);
        if (typeof messages.message !== 'undefined') {
            showToast(elements.toastSuccess, messages.message);
        }
    }
}

if (elements.uploadInput) {
    elements.uploadInput.addEventListener('change', (event) => {
        let file = event.target.files[0];
        let url = elements.uploadInput.getAttribute('data-url');
        let max = +elements.uploadInput.getAttribute('max');
        let status = document.querySelector('[role="status"]');
        const attachments = document.getElementById('attachments');

        if (file.size > max) {
            elements.uploadInput.value = null;
            file = null;
            showToast(elements.toastDanger, 'The file size too large');
            return;
        }

        formData.append('file', file);
        attachments.parentElement.classList.add('invisible');
        status.classList.remove('hidden');

        fetch(url, {method: 'POST', body: formData}).then(async response => {
            const isJson = response.headers.get('content-type')?.includes('application/json') || undefined;
            const data = isJson && await response.json();
            showToast(elements.toastSuccess, data.message);

            const wrap = document.createElement('li');
            const img = document.createElement('img');
            const inner = document.createElement('div');

            wrap.className = attachments.firstChild.nextElementSibling.getAttribute('class');
            img.className = attachments.querySelector('img').getAttribute('class');
            inner.className = attachments.querySelector('div').getAttribute('class');
            img.src = data.picture;

            wrap.appendChild(img);
            wrap.appendChild(inner);
            attachments.prepend(wrap);

            attachments.parentElement.classList.remove('invisible');
            status.classList.add('hidden');
            elements.uploadInput.value = null;
            file = null;
        });
    });
}

if (elements.confirmChange.length > 0) {
    Array.from(elements.confirmChange).forEach((el) => {
        el.addEventListener('click', (event) => {
            event.preventDefault();
            let confirmModal = document.getElementById('changeConfirm');
            confirmModal.style.display = 'block';
            confirmModal.querySelector('a[role="button"]').addEventListener('click', async (event) => {
                event.preventDefault();
                await handleClick(el, confirmModal);
                document.getElementsByTagName('body')[0].classList.remove('overflow-y-hidden');
            }, eventOptions);
        });
    });
}

if (elements.confirmDelete.length > 0) {
    Array.from(elements.confirmDelete).forEach((el) => {
        el.addEventListener('click', (event) => {
            event.preventDefault();
            let confirmModal = document.getElementById('deleteConfirm');
            confirmModal.style.display = 'block';
            confirmModal.querySelector('a[role="button"]').addEventListener('click', async (event) => {
                event.preventDefault();
                await handleClick(el, confirmModal);
                el.parentElement.parentElement.remove();
                document.getElementsByTagName('body')[0].classList.remove('overflow-y-hidden');
            }, eventOptions);
        });
    });
}

window.handleClick = async (el, confirmModal) => {
    let id = el.parentElement.getAttribute('data-id');
    let url = el.getAttribute('href');
    const response = await fetch(url, {
        method: 'post',
        body: JSON.stringify({id: id})
    });
    const data = await response.json();
    showToast(elements.toastSuccess, data.message);
    confirmModal.style.display = 'none';
};