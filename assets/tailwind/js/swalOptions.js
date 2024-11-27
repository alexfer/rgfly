import i18next from "i18next";

const swalOptions = {
    title: i18next.t('confirm'),
    text: i18next.t('question'),
    //icon: 'warning',
    confirmButtonText: i18next.t('proceed'),
    confirmButtonColor: 'red',
    cancelButtonText: i18next.t('cancel'),
    showCancelButton: true,
    showClass: {
        popup: `
      animate__animated
      animate__fadeInLeftBig
      animate__faster
    `
    },
    hideClass: {
        popup: `
      animate__animated
      animate__fadeOutRightBig
      animate__faster
    `
    },
    customClass: {
        title: 'text-left text-gray-600',
        popup: 'rounded-xl',
        confirmButton: 'rounded-lg bg-red-700',
        cancelButton: 'rounded-lg bg-gray-500',
        input: 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-auto ms-5 p-2.5',
    },
    background: '#ffffff',
};

export default swalOptions;