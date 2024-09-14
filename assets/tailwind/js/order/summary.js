import Swal from "sweetalert2";
import i18next from "i18next";
import customCss from "../customCss";

const drops = document.querySelectorAll('.drops');
const orderForm = document.getElementById('order-summary');

if (typeof drops !== 'undefined') {
    Array.from(drops).forEach((drop) => {
        drop.onclick = (event) => {
            event.preventDefault();
            let url = drop.getAttribute('data-url');
            let product = drop.getAttribute('data-id');
            let order = drop.getAttribute('data-order');
            let store = drop.getAttribute('data-store');
            let quantity = document.getElementById('qty');
            Swal.fire({
                text: i18next.t('question'),
                showCancelButton: true,
                confirmButtonText: i18next.t('proceed'),
                denyButtonText: i18next.t('cancel'),
                customClass: customCss,
                icon: "question",
                showLoaderOnConfirm: true
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const response = await fetch(url, {
                            method: 'POST',
                            body: JSON.stringify({
                                product: product,
                                order: order,
                                store: store
                            }),
                            headers: new Headers({'Content-Type': 'application/json'})
                        }
                    );
                    const data = await response.json();
                    const summary = data.summary;
                    summary.map((item) => {
                        let total = document.getElementById('total-' + item.store);
                        let checkout = document.getElementById('checkout-' + item.store);
                        let itemSubtotal = document.getElementById('item-subtotal-' + item.store);
                        let currency = '<small>' + item.currency + '</small>';

                        checkout.innerHTML = item.total + currency;
                        itemSubtotal.innerHTML = item.total + currency;
                        total.innerHTML = item.total + currency;
                    });

                    quantity.innerHTML = data.store.quantity;

                    if (data.products >= 1) {
                        drop.closest('.parent').remove();
                    }

                    if (data.products === 0) {
                        drop.closest('.root').remove();
                        document.getElementById('store-' + data.removed).remove();
                    }

                    if (data.redirect === true) {
                        orderForm.remove();
                        document.getElementById('redirect').classList.remove('hidden');
                        fetch(data.url, {
                            method: 'OPTIONS', body: JSON.stringify({
                                session: data.session,
                            })
                        }).then((response) => {
                            if (response.status === 200) {
                                document.location.href = data.redirectUrl;
                            }
                        });
                    } else {
                        await Swal.fire({
                            title: i18next.t('removed'),
                            html: "",
                            customClass: customCss,
                            icon: "success"
                        });
                    }
                }
            });
        };
    });
}