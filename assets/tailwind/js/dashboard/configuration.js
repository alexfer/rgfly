import './../utils';

const pgForm = document.querySelector('form[name="payment_gateway"]');
const danger = document.getElementById('toast-danger');
const success = document.getElementById('toast-success');

pgForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const inputs = bindForm(pgForm);
    const response = await fetch(pgForm.getAttribute('action'), {
        method: 'POST',
        body: JSON.stringify({payment_gateway: inputs['payment_gateway']}),
        headers: new Headers({'Content-Type': 'application/json'}),
    });

    const data = await response.json();

    if (data.success === false) {
        showToast(danger, data.message);
    }

    if (data.success === true) {
        showToast(success, data.message);
        pgForm.reset();
        pgForm.querySelector('button[type="reset"]').click();
    }
})