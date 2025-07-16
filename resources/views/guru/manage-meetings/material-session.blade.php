@extends('layout-admins.app')

@section('content')
    {{-- @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mt-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif --}}

    <div class="w-full rounded-bl-xl p-5 pl-8 bg-white">   
        <h1>Manajemen Sesi Materi</h1>
        <p class="text-[10px] tracking-[5px] text-slate-200">Halaman untuk hak akses materi untuk siswa</p>
    </div>

    <div class="mt-10 bg-white p-5 pl-8 rounded-l-xl">
        <div class="button mt-5">
            <button id="dropdownRadioButton" data-dropdown-toggle="dropdownRadio" class="inline-flex items-center text-gray-500 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-3 py-1.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700" type="button">
                <i class="bi bi-person-circle mr-3"></i>
                <span id="selectedClass">Semua Kelas</span>
                <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                </svg>
            </button>
            <div id="dropdownRadio" class="z-10 hidden w-48 bg-white divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-700 dark:divide-gray-600">
                <ul class="p-3 space-y-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownRadioButton">
                    <li>
                        <div class="flex items-center p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                            <input id="filter-radio-all" type="radio" value="all" name="filter-radio" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" checked>
                            <label for="filter-radio-all" class="w-full ms-2 text-sm font-medium text-gray-900 rounded">Semua Kelas</label>
                        </div>
                    </li>
                    @php
                        $uniqueClasses = $data_siswa->groupBy('classroom.class_name');
                    @endphp
                    @foreach ($uniqueClasses as $className => $students)
                        <li>
                            <div class="flex items-center p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                <input id="filter-radio-{{ $loop->index }}" type="radio" value="{{ $className }}" name="filter-radio" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label for="filter-radio-{{ $loop->index }}" class="w-full ms-2 text-sm font-medium text-gray-900 rounded">Kelas {{ $className }}</label>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="mt-9">
            <form action="{{route('store-session-materials', ['id' => $id])}}" method="POST">
            @csrf
                <table id="student-table" class="relative">
                    <thead>
                        <tr>
                            <th class="">
                                No
                            </th>
                            <th class="">
                                <span class="flex items-center">
                                    Nama Siswa
                                </span>
                            </th>
                            <th class="">
                                <span class="flex items-center">
                                    Kelas
                                </span>
                            </th>
                            <th class="">
                                <div class="flex items-center">
                                    <input id="checkbox-all-search" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="checkbox-all-search" class="sr-only">checkbox</label>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="student-tbody">
                        @foreach ($data_siswa as $data)
                        <input type="text" id="class_id" name="class_id" value="all" hidden>
                            <tr class="student-row" data-class="{{ $data->classroom->class_name }}">
                                <td>{{$loop->iteration}}</td>
                                <td class="font-medium text-gray-900 whitespace-nowrap">{{$data->name}}</td>
                                <td class="font-medium text-gray-900 whitespace-nowrap">{{$data->classroom->class_name}}</td>
                                <td>
                                    <div class="flex items-center">
                                        <input id="checkbox-table-search-{{ $data->id }}" type="checkbox" name="student_id[]" value="{{$data->id}}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        <label for="checkbox-table-search-{{ $data->id }}" class="sr-only">checkbox</label>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="button mt-5 flex justify-self-end gap-4">
                    <button id="submit-btn" type="submit" class="px-5 py-2.5 text-sm rounded-lg bg-sky-500 text-sm hover:bg-sky-700 text-white cursor-pointer">
                        <i class="bi bi-floppy-fill mr-2"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@section('script')
    <script>   
        let dataTable;
        if (document.getElementById("student-table") && typeof simpleDatatables.DataTable !== 'undefined') {
            dataTable = new simpleDatatables.DataTable("#student-table", {
                searchable: true,
                sensitivity: "accent", 
                searchQuerySeparator: "",
                sortable: false
            });
            
            setTimeout(() => {
                initializeCheckboxAll();
            }, 100);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const radioButtons = document.querySelectorAll('input[name="filter-radio"]');
            const selectedClassSpan = document.getElementById('selectedClass');
            const dropdownButton = document.getElementById('dropdownRadioButton');
            const dropdown = document.getElementById('dropdownRadio');

            radioButtons.forEach(radio => {
                radio.addEventListener('change', function() {
                    const selectedValue = this.value;
                    const selectedLabel = this.nextElementSibling.textContent;
                    
                    selectedClassSpan.textContent = selectedLabel;
                    
                    dropdown.classList.add('hidden');

                    document.getElementById('class_id').value = selectedValue;
                    
                    filterTable(selectedValue);
                });
            });

            dropdownButton.addEventListener('click', function() {
                dropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', function(event) {
                if (!dropdownButton.contains(event.target) && !dropdown.contains(event.target)) {
                    dropdown.classList.add('hidden');
                }
            });
        });

        function filterTable(className) {
            if (dataTable) {
                dataTable.destroy();
            }

            const rows = document.querySelectorAll('.student-row');
            let visibleRowCount = 0;

            rows.forEach(row => {
                const rowClass = row.getAttribute('data-class');
                
                if (className === 'all' || rowClass === className) {
                    row.style.display = '';
                    visibleRowCount++;

                    const numberCell = row.querySelector('td:first-child');
                    numberCell.textContent = visibleRowCount;
                } else {
                    row.style.display = 'none';
                }
            });

            if (document.getElementById("student-table") && typeof simpleDatatables.DataTable !== 'undefined') {
                dataTable = new simpleDatatables.DataTable("#student-table", {
                    searchable: true,
                    sensitivity: "accent", 
                    searchQuerySeparator: "",
                    sortable: false
                });
            }
        }

        function initializeCheckboxAll() {
            const checkboxAll = document.getElementById('checkbox-all-search');
            
            checkboxAll.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('input[id^="checkbox-table-search-"]');
                const visibleCheckboxes = Array.from(checkboxes).filter(cb => 
                    cb.closest('tr').style.display !== 'none'
                );
                
                visibleCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });
        }

        function initializeCheckboxAll() {
            const checkboxAll = document.getElementById('checkbox-all-search');
            
            checkboxAll.removeEventListener('change', handleCheckboxAll);
            
            checkboxAll.addEventListener('change', handleCheckboxAll);
        }

        function handleCheckboxAll() {
            const checkboxes = document.querySelectorAll('input[id^="checkbox-table-search-"]');
            const visibleCheckboxes = Array.from(checkboxes).filter(cb => 
                cb.closest('tr').style.display !== 'none'
            );
            
            visibleCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        }

        function filterTable(className) {
            if (dataTable) {
                dataTable.destroy();
            }

            const rows = document.querySelectorAll('.student-row');
            let visibleRowCount = 0;

            rows.forEach(row => {
                const rowClass = row.getAttribute('data-class');
                
                if (className === 'all' || rowClass === className) {
                    row.style.display = '';
                    visibleRowCount++;
                    const numberCell = row.querySelector('td:first-child');
                    numberCell.textContent = visibleRowCount;
                } else {
                    row.style.display = 'none';
                }
            });

            if (document.getElementById("student-table") && typeof simpleDatatables.DataTable !== 'undefined') {
                dataTable = new simpleDatatables.DataTable("#student-table", {
                    searchable: true,
                    sensitivity: "accent", 
                    searchQuerySeparator: "",
                    sortable: false
                });
            }
            
            initializeCheckboxAll();
        }

        function showConfirmation(url) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Data ini akan dihapus!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Iya',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('delete-form');
                    form.action = url;
                    form.submit();
                }
            });
        }
    </script>
@endsection
