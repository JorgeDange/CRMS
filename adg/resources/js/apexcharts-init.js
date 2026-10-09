/* =========================================================================
   ADG — gráficos ApexCharts (prototype index.html + analytics.html)
   Prototipo usa ApexCharts inline; aqui inicializamos via data attributes
   nos <div id="..."> para separar lógica de apresentação.
   ========================================================================= */

import ApexCharts from 'apexcharts';

// ============================================================
// Helpers
// ============================================================

function parseChartData(el) {
    const attr = el.dataset.adgChart;
    if (!attr) return null;
    try {
        return JSON.parse(attr);
    } catch {
        return null;
    }
}

function formatKz(value) {
    return new Intl.NumberFormat('pt-PT').format(value);
}

// Payloads do projeto chegam de 2 formas:
//  - DashboardService.dashboardData(): [{name, data:[...]}]  (já formato Apex)
//  - DashboardService.analyticsData():  [n1, n2, ...]        (valores soltos)
// Normaliza para [{name, data}] — sem isto o Apex recebia data=[objeto]
// (1 ponto) e desenhava um gráfico vazio.
function toSeries(series, fallbackName) {
    if (
        Array.isArray(series) &&
        series.length > 0 &&
        typeof series[0] === 'object' &&
        series[0] !== null &&
        Array.isArray(series[0].data)
    ) {
        return series;
    }
    return [{ name: fallbackName, data: Array.isArray(series) ? series : [] }];
}

// ============================================================
// Chart configs baseados no prototype (analytics.html + index.html)
// ============================================================

function salesAreaConfig(payload) {
    return {
        chart: {
            type: 'area',
            height: 300,
            toolbar: { show: false },
            fontFamily: 'Inter, sans-serif',
        },
        series: toSeries(payload.series, 'Vendas'),
        xaxis: { categories: payload.labels },
        colors: [payload.color || '#3454d1'],
        yaxis: {
            labels: {
                formatter: (val) => {
                    if (Math.abs(val) >= 1_000_000) return `${(val / 1_000_000).toFixed(0)}M`;
                    if (Math.abs(val) >= 1_000) return `${(val / 1_000).toFixed(0)}K`;
                    return val;
                },
            },
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.45,
                opacityTo: 0.05,
                stops: [0, 90, 100],
            },
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 3 },
        grid: {
            borderColor: '#eef0f3',
            strokeDashArray: 4,
        },
        tooltip: {
            theme: 'dark',
            y: { formatter: (val) => `Kz ${formatKz(val)}` },
        },
        legend: { show: false },
    };
}

function donutConfig(payload) {
    return {
        chart: { type: 'donut', height: 300 },
        series: payload.series,
        labels: payload.labels,
        colors: payload.colors,
        legend: {
            position: 'bottom',
            fontSize: '13px',
            labels: { colors: '#64748b' },
        },
        dataLabels: {
            enabled: true,
            // Apex 7.x já passa o valor em PERCENTAGEM (fatia/total*100)
            // no formatter — dividir outra vez pelo total dava 741%/123%.
            formatter: (val) => `${Math.round(val ?? 0)}%`,
            style: { fontSize: '12px', fontFamily: 'Inter, sans-serif', colors: ['#fff'] },
            dropShadow: { enabled: false },
        },
        stroke: { show: false },
        tooltip: {
            theme: 'dark',
            y: { formatter: (val) => formatKz(val) },
        },
        plotOptions: {
            pie: {
                donut: { size: '65%' },
            },
        },
    };
}

function smallAreaConfig(payload) {
    return {
        chart: { type: 'area', height: 120, sparkline: { enabled: true }, toolbar: { show: false } },
        series: toSeries(payload.series, payload.name || 'Dados'),
        xaxis: { categories: payload.labels, labels: { show: false }, axisBorder: { show: false }, axisTicks: { show: false } },
        yaxis: { show: false },
        colors: [payload.color || '#3454d1'],
        fill: {
            type: 'gradient',
            gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05 },
        },
        stroke: { curve: 'smooth', width: 2 },
        tooltip: { theme: 'dark', x: { show: false } },
    };
}

