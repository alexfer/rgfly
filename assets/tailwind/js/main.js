const uploadInput = document.querySelector('input[type="file"]');
const formData = new FormData();
const confirmDelete = document.getElementsByClassName('confirm-delete');

if (uploadInput) {
    uploadInput.addEventListener('change', (event) => {
        let file = event.target.files[0];
        let url = uploadInput.getAttribute('data-url');
        let max = +uploadInput.getAttribute('max');
        let status = document.querySelector('[role="status"]');

        if (file.size > max) {
            uploadInput.value = null;
            file = null;
            // TODO: display warning text
        }
        formData.append('file', file);
        status.firstElementChild.classList.remove('hidden');

        fetch(url, {method: 'POST', body: formData}).then(async response => {
            const isJson = response.headers.get('content-type')?.includes('application/json') || undefined;
            const data = isJson && await response.json();
            status.firstElementChild.classList.add('hidden');
            console.log(data);
        });
    });
}
if (confirmDelete.length > 0) {
    console.log(confirmDelete);
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
                confirmModal.style.display = 'none';
                document.getElementsByTagName('body')[0].classList.remove('overflow-y-hidden');
                console.log(data);
            });
            document.getElementsByTagName('body')[0].classList.add('overflow-y-hidden');
        });
    });
}

window.closeModal = (id) => {
    document.getElementById(id).style.display = 'none';
    document.getElementsByTagName('body')[0].classList.remove('overflow-y-hidden');
};