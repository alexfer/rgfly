let selector = document.getElementById('store');
const customers = document.querySelectorAll('.customer');

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