// ============================================================
// Render function — lê data-adg-chart dos divs e inicializa
// ============================================================

const chartInstances = new Map();

function renderCharts() {
    // Limpa charts órfãos (Livewire navigation)
    for (const [id, chart] of chartInstances) {
        if (!document.getElementById(id)) {
            chart.destroy();
            chartInstances.delete(id);
        }
    }

    document.querySelectorAll('[data-adg-chart]').forEach((el) => {
        const payload = parseChartData(el);
        if (!payload || !payload.type) return;

        const previous = chartInstances.get(el.id);
        if (previous) previous.destroy();

        let chart;
        if (payload.type === 'area') {
            chart = new ApexCharts(el, salesAreaConfig(payload));
        } else if (payload.type === 'donut') {
            chart = new ApexCharts(el, donutConfig(payload));
        } else if (payload.type === 'small-area') {
            chart = new ApexCharts(el, smallAreaConfig(payload));
        } else {
            return;
        }

        chart.render();
        chartInstances.set(el.id, chart);
    });
}

// ============================================================
// Small area charts (taskCompleted, newTasks, projectDone)
// Estes não usam data attribute — inicializam com dados inline
// passados via window.adgSmallCharts (definido no blade)
// ============================================================

function renderSmallCharts() {
    const data = window.adgSmallCharts;
    if (!data) return;

    // Task Completed
    const tcEl = document.getElementById('taskCompletedChart');
    if (tcEl && data.taskCompleted) {
        const prev = chartInstances.get('taskCompletedChart');
        if (prev) prev.destroy();
        const chart = new ApexCharts(tcEl, smallAreaConfig({
            name: 'Concluídas',
            series: data.taskCompleted.series,
            labels: data.taskCompleted.labels,
            color: '#3454d1',
        }));
        chart.render();
        chartInstances.set('taskCompletedChart', chart);
    }

    // New Tasks
    const ntEl = document.getElementById('newTasksChart');
    if (ntEl && data.newTasks) {
        const prev = chartInstances.get('newTasksChart');
        if (prev) prev.destroy();
        const chart = new ApexCharts(ntEl, smallAreaConfig({
            name: 'Novas',
            series: data.newTasks.series,
            labels: data.newTasks.labels,
            color: '#10b981',
        }));
        chart.render();
        chartInstances.set('newTasksChart', chart);
    }

    // Project Done
    const pdEl = document.getElementById('projectDoneChart');
    if (pdEl && data.projectDone) {
        const prev = chartInstances.get('projectDoneChart');
        if (prev) prev.destroy();
        const chart = new ApexCharts(pdEl, smallAreaConfig({
            name: 'Concluídos',
            series: data.projectDone.series,
            labels: data.projectDone.labels,
            color: '#f43f5e',
        }));
        chart.render();
        chartInstances.set('projectDoneChart', chart);
    }
}

// ============================================================
// Boot
// ============================================================

function boot() {
    renderCharts();
    renderSmallCharts();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
} else {
    boot();
}

document.addEventListener('livewire:navigated', () => {
    boot();
});

// ============================================================
// Loading overlay — esconde quando a app estiver pronta
// ============================================================
function hideLoadingOverlay() {
    const overlay = document.getElementById('app-loading');
    if (overlay) {
        overlay.classList.add('hidden');
        // remove do DOM depois da transicao
        setTimeout(() => overlay.remove(), 300);
    }
}

// Esconder no DOMContentLoaded (fallback)
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', hideLoadingOverlay);
} else {
    hideLoadingOverlay();
}

// Esconder apos Livewire hidratar (navegacoes wire:navigate)
document.addEventListener('livewire:initialized', hideLoadingOverlay);
document.addEventListener('livewire:navigated', hideLoadingOverlay);

// Export para debug
window.adgCharts = { renderCharts, renderSmallCharts, chartInstances };