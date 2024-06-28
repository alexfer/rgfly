import {Chart, Colors} from "chart.js";

Chart.register(Colors);

const dashboard = {
    chartOrders: document.getElementById('chart-orders'),
    chartCustomers: document.getElementById('chart-customers'),
    chartInvoices: document.getElementById('chart-invoices'),
};

const dataOrders = dashboard.chartOrders.getAttribute('data-orders');
const dataO = [100 - dataOrders, dataOrders];

const dataCustomers = dashboard.chartCustomers.getAttribute('data-customers');
const dataC = [100 - dataCustomers, dataCustomers];

const dataInvoices = dashboard.chartInvoices.getAttribute('data-invoices');
const dataI = [100 - dataInvoices, dataInvoices];

let chartOrders = new Chart(dashboard.chartOrders, {
    type: 'doughnut',
    data: {
        datasets: [{
            label: ' ',
            backgroundColor: [
                'rgba(11,142,11,0.8)',
                'rgba(11,18,142,0.8)',
            ],
            clip: 2,
            data: dataO,
            borderWidth: 3
        }]
    }
});

let chartCustomers = new Chart(dashboard.chartCustomers, {
    type: 'doughnut',
    data: {
        datasets: [{
            label: ' ',
            backgroundColor: [
                'rgba(197,43,97,0.8)',
                'rgba(11,142,33,0.8)',
            ],
            data: dataC,
            borderWidth: 2
        }]
    }
});

let chartInvoices = new Chart(dashboard.chartInvoices, {
    type: 'doughnut',
    data: {
        datasets: [{
            label: ' ',
            backgroundColor: [
                'rgba(255,168,6,0.8)',
                'rgba(189,27,71,0.8)',
            ],
            data: dataI,
            borderWidth: 2
        }]
    }
});


