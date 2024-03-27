import 'flowbite';
import {Dropdown, initTWE, Offcanvas, Ripple} from "tw-elements";

initTWE({Offcanvas, Ripple, Dropdown});


const modal = new Modal(document.getElementById('modal'), {});
modal.hide();