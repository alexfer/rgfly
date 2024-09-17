const inputs = document.querySelectorAll('.form-input');
[...inputs].forEach((el, index) => {
    const input = el.querySelector('input[type="text"]');
    if (input !== null) {
        const counter = el.querySelector('.counter');
        const maxLength = input.getAttribute('maxlength');
        counter.innerHTML = `${maxLength}/${maxLength}`;

        input.addEventListener('keyup', (e) => {
            counter.innerHTML = `${parseFloat(maxLength) - e.target.value.length}/${maxLength}`;
        });
    }
});