window.showToast = (toast, message) => {
    toast.querySelector('.toast-body').innerText = message;
    toast.classList.remove('hidden');
    setTimeout(() => {
        toast.classList.add('hidden');
    }, 4000);
};

window.closeModal = (id) => {
    document.getElementById(id).style.display = 'none';
    document.getElementsByTagName('body')[0].classList.remove('overflow-y-hidden');
};