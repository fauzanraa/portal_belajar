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
        <h1>Detail Materi</h1>
        <p class="text-[10px] tracking-[5px] text-slate-200">Halaman untuk mengatur sesi materi</p>
    </div>

    <div class="mt-10 bg-white p-5 pl-8 rounded-l-xl">
        <div>
            <p class="font-bold mt-3">Pengaturan materi :</p>
        </div>
        <div class="mt-5">
            <div class="relative overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <tbody>
                        <tr class="bg-white">
                            <th scope="row" class="px-6 py-4 w-1/4 font-medium border border-gray-400 bg-slate-300 text-black">
                                Judul
                            </th>
                            <td class="px-6 py-4 border-y border-gray-200 border-r border-gray-200">
                                {{$data_materi->name}}
                            </td>
                        </tr>
                        <tr class="bg-white">
                            <th scope="row" class="px-6 py-4 font-medium border border-gray-400 bg-slate-300 text-black">
                                File Materi
                            </th>
                            <td class="px-6 py-4 border-y border-gray-200 border-r border-gray-200">
                                @if ($data_materi->file == true)
                                    <a href="{{asset('storage/assets/materials/'.$data_materi->file)}}" target="_blank">
                                        <i class="bi bi-file-earmark-pdf-fill text-red-500"></i><span class="ml-3">{{$data_materi->file}}</span>
                                    </a>
                                @else
                                    </i><span>Tidak ada file</span>
                                @endif

                                <div class="block mt-7 mb-2 flex gap-4">
                                    @if ($data_materi->file == false)
                                        <button data-modal-target="add-material-modal" data-modal-toggle="add-material-modal" class="text-sky-500 hover:text-sky-700">
                                            <div>
                                                <i class="bi bi-plus-square"></i><span class="ml-3">Tambah file</span>
                                            </div>
                                        </button>
                                    @else
                                        <button data-modal-target="update-material-modal" data-modal-toggle="update-material-modal" class="text-sky-500 hover:text-sky-700">
                                            <div>
                                                <i class="bi bi-pencil-square"></i><span class="ml-3">Edit file</span>
                                            </div>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        <tr class="bg-white">
                            <th scope="row" class="px-6 py-4 font-medium border border-gray-400 bg-slate-300 text-black">
                                Akses Materi
                            </th>
                            <td class="px-6 py-4 border-y border-gray-200 border-r border-gray-200">
                                <div class="block">
                                    @if($sesi_materi_siswa->isEmpty())
                                        <p class="text-gray-500 italic">Belum ada sesi</p>
                                    @else
                                        @foreach($sesi_materi_siswa as $className => $students)
                                            <div class="mb-2">
                                                <button data-modal-target="add-material-modal" data-modal-toggle="add-material-modal" class="hover:text-sky-500 cursor-pointer">
                                                    Kelas {{ $className }} - {{ $students->count() }} Siswa
                                                </button>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                <div class="block mt-5 mb-2 flex gap-4">
                                    @php
                                        $encryptedId = Illuminate\Support\Facades\Crypt::encrypt($data_materi->id);
                                    @endphp
                                    <a href="{{route('session-materials', ['id' => $encryptedId])}}" class="text-sky-500 hover:text-sky-700">
                                        <div class="block">
                                            <i class="bi bi-people-fill"></i><span class="ml-3">Atur sesi</span>
                                        </div>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="button mt-8 flex justify-self-end">
            @php
                $encryptedMeeting = Illuminate\Support\Facades\Crypt::encrypt($data_materi->meeting_id);
            @endphp
            <button type="button" class="px-5 py-2.5 text-sm rounded-lg bg-sky-500 text-sm hover:bg-sky-700 text-white cursor-pointer" onclick="window.location.href='{{ route('manage-materials', ['id' => $encryptedMeeting]) }}'">
                <i class="bi bi-caret-left mr-1"></i> Kembali
            </button>
        </div>
    </div>

    <div id="add-material-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Tambah file materi
                    </h3>
                    <button type="button" class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="add-material-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5">
                    @php
                        $encryptedId = Illuminate\Support\Facades\Crypt::encrypt($data_materi->id);
                    @endphp
                    <form class="space-y-4" action="{{route('file-materials', ['id' => $encryptedId])}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900" for="file_input">Berkas Materi</label>
                            <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" aria-describedby="file_input_help" id="file_input" name="file_material" type="file">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">Pdf (max. 10mb)</p>
                        </div>
                        <button type="submit" class="w-full text-white mt-5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="update-material-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Update file materi
                    </h3>
                    <button type="button" class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="update-material-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5">
                    @php
                        $encryptedId = Illuminate\Support\Facades\Crypt::encrypt($data_materi->id);
                    @endphp
                    <form class="space-y-4" action="{{route('file-materials', ['id' => $encryptedId])}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900" for="file_input">Berkas Materi</label>
                            <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" aria-describedby="file_input_help" id="file_input" name="file_material_update" type="file">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">Pdf (max. 10mb)</p>
                        </div>
                        <button type="submit" class="w-full text-white mt-5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="edit-meeting-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Edit pertemuan
                    </h3>
                    <button type="button" class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="edit-meeting-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5">
                    <form class="space-y-4" action="{{route('update-meetings')}}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="text" name="meeting_id" id="meeting_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" hidden />
                        <div>
                            <label for="meeting_edit" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Judul</label>
                            <input type="text" name="meeting_edit" id="meeting_edit" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Pertemuan-" required />
                        </div>
                        <div>
                            <label for="desc_meeting_edit" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Deskripsi</label>
                            <textarea id="desc_meeting_edit" name="desc_meeting_edit" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 "></textarea>
                        </div>
                        <div>
                            <label for="nip" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Dibuat Oleh</label>
                            <input type="text" value="{{$user->userable->name}}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full p-2.5" readonly/>
                        </div>
                        <div>
                            <label for="date-range-picker" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jadwal</label>
                            <div id="date-range-picker" class="flex items-center">
                                <div class="relative">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                                        </svg>
                                    </div>
                                    <input id="datepicker-range-start_edit" datepicker datepicker-orientation ="top" name="start" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5" placeholder="Tanggal Mulai" required>
                                </div>
                                <span class="mx-4 text-gray-500">to</span>
                                <div class="relative">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                                        </svg>
                                    </div>
                                    <input id="datepicker-range-end_edit" datepicker datepicker-orientation ="top"  name="end" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5" placeholder="Tanggal Selesai" required>
                                </div>
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

        if (document.getElementById("meeting-table") && typeof simpleDatatables.DataTable !== 'undefined') {
            const dataTable = new simpleDatatables.DataTable("#meeting-table", {
                searchable: true,
                sortable: false
            });
        }

        function openEditModal(id, title, desc, start, end) {
            document.getElementById('meeting_id').value = id;
            document.getElementById('meeting_edit').value = title;
            document.getElementById('desc_meeting_edit').value = desc;
            document.getElementById('datepicker-range-start_edit').value = start;
            document.getElementById('datepicker-range-end_edit').value = end;

            document.getElementById('edit-meeting-modal').classList.remove('hidden');
        }

        document.querySelector('[data-modal-hide="edit-meeting-modal"]').addEventListener('click', function () {
            document.getElementById('edit-meeting-modal').classList.add('hidden');
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

        $(document).ready(function() {
            $("#type").select2({
                placeholder: 'Pilih Sekolah',
                language: 'id',
                allowClear: true,
                width: '100%',
                theme: "classic"
            });
        })
    </script>
@endsection