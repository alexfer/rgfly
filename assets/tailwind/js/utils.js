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