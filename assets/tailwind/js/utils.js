window.showToast = (toast, message, timeout) => {
    toast.querySelector('.toast-body').innerText = message;
    toast.classList.remove('hidden');
    setTimeout(() => {
        toast.classList.add('hidden');
    }, timeout ? timeout : 5000);
};

window.closeModal = (id) => {
    document.getElementById(id).style.display = 'none';
    document.getElementsByTagName('body')[0].classList.remove('overflow-y-hidden');
};

window.focusNextInput = (el, prevId, nextId) => {
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

const any = document.getElementById('any');
const discount = document.querySelector('[id$="_discount"]');

if (discount) {
    const output = document.querySelector(".discount-output");
    output.textContent = discount.value + '%';

    discount.addEventListener("input", () => {
        output.textContent = discount.value + '%';
    });
}

if (any) {
    any.addEventListener('click', (e) => {
        let chx = false,
            checks = document.querySelectorAll('.checks');
        if (any.checked) {
            chx = true;
        }
        for (let i in checks) {
            if (typeof checks[i] !== "function" && typeof checks[i] !== "number") {
                checks[i].checked = chx;
                checks[i].addEventListener('click', () => {
                    any.checked = false;
                });
            }
        }
    });
}