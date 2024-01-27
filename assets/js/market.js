'use strict';

let attributes = document.querySelectorAll('#attributes');
let form = document.querySelector('#cart');
const url = form.getAttribute('action');

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

form.addEventListener('submit', (event) => {
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
    }).then((response) => response.json())
        .then((json) => {
            let qty = document.getElementById('qty');
            qty.textContent = json.quantity;
            console.log(json);
        });
    event.preventDefault();
}, true);