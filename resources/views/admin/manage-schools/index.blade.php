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
        <h1>Manajemen Sekolah</h1>
        <p class="text-[10px] tracking-[5px] text-slate-200">Halaman untuk mengatur data sekolah</p>
    </div>

    <div class="mt-10 bg-white p-5 pl-8 rounded-l-xl">
        <div class="button mt-5 flex justify-self-end">
            <button data-modal-target="add-school-modal" data-modal-toggle="add-school-modal" class="px-5 py-2.5 text-sm rounded-lg bg-sky-500 text-sm hover:bg-sky-700 text-white cursor-pointer">
                <i class="bi bi-plus"></i> Tambah data
            </button>
        </div>
        <div class="mt-9">
            <table id="school-table" class="relative">
                <thead>
                    <tr>
                        <th>
                            No
                        </th>
                        <th>
                            <span class="flex items-center">
                                Nama Sekolah
                            </span>
                        </th>
                        <th>
                            <span class="flex items-center">
                                Alamat
                            </span>
                        </th>
                        <th>
                            <span class="flex items-center">
                                Email
                            </span>
                        </th>
                        <th>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data_sekolah as $data)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td class="font-medium text-gray-900 whitespace-nowrap">{{$data->name_school}}</td>
                            <td>{{$data->address}}</td>
                            <td>{{$data->email}}</td>
                            <td class="flex gap-2">
                                @php
                                    $encryptedId = Illuminate\Support\Facades\Crypt::encrypt($data->id);
                                @endphp
                                <button class="p-2 text-sm rounded-lg bg-sky-500 text-sm hover:bg-sky-700 text-white cursor-pointer" onclick="window.location.href='{{ route('manage-class', ['id' => $encryptedId]) }}'">
                                    <i class="bi bi-arrow-right"></i>
                                </button>
                                <button data-modal-target="edit-school-modal" data-modal-toggle="edit-school-modal" class="p-2 text-sm rounded-lg bg-sky-500 text-sm hover:bg-sky-700 text-white cursor-pointer" onclick="openEditModal({{ $data->id }}, '{{ $data->name_school }}', '{{ $data->address }}', '{{ $data->email }}')">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <a href="{{ route('delete-schools', ['id' => $data->id]) }}" class="p-2 text-sm rounded-lg bg-red-500 text-sm hover:bg-red-700 text-white cursor-pointer"
                                    onclick="event.preventDefault(); showConfirmation('{{ route('delete-schools', ['id' => $data->id]) }}')" title="Hapus data">
                                    <i class="bi bi-trash3"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="add-school-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow-sm">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900">
                        Tambah data sekolah
                    </h3>
                    <button type="button" class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="add-school-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5">
                    <form class="space-y-4" action="{{route('store-schools')}}" method="POST">
                        @csrf
                        <div>
                            <label for="school_name" class="block mb-2 text-sm font-medium text-gray-900">Sekolah</label>
                            <input type="text" name="school_name" id="school_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                        </div>
                        <div>
                            <label for="school_address" class="block mb-2 text-sm font-medium text-gray-900">Alamat Sekolah</label>
                            <input type="text" name="school_address" id="school_address" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                        </div>
                        <div>
                            <label for="school_email" class="block mb-2 text-sm font-medium text-gray-900">Alamat Email</label>
                            <input type="email" name="school_email" id="school_email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                        </div>
                        <button type="submit" class="w-full text-white mt-5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div> 

    <div id="edit-school-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow-sm ">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900">
                        Edit data sekolah
                    </h3>
                    <button type="button" class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="edit-school-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5">
                    <form class="space-y-4" action="{{ route('update-schools') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="teacher_id" id="teacher_id">
                        <div>
                            <label for="school_name_edit" class="block mb-2 text-sm font-medium text-gray-900">Sekolah</label>
                            <input type="text" name="school_name_edit" id="school_name_edit" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                        </div>
                        <div>
                            <label for="school_address_edit" class="block mb-2 text-sm font-medium text-gray-900">Alamat Sekolah</label>
                            <input type="text" name="school_address_edit" id="school_address_edit" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                        </div>
                        <div>
                            <label for="school_email_edit" class="block mb-2 text-sm font-medium text-gray-900">Alamat Email</label>
                            <input type="email" name="school_email_edit" id="school_email_edit" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
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
        if (document.getElementById("school-table") && typeof simpleDatatables.DataTable !== 'undefined') {
            const dataTable = new simpleDatatables.DataTable("#school-table", {
                searchable: true,
                sensitivity: "accent", 
                searchQuerySeparator: "",
                sortable: false
            });
        }

        function openEditModal(id, name, address, email) {
            document.getElementById('school_id').value = id;
            document.getElementById('school_name_edit').value = name;
            document.getElementById('school_address_edit').value = address;
            document.getElementById('school_email_edit').value = email;

            document.getElementById('edit-school-modal').classList.remove('hidden');
        }

        document.querySelector('[data-modal-hide="edit-school-modal"]').addEventListener('click', function () {
            document.getElementById('edit-school-modal').classList.add('hidden');
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