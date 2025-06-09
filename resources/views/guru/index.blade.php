@extends('layout-admins.app')

@section('content')
    <div class="w-full rounded-bl-xl p-5 pl-8 bg-white">   
        <h1>Halaman Beranda</h1>
        <p class="text-[10px] tracking-[5px] text-slate-200">Ingin apa hari ini</p>
    </div>

    <div class="w-full">
        <div class="mt-10">
            <div class="p-5 px-8 rounded-2xl bg-white relative group">
                <p class="font-bold text-4xl">Halo, {{$user->userable->name}}!</p>
                <i class="bi bi-person-workspace absolute bottom-2 right-3 text-gray-500 opacity-30 text-5xl"></i>
            </div>
        </div>
    </div>

    <div class="flex">
        <div class="flex gap-6 w-full">
            <div class="mt-10 flex-1">
                <div class="p-5 px-8 rounded-2xl bg-white cursor-default">
                    <div class="flex justify-center py-1 text-black">
                        <i class="bi bi-receipt text-5xl mr-10 rounded-xl py-2 px-5"></i>
                        <div class="grid grid-rows-2">
                            <p class="">1</p>
                            <p class="">Jumlah Modul</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-10 flex-1">
                <div class="p-5 px-2 rounded-2xl bg-white cursor-default">
                    <div class="flex justify-center py-1 text-black">
                        <i class="bi bi-activity text-5xl mr-10 rounded-xl py-2 px-5"></i>
                        <div class="grid grid-rows-2">
                            <p class="">1/1</p>
                            <p class="">Pre-Test Dikerjakan</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-10 flex-1">
                <div class="p-5 px-2 rounded-2xl bg-white cursor-default">
                    <div class="flex justify-center py-1 text-black">
                        <i class="bi bi-activity text-5xl mr-10 rounded-xl py-2 px-5"></i>
                        <div class="grid grid-rows-2">
                            <p class="">1/1</p>
                            <p class="">Post-Test Dikerjakan</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-between gap-6 w-full">
        <div class="mt-10 flex-1 max-w-xs">
            <div class="max-w-sm w-full bg-white rounded-lg shadow-sm dark:bg-gray-800 p-4 md:p-6 mx-auto" style="max-width: 800px;"> <!-- Menambahkan mx-auto dan max-width -->
                <div class="flex justify-between pb-4 mb-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <h5 class="leading-none text-l font-normal text-gray-900 dark:text-white pb-1">Progress Modul</h5>
                    </div>
                </div>

                <div id="column-chart"></div>
                <div class="grid grid-cols-1 items-center border-gray-200 border-t dark:border-gray-700 justify-between"></div>
            </div>
        </div>

        <div class="mt-10 flex-1 max-w-xs">
            <div class="max-w-sm w-full bg-white rounded-lg shadow-sm dark:bg-gray-800 p-4 md:p-6">
                <div class="flex justify-between mb-3">
                    <div class="flex items-center">
                        <h5 class="text-xl font-normal leading-none text-gray-900 dark:text-white pe-1">Modul 1</h5>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                    <div class="grid grid-cols-3 gap-3 mb-2">
                        <dl class="bg-orange-50 dark:bg-gray-600 rounded-lg flex flex-col items-center justify-center h-[78px]">
                            <dt class="w-8 h-8 rounded-full bg-orange-100 dark:bg-gray-500 text-orange-600 dark:text-orange-300 text-sm font-medium flex items-center justify-center mb-1">12</dt>
                            <dd class="text-orange-600 dark:text-orange-300 text-sm font-medium">To do</dd>
                        </dl>
                        <dl class="bg-teal-50 dark:bg-gray-600 rounded-lg flex flex-col items-center justify-center h-[78px]">
                            <dt class="w-8 h-8 rounded-full bg-teal-100 dark:bg-gray-500 text-teal-600 dark:text-teal-300 text-sm font-medium flex items-center justify-center mb-1">23</dt>
                            <dd class="text-teal-600 dark:text-teal-300 text-sm font-medium">In progress</dd>
                        </dl>
                        <dl class="bg-blue-50 dark:bg-gray-600 rounded-lg flex flex-col items-center justify-center h-[78px]">
                            <dt class="w-8 h-8 rounded-full bg-blue-100 dark:bg-gray-500 text-blue-600 dark:text-blue-300 text-sm font-medium flex items-center justify-center mb-1">64</dt>
                            <dd class="text-blue-600 dark:text-blue-300 text-sm font-medium">Done</dd>
                        </dl>
                    </div>
                </div>

                <!-- Radial Chart -->
                <div class="py-6" id="radial-chart"></div>
            </div>
        </div>

        <div class="mt-10 flex-1 max-w-xs">
            <div class="max-w-sm w-full bg-white rounded-lg shadow-sm dark:bg-gray-800 p-4 md:p-6">
            <div class="flex justify-between mb-5">
                <div class="grid gap-4 grid-cols-2">
                    <div>
                        <h5 class="inline-flex items-center text-black dark:text-gray-400 leading-none font-normal mb-2">Pencapaian Siswa</h5>
                    </div>
                </div>
                <div>
                </div>
            </div>
            <div id="line-chart"></div>
            <div class="grid grid-cols-1 items-center border-gray-200 border-t dark:border-gray-700 justify-between mt-2.5">
            </div>
            </div>

        </div>
    </div>

