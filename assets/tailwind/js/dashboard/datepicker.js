import Datepicker from 'flowbite-datepicker/Datepicker';

const element = document.getElementById('period-date');
const year = new Date().getFullYear();
const month = new Date().getMonth() + 1;

class FlowbiteDatepicker extends Datepicker {
    constructor(element, options = {}, rangepicker = undefined) {
        super(element, options, rangepicker);
        this.options = options;
    }

    setDate(...args) {
        super.setDate(args);
        if (this.options.hasOwnProperty('onSelect')) {
            this.options.onSelect(this);
        }
    }
}

new FlowbiteDatepicker(element, {
    maxDate: Date.UTC(year, month),
    minDate: Date.UTC(year, 0),
    format: 'yyyy-MM-dd',
    onSelect(instant) {
        // Triggered when date is selected
        let date = new Date(instant.dates[0]).toLocaleDateString();
        let url = element.getAttribute('data-url');
        date = date.split('.');
        date = date.reverse().join('-');
        console.log(url + '/' + date);
        window.history.replaceState({}, '', url + '/' + date);
    }
});
