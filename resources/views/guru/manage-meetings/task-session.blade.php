@extends('layout-admins.app')

@section('content')
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showSuccessMessage(@json(session('success')));
            });
        </script>
    @elseif(session('error')) 
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showErrorMessage(@json(session('error')));
            });
        </script>
    @endif

    <div class="w-full rounded-bl-xl p-5 pl-8 bg-white">   
        <h1>Manajemen Sesi Tugas</h1>
        <p class="text-[10px] tracking-[5px] text-slate-200">Halaman untuk hak akses tugas untuk siswa</p>
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
                        $uniqueClasses = $filterSiswa->groupBy('classroom.class_name');
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
            <form action="{{route('store-sessions', ['id' => $id, 'type' => $type])}}" method="POST">
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
                        @foreach ($filterSiswa as $data)
                        <input type="text" id="class_id" name="class_id" value="all" hidden>
                            <tr class="student-row" data-class="{{ $data->classroom->class_name }}">
                                <td>{{$loop->iteration}}</td>
                                <td class="font-medium text-gray-900 whitespace-nowrap">{{$data->name}}</td>
                                <td class="font-medium text-gray-900 whitespace-nowrap">{{$data->classroom->class_name}}</td>
                                <td>
                                    <div class="flex items-center">
                                        <input id="checkbox-table-search-{{ $data->id }}" type="checkbox" name="student_id[]" value="{{$data->id}}" {{ in_array($data->id, $siswaTerdaftarPerAccess) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        <label for="checkbox-table-search-{{ $data->id }}" class="sr-only">checkbox</label>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="button mt-5 flex justify-self-end gap-4">
                    <a href="{{ route('detail-tasks', $id) }}"
                        class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-sky-100 focus:outline-none focus:ring-2 focus:ring-sky-500 transition-colors duration-200">
                        <i class="bi bi-arrow-left mr-2"></i> Kembali
                    </a>
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
        function showSuccessMessage(message) {
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full';
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="bi bi-check-lg mr-2"></i>
                    ${message}
                </div>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }

        function showErrorMessage(message) {
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full';
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="bi bi-x-circle-fill mr-2"></i>
                    ${message}
                </div>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 5000);
        }

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
