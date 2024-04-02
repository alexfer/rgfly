import './utils';

const uploadInput = document.querySelector('input[type="file"]');
const formData = new FormData();
const confirmDelete = document.getElementsByClassName('confirm-delete');
const confirmChange = document.getElementsByClassName('confirm-change');
const toastSuccess = document.getElementById('toast-success');
const toastDanger = document.getElementById('toast-danger');

if (uploadInput) {
    uploadInput.addEventListener('change', (event) => {
        let file = event.target.files[0];
        let url = uploadInput.getAttribute('data-url');
        let max = +uploadInput.getAttribute('max');
        let status = document.querySelector('[role="status"]');

        if (file.size > max) {
            uploadInput.value = null;
            file = null;
            showToast(toastDanger, 'The file size too large');
            return;
        }

        formData.append('file', file);
        status.firstElementChild.classList.remove('invisible');

        fetch(url, {method: 'POST', body: formData}).then(async response => {
            const isJson = response.headers.get('content-type')?.includes('application/json') || undefined;
            const attachments = document.getElementById('attachments');
            const data = isJson && await response.json();
            showToast(toastSuccess, data.message);

            const wrap = document.createElement('div');
            const img = document.createElement('img');
            wrap.className = 'd-inline-block mr-3 mb-3';
            img.className = 'h-auto max-w-xs shadow-xl dark:shadow-gray-800 rounded';
            img.src = data.picture;
            wrap.appendChild(img);
            attachments.prepend(wrap);

            status.firstElementChild.classList.add('invisible');
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
                let id = el.parentElement.getAttribute('data-id');
                let url = el.getAttribute('href');
            });
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
                let id = el.parentElement.getAttribute('data-id');
                let url = el.getAttribute('href');
                const response = await fetch(url, {
                    method: 'post',
                    body: JSON.stringify({id: id})
                });
                const data = await response.json();
                showToast(toastSuccess, data.message);
                confirmModal.style.display = 'none';
                el.parentElement.parentElement.remove();
                document.getElementsByTagName('body')[0].classList.remove('overflow-y-hidden');
            }, {capture: true, once: true});
        });
    });
}