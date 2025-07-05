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
        <h1>Manajemen Guru</h1>
        <p class="text-[10px] tracking-[5px] text-slate-200">Halaman untuk mengatur data guru</p>
    </div>

    <div class="mt-10 bg-white p-5 pl-8 rounded-l-xl">
        <div class="button mt-5 flex justify-self-end">
            <button data-modal-target="add-teacher-modal" data-modal-toggle="add-teacher-modal" class="px-5 py-2.5 text-sm rounded-lg bg-sky-500 text-sm hover:bg-sky-700 text-white cursor-pointer">
                <i class="bi bi-plus"></i> Tambah data
            </button>
        </div>
        <div class="mt-9">
            <table id="teacher-table" class="relative">
                <thead>
                    <tr>
                        <th>
                            No
                        </th>
                        <th>
                            <span class="flex items-center">
                                NIP
                            </span>
                        </th>
                        <th>
                            <span class="flex items-center">
                                Nama Guru
                            </span>
                        </th>
                        <th>
                            <span class="flex items-center">
                                Jenis Kelamin
                            </span>
                        </th>
                        <th>
                            <span class="flex items-center">
                                Alamat
                            </span>
                        </th>
                        <th>
                            <span class="flex items-center">
                                Tanggal Lahir
                            </span>
                        </th>
                        <th>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data_guru as $data)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td class="font-medium text-gray-900 whitespace-nowrap">{{$data->nip}}</td>
                            <td class="font-medium text-gray-900 whitespace-nowrap">{{$data->name}}</td>
                            <td>
                                @if ($data->gender == 'L')
                                    <span>Laki-laki</span>
                                @else
                                    <span>Perempuan</span>
                                @endif
                            </td>
                            <td>{{$data->address}}</td>
                            <td>{{$data->birthday ? \Carbon\Carbon::parse($data->birthday)->locale('id')->isoFormat('D MMMM YYYY') : '-'}}</td>
                            <td class="flex gap-2">
                                <button data-modal-target="edit-teacher-modal" data-modal-toggle="edit-teacher-modal" class="p-2 text-sm rounded-lg bg-sky-500 text-sm hover:bg-sky-700 text-white cursor-pointer" onclick="openEditModal({{ $data->id }}, '{{ $data->nip }}', '{{ $data->name }}', '{{ $data->address }}', '{{ $data->gender }}', '{{ $data->birthday }}')">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <a href="{{ route('delete-teachers', ['id' => $data->id]) }}" class="p-2 text-sm rounded-lg bg-red-500 text-sm hover:bg-red-700 text-white cursor-pointer"
                                    onclick="event.preventDefault(); showConfirmation('{{ route('delete-teachers', ['id' => $data->id]) }}')" title="Hapus data">
                                    <i class="bi bi-trash3"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="add-teacher-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Tambah data guru
                    </h3>
                    <button type="button" class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="add-teacher-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5">
                    <form class="space-y-4" action="{{route('store-teachers')}}" method="POST">
                        @csrf
                        <div>
                            <label for="teacher_nip" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">NIP</label>
                            <input type="text" inputmode="numeric" name="teacher_nip" id="teacher_nip" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                        </div>
                        <div>
                            <label for="teacher_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Guru</label>
                            <input type="text" name="teacher_name" id="teacher_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                        </div>
                        {{-- <div>
                            <label for="school" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Sekolah</label>
                            <select id="school" name="school" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option value="" selected disabled>Pilihan Sekolah</option>
                                @foreach ($data_sekolah as $list)
                                    <option value="{{$list->id}}">{{$list->name_school}}</option>
                                @endforeach
                            </select>
                        </div> --}}
                        <div>
                            <label for="radio" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jenis Kelamin</label>
                            <div class="flex items-center mb-4">
                                <input id="laki-laki" type="radio" value="L" name="teacher_gender" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600">
                                <label for="laki-laki" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Laki-laki</label>
                            </div>
                            <div class="flex items-center">
                                <input id="perempuan" type="radio" value="P" name="teacher_gender" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600">
                                <label for="perempuan" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Perempuan</label>
                            </div>
                        </div>
                        <div>
                            <label for="teacher_address" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alamat Domisili</label>
                            <input type="text" name="teacher_address" id="teacher_address" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                        </div>
                        <div>
                            <label for="teacher_birthday" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Lahir</label>
                            <div class="relative max-w-sm">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                                    </svg>
                                </div>
                                <input datepicker id="teacher_birthday" name="teacher_birthday" datepicker-orientation="top" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5" placeholder="Pilih hari">
                            </div>
                        </div>
                        <button type="submit" class="w-full text-white mt-5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div> 

    <div id="edit-teacher-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Edit data guru
                    </h3>
                    <button type="button" class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="edit-teacher-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5">
                    <form class="space-y-4" action="{{route('update-teachers')}}" method="POST">
                        @csrf
                        <input type="text" name="teacher_id" id="teacher_id" hidden>
                        <div>
                            <label for="teacher_nip_edit" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">NIP</label>
                            <input type="text" inputmode="numeric" name="teacher_nip_edit" id="teacher_nip_edit" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                        </div>
                        <div>
                            <label for="teacher_name_edit" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Guru</label>
                            <input type="text" name="teacher_name_edit" id="teacher_name_edit" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                        </div>
                        {{-- <div>
                            <label for="school" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Sekolah</label>
                            <select id="school_edit" name="school_edit" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option value="" selected disabled>Pilihan Sekolah</option>
                                @foreach ($data_sekolah as $list)
                                    <option value="{{$list->id}}">{{$list->name_school}}</option>
                                @endforeach
                            </select>
                        </div> --}}
                        <div>
                            <label for="radio" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jenis Kelamin</label>
                            <div class="flex items-center mb-4">
                                <input id="laki-laki" type="radio" value="L" name="teacher_gender_edit" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600">
                                <label for="laki-laki" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Laki-laki</label>
                            </div>
                            <div class="flex items-center">
                                <input id="perempuan" type="radio" value="P" name="teacher_gender_edit" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600">
                                <label for="perempuan" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Perempuan</label>
                            </div>
                        </div>
                        <div>
                            <label for="teacher_address_edit" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alamat Domisili</label>
                            <input type="text" name="teacher_address_edit" id="teacher_address_edit" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                        </div>
                        <div>
                            <label for="teacher_birthday_edit" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Lahir</label>
                            <div class="relative max-w-sm">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                                    </svg>
                                </div>
                                <input datepicker id="teacher_birthday_edit" name="teacher_birthday_edit" datepicker-orientation="top" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5" placeholder="Pilih hari">
                            </div>
                        </div>
                        <button type="submit" class="w-full text-white mt-5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Simpan</button>
                    </form>
                </div>
            </div>
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

        if (document.getElementById("teacher-table") && typeof simpleDatatables.DataTable !== 'undefined') {
            const dataTable = new simpleDatatables.DataTable("#teacher-table", {
                searchable: true,
                sensitivity: "accent", 
                searchQuerySeparator: "",
                sortable: false
            });
        }

        function openEditModal(id, nip, name, address, gender, birthday) {
            document.getElementById('teacher_id').value = id;
            document.getElementById('teacher_nip_edit').value = nip;
            document.getElementById('teacher_name_edit').value = name;
            document.querySelectorAll('input[name="teacher_gender_edit"]').forEach((radio) => {
                if (radio.value === gender) {
                    radio.checked = true;  
                }
            });
            document.getElementById('teacher_address_edit').value = address;
            document.getElementById('teacher_birthday_edit').value = birthday;

            document.getElementById('edit-teacher-modal').classList.remove('hidden');
        }

        document.querySelector('[data-modal-hide="edit-teacher-modal"]').addEventListener('click', function () {
            document.getElementById('edit-teacher-modal').classList.add('hidden');
        });

        function showConfirmation(url) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Data ini akan dihapus!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Iya',
                cancelButtonText: 'Batal',
                reverseButtons: true
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