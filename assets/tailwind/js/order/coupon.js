const verify = document.getElementById('verify');

window.onload = function () {
    const focusNextInput = (el, prevId, nextId) => {
        if (el.value.length === 0) {
            if (prevId) {
                document.getElementById(prevId).focus();
            }
        } else {
            if (nextId) {
                document.getElementById(nextId).focus();
            }
        }
    }
    document.querySelectorAll('[data-focus-input-init]').forEach(function (element) {
        element.addEventListener('keyup', function () {
            const prevId = this.getAttribute('data-focus-input-prev');
            const nextId = this.getAttribute('data-focus-input-next');
            focusNextInput(this, prevId, nextId);
        });
    });
}

if (verify !== null) {
    verify.addEventListener('click', () => {
        const url = verify.getAttribute('data-url');
        const container = verify.closest('ul');
        const inputs = container.querySelectorAll('input');
        const symbols = [];

        [...inputs].forEach((input) => input.value ? symbols.push(input.value) : null);

        if (symbols.length === inputs.length) {
            let success = document.getElementById('coupon-success');
            let warning = document.getElementById('coupon-warning');
            let danger = document.getElementById('coupon-danger');
            let discount = document.getElementById('discount');

            fetch(url, {
                method: 'POST',
                body: JSON.stringify({ids: symbols}),
                headers: {'Content-type': 'application/json; charset=utf-8'}
            })
                .then((response) => response.json()
                    .then(json => (({
                        status: response.status,
                        data: json
                    }))))
                .then(result => {
                    switch (result.status) {
                        case 400:
                            warning.classList.remove('hidden');
                            warning.innerHTML = result.data.message;
                            break;
                        case 403:
                            danger.classList.remove('hidden');
                            danger.innerHTML = result.data.message;
                            break;
                        case 200:
                            discount.parentElement.classList.remove('hidden');
                            discount.textContent = result.data.discount;
                            success.classList.remove('hidden');
                            success.innerHTML = result.data.message;
                            container.remove();
                            break;
                    }
                    setTimeout(() => {
                        if (!danger.classList.contains('hidden')) {
                            danger.classList.toggle('hidden');
                        }
                        if (!warning.classList.contains('hidden')) {
                            warning.classList.toggle('hidden');
                        }
                        [...inputs].forEach((input) => input.value = null);
                    }, 2000);
                });
        }

    });
}