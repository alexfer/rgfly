'use strict';

import Swal from "sweetalert2";
import i18next from "i18next";
import messages from "./i18n";

i18next.init(messages);

const cart = document.getElementById('shopping-cart') || undefined;
let attributes = document.querySelectorAll('#attributes');
let forms = document.querySelectorAll('.shopping-cart') || undefined;
let drops = document.querySelectorAll('.drops') || undefined;
let headers = {'Content-type': 'application/json; charset=utf-8'};

if (typeof drops !== 'undefined') {
    Array.from(drops).forEach((drop) => {
        drop.onclick = (event) => {
            let url = drop.getAttribute('data-url');
            let product = drop.getAttribute('data-id');
            let order = drop.getAttribute('data-order');
            Swal.fire({
                text: i18next.t('question'),
                showCancelButton: true,
                confirmButtonText: i18next.t('proceed'),
                denyButtonText: i18next.t('cancel'),
                icon: "question",
                showLoaderOnConfirm: true
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const response = await fetch(url, {
                            method: 'POST',
                            body: JSON.stringify({
                                product: product,
                                order: order
                            }),
                            headers: headers
                        }
                    );
                    const data = await response.json();
                    if (data.order) {
                        drop.closest('.root').remove();
                        if (data.redirect) {
                            document.location.href = data.redirect;
                        }
                    }
                    if (data.product) {
                        drop.closest('.parent').remove();
                    }
                    await Swal.fire(i18next.t('removed'), "", "success");
                }
            });
        };
    });
}

if (typeof cart !== undefined) {
    cart.addEventListener('show.bs.offcanvas', (event) => {
        let url = cart.getAttribute('data-url');
        let body = cart.querySelectorAll('#order-body');
        body.item(0).innerHTML = '';
        fetch(url, {
            headers: headers
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
                headers: headers
            })
                .then((response) => response.json())
                .then((json) => {
                    let qty = document.getElementById('qty');
                    if(qty) {
                        qty.innerHTML = json.quantity;
                    }
                })
                .catch(err => {
                    console.log(err);
                });
        }, true);
    });
}