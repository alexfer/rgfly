import './utils';

const form = document.getElementById('form-message');
const toastDanger = document.getElementById('toast-danger');
const toastSuccess = document.getElementById('toast-success');

form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const url = form.getAttribute('action')
    const message = form.querySelector('textarea');
    await fetch(url, {
        method: "POST",
        body: JSON.stringify({
            message: message.value,
            _token: form.querySelector('input[name="_token"]').value,
            product: form.querySelector('input[name="product"]').value,
            market: form.querySelector('input[name="market"]').value
        }),
        headers: {'Content-type': 'application/json; charset=utf-8'}
    })
        .then((response) => response.json())
        .then((json) => {
            const response = json;
            if (response.success === false) {
                showToast(toastDanger, response.error);
            }
            if (response.success === true) {
                showToast(toastSuccess, response.message);
            }
            message.value = null;
        }).catch(err => {
            console.log(err);
        });
});