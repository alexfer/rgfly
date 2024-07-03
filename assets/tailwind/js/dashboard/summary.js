import {Chart} from "chart.js";

const dashboard = {
    chartOrders: document.getElementById('chart-orders'),
    chartTotal: document.getElementById('chart-total'),
};

const dataOrders = dashboard.chartOrders.getAttribute('data-orders');

const dataChartTotal = dashboard.chartTotal.getAttribute('data-total');
const dataChartProfit = dashboard.chartTotal.getAttribute('data-profit');

let orders = new Chart(dashboard.chartOrders, {
    type: 'doughnut',
    data: {
        datasets: [{
            label: ' orders',
            backgroundColor: [
                'rgba(11,142,11,0.8)',
                'rgba(11,18,142,0.8)',
            ],
            clip: 2,
            data: [100 - dataOrders, dataOrders],
            borderWidth: 2
        }]
    }
});

let total = new Chart(dashboard.chartTotal, {
    type: 'pie',
    data: {
        datasets: [{
            label: ' total',
            backgroundColor: [
                'rgba(255,168,6,0.8)',
                'rgba(189,27,71,0.8)',
            ],
            data: [dataChartProfit - dataChartTotal, dataChartTotal],
            borderWidth: 2
        }]
    }
});


