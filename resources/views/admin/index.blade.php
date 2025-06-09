@extends('layout-admins.app')

@section('content')
    <div class="w-full rounded-bl-xl p-5 pl-8 bg-white">   
        <h1>Halaman Beranda</h1>
        <p class="text-[10px] tracking-[5px] text-slate-200">Ingin apa hari ini</p>
    </div>

    <div class="w-full">
        <div class="mt-10">
            <div class="p-5 px-8 rounded-2xl bg-white relative group">
                <p class="font-bold text-4xl">Halo, Admin!</p>
                <i class="bi bi-person-workspace absolute bottom-2 right-3 text-gray-500 opacity-30 text-5xl"></i>
            </div>
        </div>
    </div>

    <div class="flex gap-6 w-full">
        <div class="w-2/3">
            <div class="flex flex-col gap-6 flex-1">
                <div class="flex gap-6">
                    <div class="mt-10 flex-1">
                        <div class="home-card-admin relative group">
                            <p class="group-hover:text-white">Data Guru</p>
                            <p class="text-5xl mt-3 group-hover:text-white">{{$jumlah_guru}}</p>
                            <i class="bi bi-person-workspace absolute bottom-2 right-3 text-gray-500 opacity-30 text-5xl"></i>
                        </div>
                    </div>
                    <div class="mt-10 flex-1">
                        <div class="home-card-admin relative group">
                            <p class="group-hover:text-white">Data Siswa</p>
                            <p class="text-5xl mt-3 group-hover:text-white">{{$jumlah_siswa}}</p>
                            <i class="bi bi-person-vcard absolute bottom-2 right-3 text-gray-500 opacity-30 text-5xl"></i>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <div class="w-full bg-white rounded-lg shadow-sm dark:bg-gray-800 p-4 md:p-6">
                        <div class="flex justify-between border-gray-200 border-b dark:border-gray-700 pb-3">
                            <dl>
                                <dt class="text-l font-normal text-black">Jumlah Modul</dt>
                            </dl>
                        </div>
                        <div id="bar-chart"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-1/3 flex justify-end">
            <div class="w-full">
                <div class="mt-10">
                    <div class="bg-white rounded-lg shadow-sm dark:bg-gray-800 p-4 md:p-6">
                        <div class="flex justify-between items-start w-full">
                            <div class="flex-col items-center">
                                <div class="flex items-center mb-1">
                                    <h5 class="text-l font-normal leading-none text-gray-900">Jumlah Login</h5>
                                    <div data-popover id="chart-info" role="tooltip" class="absolute z-10 invisible inline-block text-sm text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-xs opacity-0 w-72 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400">
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end items-center">
                                <div id="widgetDropdown" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-44 dark:bg-gray-700">
                                </div>
                            </div>
                        </div>
                        <div class="py-6" id="pie-chart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
