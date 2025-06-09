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
        <h1>Manajemen Siswa</h1>
        <p class="text-[10px] tracking-[5px] text-slate-200">Halaman untuk mengatur data siswa tiap sekolah</p>
    </div>

    <div class="mt-10 bg-white p-5 pl-8 rounded-l-xl">
        <div class="button mt-5 flex justify-self-end">
            <button data-modal-target="add-student-modal" data-modal-toggle="add-student-modal" class="px-5 py-2.5 text-sm rounded-lg bg-sky-500 text-sm hover:bg-sky-700 text-white cursor-pointer">
                <i class="bi bi-plus"></i> Tambah data
            </button>
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
                                NISN
                            </span>
                        </th>
                        <th>
                            <span class="flex items-center">
                                Sekolah
                            </span>
                        </th>
                        <th>
                            <span class="flex items-center">
                                Jenis Kelamin
                            </span>
                        </th>
                        <th>
                            <span class="flex items-center">
                                Kelas
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
                    @foreach ($data_siswa as $data)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td class="font-medium text-gray-900 whitespace-nowrap dark:text-white">{{$data->nisn}}</td>
                            <td class="font-medium text-gray-900 whitespace-nowrap dark:text-white">{{$data->name}}</td>
                            <td>
                                @if ($data->gender == 'L')
                                    <span>Laki-laki</span>
                                @else
                                    <span>Perempuan</span>
                                @endif
                            </td>
                            <td>{{$data->classroom->class_name ?? ''}}</td>
                            <td>{{$data->address}}</td>
                            <td>{{$data->birthday ? \Carbon\Carbon::parse($data->birthday)->locale('id')->isoFormat('D MMMM YYYY') : '-'}}</td>
                            <td class="flex gap-2">
                                <button data-modal-target="edit-student-modal" data-modal-toggle="edit-student-modal" class="p-2 text-sm rounded-lg bg-sky-500 text-sm hover:bg-sky-700 text-white cursor-pointer" onclick="openEditModal({{ $data->id }}, '{{$data->class_id}}', '{{ $data->nisn }}', '{{ $data->name }}', '{{ $data->address }}', '{{ $data->gender }}', '{{ $data->birthday }}')">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <a href="{{ route('delete-students', ['id' => $data->id]) }}" class="p-2 text-sm rounded-lg bg-red-500 text-sm hover:bg-red-700 text-white cursor-pointer"
                                    onclick="event.preventDefault(); showConfirmation('{{ route('delete-students', ['id' => $data->id]) }}')" title="Hapus data">
                                    <i class="bi bi-trash3"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="add-student-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Tambah data siswa
                    </h3>
                    <button type="button" class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="add-student-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5">
                    <form class="space-y-4" action="{{route('store-students')}}" method="POST">
                        @csrf
                        <div>
                            <label for="student_nisn" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">NISN</label>
                            <input type="text" inputmode="numeric" name="student_nisn" id="student_nisn" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                        </div>
                        <div>
                            <label for="student_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Siswa</label>
                            <input type="text" name="student_name" id="student_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                        </div>
                        {{-- <div>
                            <label for="school" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Sekolah</label>
                            <select id="school" name="school" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option value="" selected disabled>Pilihan Sekolah</option>
                                @foreach ($data_sekolah as $list)
                                    @php
                                        $encryptedId = Illuminate\Support\Facades\Crypt::encrypt($list->id);
                                    @endphp
                                    <option value="{{$encryptedId}}">{{$list->name_school}}</option>
                                @endforeach
                            </select>
                        </div> --}}
                        <div>
                            <label for="classroom" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kelas</label>
                            <select id="classroom" name="classroom" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option value="" selected disabled>Pilihan Kelas</option>
                                @foreach ($data_kelas as $data)
                                    <option value="{{$data->id}}">{{$data->class_name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="radio" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jenis Kelamin</label>
                            <div class="flex items-center mb-4">
                                <input id="laki-laki" type="radio" value="L" name="student_gender" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600">
                                <label for="laki-laki" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Laki-laki</label>
                            </div>
                            <div class="flex items-center">
                                <input id="perempuan" type="radio" value="P" name="student_gender" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600">
                                <label for="perempuan" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Perempuan</label>
                            </div>
                        </div>
                        <div>
                            <label for="student_address" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alamat Domisili</label>
                            <input type="text" name="student_address" id="student_address" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                        </div>
                        <div>
                            <label for="student_birthday" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Lahir</label>
                            <div class="relative max-w-sm">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                                    </svg>
                                </div>
                                <input datepicker id="student_birthday" name="student_birthday" datepicker-orientation="top" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5" placeholder="Pilih hari">
                            </div>
                        </div>
                        <button type="submit" class="w-full text-white mt-5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div> 

    <div id="edit-student-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Edit data siswa
                    </h3>
                    <button type="button" class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="edit-student-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5">
                    <form class="space-y-4" action="{{route('update-students')}}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="text" name="student_id" id="student_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" hidden />
                        <div>
                            <label for="student_nisn_edit" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">NISN</label>
                            <input type="text" inputmode="numeric" name="student_nisn_edit" id="student_nisn_edit" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                        </div>
                        <div>
                            <label for="student_name_edit" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Siswa</label>
                            <input type="text" name="student_name_edit" id="student_name_edit" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                        </div>
                        {{-- <div>
                            <label for="school_edit" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Sekolah</label>
                            <select id="school_edit" name="school_edit" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option value="" selected disabled>Pilihan Sekolah</option>
                                @foreach ($data_sekolah as $list)
                                    <option value="{{$list->id}}">{{$list->name_school}}</option>
                                @endforeach
                            </select>
                        </div> --}}
                        <div>
                            <label for="classroom_edit" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kelas</label>
                            <select id="classroom_edit" name="classroom_edit" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option value="" selected disabled>Pilihan Kelas</option>
                                @foreach ($data_kelas as $data)
                                    <option value="{{$data->id}}">{{$data->class_name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="radio" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jenis Kelamin</label>
                            <div class="flex items-center mb-4">
                                <input id="laki-laki" type="radio" value="L" name="student_gender_edit" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600">
                                <label for="laki-laki" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Laki-laki</label>
                            </div>
                            <div class="flex items-center">
                                <input id="perempuan" type="radio" value="P" name="student_gender_edit" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600">
                                <label for="perempuan" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Perempuan</label>
                            </div>
                        </div>
                        <div>
                            <label for="student_address_edit" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alamat Domisili</label>
                            <input type="text" name="student_address_edit" id="student_address_edit" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                        </div>
                        <div>
                            <label for="student_birthday_edit" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Lahir</label>
                            <div class="relative max-w-sm">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                                    </svg>
                                </div>
                                <input datepicker id="student_birthday_edit" name="student_birthday_edit" datepicker-orientation="top" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5" placeholder="Pilih hari">
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
        if (document.getElementById("student-table") && typeof simpleDatatables.DataTable !== 'undefined') {
            const dataTable = new simpleDatatables.DataTable("#student-table", {
                searchable: true,
                sortable: false
            });
        }

    function openEditModal(id, classroom, nisn, name, address, gender, birthday) {
        document.getElementById('student_id').value = id;
        $('#classroom_edit').val(classroom).trigger('change');
        document.getElementById('student_nisn_edit').value = nisn;
        document.getElementById('student_name_edit').value = name;
        document.querySelectorAll('input[name="student_gender_edit"]').forEach((radio) => {
            if (radio.value === gender) {
                radio.checked = true;  
            }
        });
        document.getElementById('student_address_edit').value = address;
        document.getElementById('student_birthday_edit').value = birthday;

        document.getElementById('edit-student-modal').classList.remove('hidden');
    }

    document.querySelector('[data-modal-hide="edit-student-modal"]').addEventListener('click', function () {
        document.getElementById('edit-student-modal').classList.add('hidden');
    });

    $(document).ready(function() {
        $("#classroom").select2({
            placeholder: 'Pilih Kelas',
            language: 'id',
            allowClear: true,
            width: '100%',
            theme: "classic"
        });
        $("#classroom_edit").select2({
            placeholder: 'Pilih Kelas',
            language: 'id',
            allowClear: true,
            width: '100%',
            theme: "classic"
        });
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