@endsection

@section('script')
<script>
    const optionsBarChart = {
        colors: ["#1A56DB", "#FDBA8C"],
        series: [
            {
            name: "Organic",
            color: "#1A56DB",
            data: [
                { x: "Mon", y: 231 },
                { x: "Tue", y: 122 },
                { x: "Wed", y: 63 },
                { x: "Thu", y: 421 },
                { x: "Fri", y: 122 },
                { x: "Sat", y: 323 },
                { x: "Sun", y: 111 },
            ],
            },
            {
            name: "Social media",
            color: "#FDBA8C",
            data: [
                { x: "Mon", y: 232 },
                { x: "Tue", y: 113 },
                { x: "Wed", y: 341 },
                { x: "Thu", y: 224 },
                { x: "Fri", y: 522 },
                { x: "Sat", y: 411 },
                { x: "Sun", y: 243 },
            ],
            },
        ],
        chart: {
            type: "bar",
            height: "320px",
            fontFamily: "Inter, sans-serif",
            toolbar: {
            show: false,
            },
        },
        plotOptions: {
            bar: {
            horizontal: false,
            columnWidth: "70%",
            borderRadiusApplication: "end",
            borderRadius: 8,
            },
        },
        tooltip: {
            shared: true,
            intersect: false,
            style: {
            fontFamily: "Inter, sans-serif",
            },
        },
        states: {
            hover: {
            filter: {
                type: "darken",
                value: 1,
            },
            },
        },
        stroke: {
            show: true,
            width: 0,
            colors: ["transparent"],
        },
        grid: {
            show: false,
            strokeDashArray: 4,
            padding: {
            left: 2,
            right: 2,
            top: -14
            },
        },
        dataLabels: {
            enabled: false,
        },
        legend: {
            show: false,
        },
        xaxis: {
            floating: false,
            labels: {
            show: true,
            style: {
                fontFamily: "Inter, sans-serif",
                cssClass: 'text-xs font-normal fill-gray-500 dark:fill-gray-400'
            }
            },
            axisBorder: {
            show: false,
            },
            axisTicks: {
            show: false,
            },
        },
        yaxis: {
            show: false,
        },
        fill: {
            opacity: 1,
        },
        }

        if(document.getElementById("column-chart") && typeof ApexCharts !== 'undefined') {
        const chart = new ApexCharts(document.getElementById("column-chart"), optionsBarChart);
        chart.render();
    }

    const getChartOptions = () => {
        return {
            series: [90, 85, 70],
            colors: ["#1C64F2", "#16BDCA", "#FDBA8C"],
            chart: {
            height: "350px",
            width: "100%",
            type: "radialBar",
            sparkline: {
                enabled: true,
            },
            },
            plotOptions: {
            radialBar: {
                track: {
                background: '#E5E7EB',
                },
                dataLabels: {
                show: false,
                },
                hollow: {
                margin: 0,
                size: "32%",
                }
            },
            },
            grid: {
            show: false,
            strokeDashArray: 4,
            padding: {
                left: 2,
                right: 2,
                top: -23,
                bottom: -20,
            },
            },
            labels: ["Done", "In progress", "To do"],
            legend: {
            show: true,
            position: "bottom",
            fontFamily: "Inter, sans-serif",
            },
            tooltip: {
            enabled: true,
            x: {
                show: false,
            },
            },
            yaxis: {
            show: false,
            labels: {
                formatter: function (value) {
                return value + '%';
                }
            }
            }
        }
        }

        if (document.getElementById("radial-chart") && typeof ApexCharts !== 'undefined') {
        const chart = new ApexCharts(document.querySelector("#radial-chart"), getChartOptions());
        chart.render();
    }

    const optionsLineChart = {
        chart: {
            height: "100%",
            maxWidth: "100%",
            type: "line",
            fontFamily: "Inter, sans-serif",
            dropShadow: {
            enabled: false,
            },
            toolbar: {
            show: false,
            },
        },
        tooltip: {
            enabled: true,
            x: {
            show: false,
            },
        },
        dataLabels: {
            enabled: false,
        },
        stroke: {
            width: 6,
        },
        grid: {
            show: true,
            strokeDashArray: 4,
            padding: {
            left: 2,
            right: 2,
            top: -26
            },
        },
        series: [
            {
            name: "Clicks",
            data: [6500, 6418, 6456, 6526, 6356, 6456],
            color: "#1A56DB",
            },
            {
            name: "CPC",
            data: [6456, 6356, 6526, 6332, 6418, 6500],
            color: "#7E3AF2",
            },
        ],
        legend: {
            show: false
        },
        stroke: {
            curve: 'smooth'
        },
        xaxis: {
            categories: ['01 Feb', '02 Feb', '03 Feb', '04 Feb', '05 Feb', '06 Feb', '07 Feb'],
            labels: {
            show: true,
            style: {
                fontFamily: "Inter, sans-serif",
                cssClass: 'text-xs font-normal fill-gray-500 dark:fill-gray-400'
            }
            },
            axisBorder: {
            show: false,
            },
            axisTicks: {
            show: false,
            },
        },
        yaxis: {
            show: false,
        },
        }

        if (document.getElementById("line-chart") && typeof ApexCharts !== 'undefined') {
        const chart = new ApexCharts(document.getElementById("line-chart"), optionsLineChart);
        chart.render();
    }


</script>
@endsection