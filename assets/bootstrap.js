// assets/bootstrap.js
import { startStimulusApp } from '@symfony/stimulus-bridge';
import * as bootstrap from 'bootstrap';

// Registers Stimulus controllers from controllers.json and in the controllers/ directory
export const app = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.(j|t)sx?$/
));

// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);
let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
let tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
});
let toastTrigger = document.getElementById('liveToastBtn');
let toastLiveExample = document.getElementById('liveToast');
if (toastTrigger) {
    toastTrigger.addEventListener('click', function () {
        let toast = new bootstrap.Toast(toastLiveExample);

        toast.show()
    })
}
