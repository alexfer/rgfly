import { Dismiss } from "flowbite";

const toast= document.getElementById('toast-success');
const toastClose= document.querySelector('[data-dismiss-target="#toast-success"]');

if(toast !== undefined) {
    const options = {
        transition: 'transition-opacity',
        duration: 4000,
        timing: 'ease-out',

        onHide: (toast, toastClose) => {
            console.log(toastClose, toast)
        }
    };

    const instanceOptions = {
        id: 'targetElement',
        override: true
    };
    const dismiss = new Dismiss(toast, toastClose, options, instanceOptions);

}