import './utils';
import messages from "./i18n";
import i18next from "i18next";

i18next.init(messages);

const el = {
    form: document.getElementById('form-message'),
    search: document.getElementById('order-search'),
    danger: document.getElementById('toast-danger'),
    success: document.getElementById('toast-success'),
    modal: document.getElementById('modal-message')
};

el.form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const url = el.form.getAttribute('action')
    const message = el.form.querySelector('textarea');
    let order = null;

    if (el.modal) {
        order = el.form.querySelector('input[name="order"]');
        if (order && !order.value) {
            showToast(el.danger, i18next.t('notFound'));
            return false;
        }
    }

    await fetch(url, {
        method: "POST",
        body: JSON.stringify({
            message: message.value,
            _token: el.form.querySelector('input[name="_token"]').value,
            product: el.form.querySelector('input[name="product"]').value,
            market: el.form.querySelector('input[name="market"]').value,
            order: order ? order.value : order
        }),
        headers: {'Content-type': 'application/json; charset=utf-8'}
    })
        .then((response) => response.json())
        .then((json) => {
            const response = json;
            if (response.success === false) {
                showToast(el.danger, response.error);
            }
            if (response.success === true) {
                showToast(el.success, response.message);
                if (el.modal) {
                    if (order) {
                        order.value = null;
                        el.form.querySelector('input[type="search"]').value = null;
                    }
                    el.modal.querySelector('button[data-modal-hide="modal-message"]').click();
                }
            }
            message.value = null;
        }).catch(err => {
            console.log(err);
        });
});

if (el.search) {
    const icon = el.search.previousElementSibling.previousElementSibling.children[0];
    const input = el.form.querySelector('input[type="search"]');
    document.getElementById('open-message').onclick = function () {
        icon.classList.replace('text-green-500', 'text-gray-500');
    }
    el.search.addEventListener('click', async () => {
        const url = el.search.getAttribute('data-url');
        await fetch(url, {
            method: 'POST',
            body: JSON.stringify({query: input.value}),
            headers: {'Content-type': 'application/json; charset=utf-8'}
        }).then((response) => response.json()).then((json) => {
            const response = json;
            if (response.order) {
                icon.classList.replace('text-gray-500', 'text-green-500');
                el.form.querySelector('input[name="market"]').value = response.order.market;
                el.form.querySelector('input[name="order"]').value = response.order.id
            }
        }).catch(err => {
            console.log(err);
        });
    });
}