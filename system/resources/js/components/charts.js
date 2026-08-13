/**
 * Chart Component using ApexCharts
 * @module components/charts
 */

import ApexCharts from "apexcharts";

// Expose globally so widget chart partials can use it
window.ApexCharts = ApexCharts;

export function getChartTheme() {
  const isDark = document.documentElement.classList.contains("dark");
  return {
    text: isDark ? "#8e99a4" : "#717f8e",
    grid: isDark ? "#2d3339" : "#e3e6e8",
    card: isDark ? "#101214" : "#ffffff",
    tooltip: isDark ? "#17191c" : "#ffffff",
    tooltipBorder: isDark ? "#2d3339" : "#e3e6e8",
  };
}

let revenueChart, categoryChart;

/**
 * Initializes and renders the application charts.
 */
export function initCharts() {
  if (typeof ApexCharts === "undefined") return;

  const revenueEl = document.querySelector("#revenueChart");
  const categoryEl = document.querySelector("#categoryChart");
  if (!revenueEl || !categoryEl) return;

  const t = getChartTheme();
  if (revenueChart) revenueChart.destroy();
  if (categoryChart) categoryChart.destroy();

  revenueChart = new ApexCharts(revenueEl, {
    chart: {
      type: "area",
      height: 300,
      fontFamily: "Inter, sans-serif",
      toolbar: { show: false },
      background: "transparent",
    },
    series: [
      {
        name: "Revenue",
        data: [
          18200, 22400, 19800, 27600, 24300, 31200, 28900, 35100, 32400, 38700,
          41200, 48295,
        ],
      },
      {
        name: "Expenses",
        data: [
          12100, 14200, 13400, 16800, 15600, 19200, 17800, 21400, 19600, 23100,
          25400, 28100,
        ],
      },
    ],
    colors: ["#5096f2", "#6366f1"],
    fill: {
      type: "gradient",
      gradient: {
        shadeIntensity: 1,
        opacityFrom: 0.25,
        opacityTo: 0.02,
        stops: [0, 100],
      },
    },
    stroke: { width: 2.5, curve: "smooth" },
    dataLabels: { enabled: false },
    xaxis: {
      categories: [
        "Jan",
        "Feb",
        "Mar",
        "Apr",
        "May",
        "Jun",
        "Jul",
        "Aug",
        "Sep",
        "Oct",
        "Nov",
        "Dec",
      ],
      axisBorder: { show: false },
      axisTicks: { show: false },
      labels: { style: { colors: t.text, fontSize: "12px" } },
    },
    yaxis: {
      labels: {
        style: { colors: t.text, fontSize: "12px" },
        formatter: (v) => "$" + (v / 1000).toFixed(0) + "k",
      },
    },
    grid: {
      borderColor: t.grid,
      strokeDashArray: 4,
      padding: { left: 8, right: 8 },
    },
    tooltip: {
      theme: false,
      custom: function ({ series, dataPointIndex, w }) {
        const rev = series[0][dataPointIndex];
        const exp = series[1][dataPointIndex];
        const month = w.globals.categoryLabels[dataPointIndex];
        return `<div style="background:${t.tooltip};border:1px solid ${t.tooltipBorder};border-radius:8px;padding:10px 14px;box-shadow:0 4px 16px rgba(0,0,0,0.12);">
          <div style="font-weight:600;color:${t.text};margin-bottom:6px;font-size:12px;">${month}</div>
          <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;"><span style="width:8px;height:8px;border-radius:50%;background:#5096f2;display:inline-block;"></span><span style="color:${t.text};font-size:12px;">Revenue: <b>$${rev.toLocaleString()}</b></span></div>
          <div style="display:flex;align-items:center;gap:6px;"><span style="width:8px;height:8px;border-radius:50%;background:#6366f1;display:inline-block;"></span><span style="color:${t.text};font-size:12px;">Expenses: <b>$${exp.toLocaleString()}</b></span></div>
        </div>`;
      },
    },
    legend: {
      show: true,
      position: "top",
      horizontalAlign: "right",
      labels: { colors: t.text },
      fontSize: "12px",
      markers: { size: 4 },
    },
  });
  revenueChart.render();

  categoryChart = new ApexCharts(categoryEl, {
    chart: {
      type: "donut",
      height: 240,
      fontFamily: "Inter, sans-serif",
      background: "transparent",
    },
    series: [42, 28, 18, 12],
    labels: ["Electronics", "Clothing", "Furniture", "Others"],
    colors: ["#5096f2", "#6366f1", "#22c55e", "#f59e0b"],
    stroke: { width: 3, colors: [t.card] },
    plotOptions: {
      pie: {
        donut: {
          size: "72%",
          labels: {
            show: true,
            name: {
              show: true,
              color: t.text,
              fontSize: "13px",
              offsetY: -4,
            },
            value: {
              show: true,
              color: t.text,
              fontSize: "20px",
              fontWeight: 700,
              offsetY: 4,
              formatter: (v) => v + "%",
            },
            total: {
              show: true,
              label: "Total Sales",
              color: t.text,
              fontSize: "12px",
              formatter: () => "2,842",
            },
          },
        },
      },
    },
    dataLabels: { enabled: false },
    legend: { show: false },
    tooltip: {
      enabled: true,
      fillSeriesColor: false,
      theme: false,
      custom: function ({ series, seriesIndex, w }) {
        return `<div style="background:${t.tooltip};border:1px solid ${t.tooltipBorder};border-radius:8px;padding:8px 12px;box-shadow:0 4px 12px rgba(0,0,0,0.1);"><span style="color:${t.text};font-size:12px;">${w.globals.labels[seriesIndex]}: <b>${series[seriesIndex]}%</b></span></div>`;
      },
    },
  });
  categoryChart.render();
}

// Self-initialize when DOM is ready
document.addEventListener("DOMContentLoaded", () => {
  initCharts();
});
