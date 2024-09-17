import './../utils';

const pgForm = document.querySelector('form[name="payment_gateway"]');
const carrierForm = document.querySelector('form[name="carrier"]');
const danger = document.getElementById('toast-danger');
const success = document.getElementById('toast-success');
const logo = document.getElementById('carrier_logo');
let src = null;

logo.addEventListener('change', (event) => {
    event.preventDefault();
    const image = event.target.files[0];
    const reader = new FileReader();
    const preview = document.getElementById('carrier_preview');
    reader.onload = function(e) {
        src = e.target.result;
        preview.classList.remove('hidden');
        preview.setAttribute('src', src);

    }
    reader.readAsDataURL(image);

});

carrierForm.addEventListener('submit', async event => {
    event.preventDefault();
    const response = await fetch(carrierForm.getAttribute('action'), {
        method: 'POST',
        mode: 'cors',
        body: JSON.stringify({carrier: {src: src}}),
        headers: new Headers({'content-type': 'application/json'}),
    })
});

pgForm.addEventListener('submit', async event => {
    event.preventDefault();
    const inputs = bindForm(pgForm);
    const target = pgForm.getAttribute('name');

    const response = await fetch(pgForm.getAttribute('action'), {
        method: 'POST',
        body: JSON.stringify({payment_gateway: inputs[target]}),
        headers: new Headers({'Content-Type': 'application/json'}),
    });

    const data = await response.json();

    if (data.success === false) {
        showToast(danger, data.error);
    }

    if (data.success === true) {
        showToast(success, data.message);
        pgForm.reset();
        pgForm.querySelector('button[type="reset"]').click();
    }
});