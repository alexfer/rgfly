'use strict';

const cart = document.getElementById('shopping-cart') || undefined;
let attributes = document.querySelectorAll('#attributes');
let forms = document.querySelectorAll('.shopping-cart') || undefined;

if (typeof cart !== undefined) {
    cart.addEventListener('show.bs.offcanvas', (event) => {
        let url = cart.getAttribute('data-url');
        let body = cart.querySelectorAll('#order-body');
        body.item(0).innerHTML = '';
        fetch(url, {
            headers: {
                'Content-type': 'application/json; charset=utf-8'
            }
        })
            .then((response) => response.json())
            .then((json) => {
                body.item(0).innerHTML = json.template;
                if (parseInt(json.quantity) !== 0) {
                    body.item(0).children[0].classList.remove('visually-hidden');
                } else {
                    body.item(0).children[0].classList.toggle('visually-hidden');
                }
            })
            .catch(err => {
                console.log(err);
            });
    });
}

if (attributes.length) {
    Array.from(attributes).forEach((el) => {
        if (el.children !== null) {
            Array.from(el.children).forEach((item) => {
                if (item.hasAttribute('id')) {
                    Array.from(item.children).forEach((input) => {
                        input.addEventListener("change", (event) => {
                            if (event.currentTarget.checked) {
                                let root = input.getAttribute('data-root-name');
                                let value = input.getAttribute('data-name');
                                el.querySelector('input[name="' + root + '"]').value = value;
                                input.parentElement.previousElementSibling.innerHTML = value;
                            }
                        });
                    });
                }
            });
        }
    });
}

if (typeof forms != 'undefined') {
    Array.from(forms).forEach((form) => {
        const url = form.getAttribute('action');
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            let color = form.querySelector('input[name="color"]');
            let size = form.querySelector('input[name="size"]');
            fetch(url, {
                method: 'POST',
                body: JSON.stringify({
                    quantity: 1,
                    color: color ? color.value : null,
                    size: size ? size.value : null,
                }),
                headers: {
                    'Content-type': 'application/json; charset=utf-8'
                }
            })
                .then((response) => response.json())
                .then((json) => {
                    let qty = document.getElementById('qty');
                    qty.innerHTML = json.quantity;
                })
                .catch(err => {
                    console.log(err);
                });
        }, true);
    });
}