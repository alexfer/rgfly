import Swal from "sweetalert2";
import i18next from "i18next";
import messages from "./i18n";

i18next.init(messages);

const cart = document.getElementById('shopping-cart');
const attributes = document.querySelectorAll('#attributes');
const forms = document.querySelectorAll('.shopping-cart');
const wishlists = document.querySelectorAll('.add-wishlist');
const drops = document.querySelectorAll('.drops');
const headers = {'Content-type': 'application/json; charset=utf-8'};
const bulkRemoveWishlist = document.getElementById('bulk-remove');

if (bulkRemoveWishlist !== null) {
    bulkRemoveWishlist.addEventListener('click', (event) => {
        event.preventDefault();
        let url = bulkRemoveWishlist.getAttribute('data-url');
        let tbody = document.getElementById('bulk-item');
        let items = [];
        let els = [];
        Array.from(tbody.children).forEach((el) => {
            let children = el.firstElementChild.getElementsByTagName('input');
            Array.from(children).forEach((checkbox) => {
                if (checkbox.checked) {
                    items.push(checkbox.value);
                    els.push(el);
                }
            });
        });
        if (els.length > 0) {
            fetch(url, {
                method: 'POST',
                body: JSON.stringify({items: items}),
                headers: headers
            })
                .then((response) => {
                    if (response.status === 204) {
                        for (let i in els) {
                            els[i].remove();
                        }
                        if (tbody.children.length === 0) {
                            tbody.parentElement.parentElement.remove();
                            bulkRemoveWishlist.remove();
                            let notFound = document.getElementById('not-found');
                            notFound.classList.remove('visually-hidden');
                        }
                    }
                })
                .catch(error => {
                    console.log(error);
                });
        }
    });
}

if (typeof drops !== 'undefined') {
    Array.from(drops).forEach((drop) => {
        drop.onclick = (event) => {
            event.preventDefault();
            let url = drop.getAttribute('data-url');
            let product = drop.getAttribute('data-id');
            let order = drop.getAttribute('data-order');
            let market = drop.getAttribute('data-market');
            let quantity = document.getElementById('qty');
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
                                order: order,
                                market: market
                            }),
                            headers: headers
                        }
                    );
                    const data = await response.json();
                    const summary = data.summary;
                    summary.map((item, key) => {
                        let total = document.getElementById('total-' + item.market);
                        let checkout = document.getElementById('checkout-' + item.market);
                        let itemSubtotal = document.getElementById('item-subtotal-' + item.market);
                        let currency = '<small>' + item.currency + '</small>';

                        checkout.innerHTML = item.total + currency;
                        itemSubtotal.innerHTML = item.total + currency;
                        total.innerHTML = item.total + currency;
                    });

                    quantity.innerHTML = data.quantity;

                    if (data.products >= 1) {
                        drop.closest('.parent').remove();
                    }
                    if (data.products === 0) {
                        drop.closest('.root').remove();
                        document.getElementById('market-' + data.removed).remove();
                    }
                    if (data.redirect && data.order) {
                        document.location.href = data.redirect;
                    } else {
                        await Swal.fire(i18next.t('removed'), "", "success");
                    }
                }
            });
        };
    });
}

if (cart !== null) {
    cart.addEventListener('show.twe.offcanvas', () => {
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
                    Array.from(item.children).forEach((wrapper, index) => {
                        Array.from(wrapper.children).forEach((input, index) => {
                            input.addEventListener("change", (event) => {
                                if (event.currentTarget.checked) {
                                    let root = input.getAttribute('data-root-name');
                                    let value = input.getAttribute('data-name');
                                    el.querySelector('input[name="' + root + '"]').value = value;
                                    input.parentElement.parentElement.previousElementSibling.innerHTML = value;
                                }
                            });
                        });
                    });
                }
            });
        }
    });
}

if (typeof wishlists != 'undefined') {
    Array.from(wishlists).forEach((form) => {
        const url = form.getAttribute('action');
        let market = form.querySelector('input[name="market"]');
        let button = form.querySelector('button[type="submit"]');
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            fetch(url, {
                method: 'POST',
                body: JSON.stringify({market: market.value})
            })
                .then((response) => {
                    if (response.status === 401) {
                        return false;
                    }
                    button.children.item(0).classList.replace('text-secondary', 'text-danger');
                    button.children.item(0).classList.replace('bi-heart', 'bi-heart-fill');
                    button.disabled = true;
                    return response.json();
                })
                .catch(err => {
                    console.log(err);
                });
        });
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
                    if (qty) {
                        qty.innerHTML = json.quantity;
                    }
                })
                .catch(err => {
                    console.log(err);
                });
        }, true);
    });
}