<script>
    const getChartOptions = () => {
        return {
            series: [52.8, 26.8, 20.4],
            colors: ["#1C64F2", "#16BDCA", "#9061F9"],
            chart: {
            height: 420,
            width: "100%",
            type: "pie",
            },
            stroke: {
            colors: ["white"],
            lineCap: "",
            },
            plotOptions: {
            pie: {
                labels: {
                show: true,
                },
                size: "100%",
                dataLabels: {
                offset: -25
                }
            },
            },
            labels: ["Direct", "Organic search", "Referrals"],
            dataLabels: {
            enabled: true,
            style: {
                fontFamily: "Fira Sans, sans-serif",
            },
            },
            legend: {
            position: "bottom",
            fontFamily: "Fira Sans, sans-serif",
            },
            yaxis: {
            labels: {
                formatter: function (value) {
                return value + "%"
                },
            },
            },
            xaxis: {
            labels: {
                formatter: function (value) {
                return value  + "%"
                },
            },
            axisTicks: {
                show: false,
            },
            axisBorder: {
                show: false,
            },
            },
        }
    }

    if (document.getElementById("pie-chart") && typeof ApexCharts !== 'undefined') {
    const chart = new ApexCharts(document.getElementById("pie-chart"), getChartOptions());
    chart.render();
    }

    
    const optionsChartModul = {
        series: [
            {
            name: "Income",
            color: "#31C48D",
            data: ["1420", "1620", "1820", "1420", "1650", "2120"],
            },
            {
            name: "Expense",
            data: ["788", "810", "866", "788", "1100", "1200"],
            color: "#F05252",
            }
        ],
        chart: {
            sparkline: {
            enabled: false,
            },
            type: "bar",
            width: "100%",
            height: 400,
            toolbar: {
            show: false,
            }
        },
        fill: {
            opacity: 1,
        },
        plotOptions: {
            bar: {
            horizontal: true,
            columnWidth: "100%",
            borderRadiusApplication: "end",
            borderRadius: 6,
            dataLabels: {
                position: "top",
            },
            },
        },
        legend: {
            show: true,
            position: "bottom",
        },
        dataLabels: {
            enabled: false,
        },
        tooltip: {
            shared: true,
            intersect: false,
            formatter: function (value) {
            return "$" + value
            }
        },
        xaxis: {
            labels: {
            show: true,
            style: {
                fontFamily: "Inter, sans-serif",
                cssClass: 'text-xs font-normal fill-gray-500 dark:fill-gray-400'
            },
            formatter: function(value) {
                return "$" + value
            }
            },
            categories: ["Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
            axisTicks: {
            show: false,
            },
            axisBorder: {
            show: false,
            },
        },
        yaxis: {
            labels: {
            show: true,
            style: {
                fontFamily: "Inter, sans-serif",
                cssClass: 'text-xs font-normal fill-gray-500 dark:fill-gray-400'
            }
            }
        },
        grid: {
            show: true,
            strokeDashArray: 4,
            padding: {
            left: 2,
            right: 2,
            top: -20
            },
        },
        fill: {
            opacity: 1,
        }
    }

    if(document.getElementById("bar-chart") && typeof ApexCharts !== 'undefined') {
    const chart = new ApexCharts(document.getElementById("bar-chart"), optionsChartModul);
    chart.render();
    }



    const prevMonthButton = document.getElementById("prev-month");
    const nextMonthButton = document.getElementById("next-month");
    const monthYear = document.getElementById("month-year");
    const calendarGrid = document.getElementById("calendar-grid");

    let currentDate = new Date();

    function renderCalendar() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        
        const firstDayOfMonth = new Date(year, month, 1);
        const lastDayOfMonth = new Date(year, month + 1, 0);

        monthYear.textContent = `${firstDayOfMonth.toLocaleString("default", { month: "long" })} ${year}`;

        const daysInMonth = lastDayOfMonth.getDate();
        const firstDayOfWeek = firstDayOfMonth.getDay();
        
        calendarGrid.innerHTML = "";

        // Empty spaces before the first day of the month
        for (let i = 0; i < firstDayOfWeek; i++) {
            const emptyCell = document.createElement("div");
            calendarGrid.appendChild(emptyCell);
        }

        // Create day cells for the month
        for (let i = 1; i <= daysInMonth; i++) {
            const dayCell = document.createElement("div");
            dayCell.textContent = i;
            dayCell.classList.add("text-center", "py-5", "rounded-lg", "cursor-pointer", "hover:bg-gray-200");

            // Highlight today's date with bg-sky-500
            if (i === new Date().getDate() && currentDate.getMonth() === new Date().getMonth() && currentDate.getFullYear() === new Date().getFullYear()) {
                dayCell.classList.add("bg-sky-500", "text-white", "font-bold", "rounded-full"); // Tailwind classes to highlight today
            }

            calendarGrid.appendChild(dayCell);
        }

        // Empty spaces after the last day of the month
        const remainingCells = 7 - (daysInMonth + firstDayOfWeek) % 7;
        for (let i = 0; i < remainingCells && remainingCells < 7; i++) {
            const emptyCell = document.createElement("div");
            calendarGrid.appendChild(emptyCell);
        }

        dayCell.classList.add("text-center", "py-2", "rounded-lg", "cursor-pointer", "hover:bg-gray-200", "flex", "items-center", "justify-center", "h-10");
    }

    // Move to the previous month
    prevMonthButton.addEventListener("click", () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
    });

    // Move to the next month
    nextMonthButton.addEventListener("click", () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
    });

    // Initial render
    renderCalendar();
</script>
@endsection