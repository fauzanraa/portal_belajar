@extends('layout-admins.app')

@section('content')
    <div class="w-full rounded-bl-xl p-5 pl-8 bg-white">   
        <h1>Manajemen Nilai</h1>
        <p class="text-[10px] tracking-[5px] text-slate-200">Halaman untuk guru mengelola nilai siswa</p>
    </div>

    <div class="mt-10 rounded-tl-xl bg-white p-5 pl-8">
        <div class="mt-1">
            <div class="button my-5">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Pilihan Akses</h3>
                <button id="dropdownRadioButton" data-dropdown-toggle="dropdownRadio" class="inline-flex items-center text-gray-500 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-3 py-1.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700" type="button">
                    <i class="bi bi-shield-lock mr-3"></i>
                    <span id="selectedClass">Sistem</span>
                    <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                    </svg>
                </button>
                <div id="dropdownRadio" class="z-10 hidden w-48 bg-white divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-700 dark:divide-gray-600">
                    <ul class="p-3 space-y-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownRadioButton">
                        <li>
                            <div class="flex items-center p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                <input id="filter-radio-system" type="radio" value="system" name="filter-radio" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600" checked>
                                <label for="filter-radio-system" class="w-full ms-2 text-sm font-medium text-gray-900 rounded">Sistem</label>
                            </div>
                        </li>
                    </ul>
                    <ul class="p-3 space-y-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownRadioButton">
                        <li>
                            <div class="flex items-center p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                <input id="filter-radio-non_system" type="radio" value="non_system" name="filter-radio" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600">
                                <label for="filter-radio-non_system" class="w-full ms-2 text-sm font-medium text-gray-900 rounded">Non Sistem</label>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="mt-9">
            <table id="student-table">
                <thead>
                    <tr>
                        <th>
                            No
                        </th>
                        <th>
                            <span class="flex items-center">
                                Nama
                            </span>
                        </th>
                        <th>
                            <span class="flex items-center">
                                Kelas
                            </span>
                        </th>
                        <th>
                            <span class="flex items-center">
                                Nilai
                            </span>
                        </th>
                        <th>
                            <span class="flex items-center">
                                Waktu
                            </span>
                        </th>
                        <th>
                            <span class="flex items-center">
                                Jumlah Benar
                            </span>
                        </th>
                        <th>
                            <span class="flex items-center">
                                Tipe
                            </span>
                        </th>
                        <th>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dataSiswa as $data)
                        <tr data-access ="{{$data->access}}">
                            <td>{{$loop->iteration}}</td>
                            <td class="font-medium text-gray-900 whitespace-nowrap">{{$data->name}}</td>
                            <td class="font-medium text-gray-900 whitespace-nowrap">{{$data->class_name}}</td>
                            <td>{{$data->score}}</td>
                            <td>{{$data->duration}}</td>
                            <td>{{$data->correct_elements}}</td>
                            @if ($data->type == 'pretest')
                                <td>Pre-Test</td>
                            @else
                                <td>Post-Test</td>
                            @endif
                            @php
                                $encryptedModul = Illuminate\Support\Facades\Crypt::encrypt($taskSession->meeting_id);
                                $encryptedSession = Illuminate\Support\Facades\Crypt::encrypt($data->id);
                            @endphp
                            <td class="flex gap-2">
                                <a href="{{ route('detail-assessments', ['idModul' => $encryptedModul, 'idSession' => $encryptedSession])}}" class="p-2 text-sm rounded-lg bg-sky-500 text-sm hover:bg-sky-700 text-white cursor-pointer">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach

                    <tr id="no-data-row" style="display: none;">
                        <td colspan="7" class="text-center text-gray-500 italic py-4">Tidak ada data.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const radios = document.querySelectorAll('input[name="filter-radio"]');
            const rows = document.querySelectorAll('#student-table tbody tr[data-access]');
            const noDataRow = document.getElementById('no-data-row');

            function filterRows(selectedAccess) {
                let visibleCount = 0;

                rows.forEach(row => {
                    const access = row.getAttribute('data-access');
                    const match = (access === selectedAccess);

                    row.style.display = match ? '' : 'none';
                    if (match) visibleCount++;
                });

                noDataRow.style.display = visibleCount === 0 ? '' : 'none';

                const label = selectedAccess === 'system' ? 'Sistem' : 'Non Sistem';
                document.getElementById('selectedClass').innerText = label;
            }

            filterRows('system');

            radios.forEach(radio => {
                radio.addEventListener('change', function () {
                    filterRows(this.value);
                });
            });
        });

        if (document.getElementById("student-table") && typeof simpleDatatables.DataTable !== 'undefined') {
            const dataTable = new simpleDatatables.DataTable("#student-table", {
                searchable: true,
                sensitivity: "accent", 
                searchQuerySeparator: "",
                sortable: false
            });
        }
    </script>
@endsection