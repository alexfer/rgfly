import Swal from "sweetalert2";
import i18next from "i18next";
import customCss  from "../customCss";

let selector = document.getElementById('store');
const customers = document.querySelectorAll('.customer');
const lock = document.querySelector('.lock');

lock.addEventListener('click', (e) => {
    const url = lock.dataset.url;
    Swal.fire({
        text: i18next.t('confirmLock'),
        showCancelButton: true,
        showConfirmButton: true,
        confirmButtonText: i18next.t('proceed'),
        denyButtonText: i18next.t('cancel'),
        customClass: customCss,
        icon: "question",
        input: "date",
        didOpen: () => {
            const today = new Date();
            const newDate = new Date(today.setMonth(today.getMonth() + 1)).toISOString();
            Swal.getInput().value = newDate.split("T")[0];
        }
    }).then(async (result) => {
        if (result.isConfirmed) {
            const response = await fetch(url, {
                method: 'POST',
                body: JSON.stringify({
                    id: lock.dataset.id,
                    date: result.value
                })
            });
            const data = await response.json();

            if(data.message !== undefined) {
                Swal.fire({
                    icon: "error",
                    text: data.message,
                    customClass: customCss
                });
            } else {
                const tr = document.getElementById('blog-' + data.entry);
                tr.classList.remove('bg-white');
                tr.classList.toggle('bg-red-100');
                lock.classList.add('pointer-events-none');
            }
        }
    });
});

if (customers !== undefined) {
    [...customers].forEach((el) => {
        const url = el.dataset.url;

        el.addEventListener('click', (e) => {
            fetch(url, {'Content-type': 'application/json'})
                .then((response) => response.json())
                .then((json) => {
                    if (json.customer.length) {
                        const customer = json.customer.shift();
                        const body = document.getElementById('modal-body');
                        for (const [key, value] of Object.entries(customer)) {
                            body.querySelectorAll('dd').forEach((el) => {
                                if (el.classList.contains(key)) {
                                    if (value === null) {
                                        el.parentElement.remove();
                                    } else {
                                        el.innerHTML = value.toString();
                                    }
                                }
                            });
                        }
                    }
                });
        });
    });
}

if (selector !== null) {
    selector = selector.querySelector('select');
    selector.addEventListener('change', () => window.location.replace(selector.value));
}