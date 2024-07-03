import {Dismiss, initFlowbite} from "flowbite";
import {Datepicker} from 'flowbite-datepicker';


initFlowbite();

const datepicker = document.getElementById('period-date');
const year = new Date().getFullYear();
const month = new Date().getMonth() + 1;

new Datepicker(datepicker, {
    maxDate: Date.UTC(year, month),
    minDate: Date.UTC(year, 0),
    format: 'yyyy-mm-dd',
    //inline: true
});

window.onload = function () {
    // const grid = datepicker.querySelector('.datepicker-grid');
    // grid.querySelectorAll('span').forEach(span => {
    //     span.addEventListener('click', function () {
    //         let time = span.previousElementSibling.getAttribute('data-date');
    //
    //         let value = span.textContent;
    //         console.log(new Date(time / value / 42).getDate());
    //     })
    // });
}

const toast = document.getElementById('toast-success');
const toastClose = document.querySelector('[data-dismiss-target="#toast-success"]');

if (toast !== undefined) {
    const options = {
        transition: 'transition-opacity',
        duration: 4000,
        timing: 'ease-out'
    };
    const instanceOptions = {
        id: 'targetElement',
        override: true
    };
    const dismiss = new Dismiss(toast, toastClose, options, instanceOptions);
}

const clipboardEmail = FlowbiteInstances.getInstance('CopyClipboard', 'email');
const tooltipEmail = FlowbiteInstances.getInstance('Tooltip', 'tooltip-copy-email');

const clipboardPhone = FlowbiteInstances.getInstance('CopyClipboard', 'phone');
const tooltipPhone = FlowbiteInstances.getInstance('Tooltip', 'tooltip-copy-phone');

const defaultIconEmail = document.getElementById('default-icon-email');
const successIconEmail = document.getElementById('success-icon-email');

const defaultIconPhone = document.getElementById('default-icon-phone');
const successIconPhone = document.getElementById('success-icon-phone');

const defaultTooltipMessageEmail = document.getElementById('default-tooltip-message-email');
const successTooltipMessageEmail = document.getElementById('success-tooltip-message-email');

const defaultTooltipMessagePhone = document.getElementById('default-tooltip-message-phone');
const successTooltipMessagePhone = document.getElementById('success-tooltip-message-phone');

if (clipboardPhone !== undefined) {
    clipboardPhone.updateOnCopyCallback((clipboard) => {
        showSuccess(tooltipPhone, defaultIconPhone, successIconPhone, defaultTooltipMessagePhone, successTooltipMessagePhone);
        setTimeout(() => {
            resetToDefault(tooltipPhone, defaultIconPhone, successIconPhone, defaultTooltipMessagePhone, successTooltipMessagePhone);
        }, 5000);
    });
}

if (clipboardEmail !== undefined) {
    clipboardEmail.updateOnCopyCallback((clipboard) => {
        showSuccess(tooltipEmail, defaultIconEmail, successIconEmail, defaultTooltipMessageEmail, successTooltipMessageEmail);
        setTimeout(() => {
            resetToDefault(tooltipEmail, defaultIconEmail, successIconEmail, defaultTooltipMessageEmail, successTooltipMessageEmail);
        }, 5000);
    });
}

const showSuccess = (tooltip, defaultIcon, successIcon, defaultMessage, successMessage) => {
    defaultIcon.classList.add('hidden');
    successIcon.classList.remove('hidden');
    defaultMessage.classList.add('hidden');
    successMessage.classList.remove('hidden');
    tooltip.show();
}

const resetToDefault = (tooltip, defaultIcon, successIcon, defaultMessage, successMessage) => {
    defaultIcon.classList.remove('hidden');
    successIcon.classList.add('hidden');
    defaultMessage.classList.remove('hidden');
    successMessage.classList.add('hidden');
    tooltip.hide();
}