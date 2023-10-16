import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        this.element.addEventListener('cropperjs:pre-connect', this._onPreConnect);
        this.element.addEventListener('cropperjs:connect', this._onConnect);
    }

    disconnect() {
        // You should always remove listeners when the controller is disconnected to avoid side effects
        this.element.removeEventListener('cropperjs:pre-connect', this._onConnect);
        this.element.removeEventListener('cropperjs:connect', this._onConnect);
    }

    _onPreConnect(event) {
        // The cropper has not yet been created and options can be modified
        console.log(event.detail.options);
        console.log(event.detail.img);
    }

    _onConnect(event) {
        // The cropper was just created and you can access details from the event
        console.log(event.detail.cropper);
        console.log(event.detail.options);
        console.log(event.detail.img);

        // For instance you can listen to additional events
        event.detail.img.addEventListener('cropend', function () {
            // ...
        });
    }
}