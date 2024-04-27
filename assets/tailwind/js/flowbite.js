import {Dismiss, initFlowbite} from "flowbite";

initFlowbite();

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

clipboardPhone.updateOnCopyCallback((clipboard) => {
    showSuccess(tooltipPhone, defaultIconPhone, successIconPhone, defaultTooltipMessagePhone, successTooltipMessagePhone);
    setTimeout(() => {
        resetToDefault(tooltipPhone, defaultIconPhone, successIconPhone, defaultTooltipMessagePhone, successTooltipMessagePhone);
    }, 5000);
});

clipboardEmail.updateOnCopyCallback((clipboard) => {
    showSuccess(tooltipEmail, defaultIconEmail, successIconEmail, defaultTooltipMessageEmail, successTooltipMessageEmail);
    setTimeout(() => {
        resetToDefault(tooltipEmail, defaultIconEmail, successIconEmail, defaultTooltipMessageEmail, successTooltipMessageEmail);
    }, 5000);
});

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
