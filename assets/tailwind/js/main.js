import './utils';

const uploadInput = document.querySelector('input[type="file"]');
const formData = new FormData();
const confirmDelete = document.getElementsByClassName('confirm-delete');
const confirmChange = document.getElementsByClassName('confirm-change');
const toastSuccess = document.getElementById('toast-success');
const toastDanger = document.getElementById('toast-danger');
const loadCategories = document.getElementById('load-categories');
const eventOptions = {
    capture: true,
    once: true
};

if (loadCategories) {
    loadCategories.addEventListener('click', () => {
        let children = loadCategories.parentElement.children;
        for (let el of Array.from(children)) {
            el.classList.remove('sr-only');
        }
        loadCategories.remove();
    });
}

if (toastSuccess) {
    let flash = document.querySelector('input[name="flash"]').value;
    if (typeof flash !== 'undefined') {
        let messages = JSON.parse(flash);
        if (typeof messages.message !== 'undefined') {
            showToast(toastSuccess, messages.message);
        }
    }
}

if (uploadInput) {
    uploadInput.addEventListener('change', (event) => {
        let file = event.target.files[0];
        let url = uploadInput.getAttribute('data-url');
        let max = +uploadInput.getAttribute('max');
        let status = document.querySelector('[role="status"]');
        const attachments = document.getElementById('attachments');

        if (file.size > max) {
            uploadInput.value = null;
            file = null;
            showToast(toastDanger, 'The file size too large');
            return;
        }

        formData.append('file', file);
        attachments.parentElement.classList.add('invisible');
        status.classList.remove('hidden');

        fetch(url, {method: 'POST', body: formData}).then(async response => {
            const isJson = response.headers.get('content-type')?.includes('application/json') || undefined;
            const data = isJson && await response.json();
            showToast(toastSuccess, data.message);

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
            uploadInput.value = null;
            file = null;
        });
    });
}

if (confirmChange.length > 0) {
    Array.from(confirmChange).forEach((el) => {
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

if (confirmDelete.length > 0) {
    Array.from(confirmDelete).forEach((el) => {
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
    showToast(toastSuccess, data.message);
    confirmModal.style.display = 'none';
};