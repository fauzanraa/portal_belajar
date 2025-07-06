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
        <h1>{{$data_pertemuan->title}} : {{$data_pertemuan->description}}</h1>
        <p class="text-[10px] tracking-[5px] text-slate-200">Halaman untuk menambah bahan pertemuan</p>
    </div>

    <div class="mt-10 bg-white p-5 pl-8 rounded-l-xl">
        <div class="button mt-5 flex gap-4 justify-self-end">
            <button data-modal-target="add-material-modal" data-modal-toggle="add-material-modal" class="px-5 py-2.5 text-sm rounded-lg bg-sky-500 text-sm hover:bg-sky-700 text-white cursor-pointer @if ($pretest->Empty() && $data_pertemuan->type != 'modul') button-disabled @endif" @if ($pretest->Empty() && $data_pertemuan->type != 'modul') disabled @endif>
                <i class="bi bi-plus"></i> Tambah materi
            </button>
            <button 
                data-modal-target="add-task-modal" 
                data-modal-toggle="add-task-modal" 
                class="px-5 py-2.5 text-sm rounded-lg bg-sky-500 text-sm hover:bg-sky-700 text-white cursor-pointer @if ($pretest->isNotEmpty() && $posttest->isNotEmpty()) button-disabled @endif" 
                @if ($pretest->isNotEmpty() && $posttest->isNotEmpty()) disabled @endif>
                <i class="bi bi-plus"></i> Tambah tugas
            </button>
        </div>
        <div class="mt-9">
            <div class="grid grid-row gap-7">
                <div>
                    <p class="font-medium mb-5">Materi :</p>
                    @foreach ($data_materi as $data)
                        @php
                            $encryptedMeeting = Illuminate\Support\Facades\Crypt::encrypt($data->id);
                        @endphp
                        <a href="{{route('detail-materials', $encryptedMeeting)}}" class="flex bg-white border border-gray-200 rounded-lg shadow-sm w-full hover:bg-sky-500 hover:scale-101 transition-all group">
                            <div class="flex-none w-1/4 flex items-center justify-center p-4 border-r border-gray-100 group-hover:border-white">
                                <i class="bi bi-file-earmark text-7xl group-hover:text-white"></i>
                            </div>
                            <div class="flex-1 flex flex-col justify-center p-4 leading-normal">
                                <h5 class="mb-5 text-2xl font-bold tracking-tight text-gray-900 group-hover:text-white">{{$data->name}}</h5>
                                <p class="mb-3 text-xs font-normal text-slate-500 group-hover:text-white">Lihat selengkapnya <i class="bi bi-caret-right"></i></p>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div>
                    <p class="font-medium mb-5">Tugas :</p>
                    @foreach ($data_tugas as $data)
                        @php
                            $encryptedTask = Illuminate\Support\Facades\Crypt::encrypt($data->id);
                        @endphp
                        <a href="{{route('detail-tasks', ['id' => $encryptedTask])}}" class="flex mt-5 bg-white border border-gray-200 rounded-lg shadow-sm w-full hover:bg-sky-500 hover:scale-101 transition-all group">
                            <div class="flex-none w-1/4 flex items-center justify-center p-4 border-r border-gray-100 group-hover:border-white">
                                <i class="bi bi-clipboard text-7xl group-hover:text-white"></i>
                            </div>
                            <div class="flex-1 flex flex-col justify-center p-4 leading-normal">
                                <h5 class="text-2xl font-bold tracking-tight text-gray-900 group-hover:text-white">{{$data->name}}</h5>
                                @if ($data->type == 'pretest')
                                    <p class="mb-5 text-xs text-slate-300">Tipe : Pre-test</p>
                                @else
                                    <p class="mb-5 text-xs text-slate-300">Tipe : Post-test</p>
                                @endif
                                <p class="mb-3 text-xs font-normal text-slate-500 group-hover:text-white">Lihat selengkapnya <i class="bi bi-caret-right"></i></p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            </div>
        </div>
    </div>

    <div id="add-material-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Tambah materi
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
                    <form class="space-y-4" action="{{route('store-materials', ['id' => $id_meeting])}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div>
                            <label for="material" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Judul</label>
                            <input type="text" name="material" id="material" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                        </div>
                        <div>
                            <label for="nip" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Dibuat Oleh</label>
                            <input type="text" value="{{$user->userable->name}}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full p-2.5" readonly/>
                        </div>
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

    <div id="add-task-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Tambah tugas
                    </h3>
                    <button type="button" class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="add-task-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5">
                    <form class="space-y-4" action="{{route('store-tasks', ['id' => $id_meeting])}}" method="POST">
                        @csrf
                        <div>
                            <label for="task" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Judul</label>
                            <input type="text" name="task" id="task" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                        </div>
                        <div>
                            <label for="type" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tipe Tugas</label>
                            <select id="type" name="type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option value="" selected disabled>Pilihan</option>
                                <option value="pretest" @if ($pretest->isNotEmpty() || $data_pertemuan->type != 'modul') disabled @endif>Pre-Test<option>
                                <option value="posttest" @if ($pretest->isEmpty() && $data_pertemuan->type === 'modul') disabled @endif>Post-Test</option>
                            </select>
                        </div>
                        <div>
                            <label for="nip" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Dibuat Oleh</label>
                            <input type="text" value="{{$user->userable->name}}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full p-2.5" readonly/>
                        </div>
                        <div>
                            <label for="datepicker" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jadwal</label>
                            <div id="date-range-picker" class="flex items-center gap-3">
                                <div class="flex-1 space-y-2">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                                            </svg>
                                        </div>
                                        <input id="datepicker-range-start" datepicker datepicker-orientation="top" name="start" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5" placeholder="Tanggal mulai">
                                    </div>
                                    
                                    <div class="flex">
                                        <input type="time" id="time-start" name="time_start" class="bg-gray-50 border border-gray-300 text-gray-900 leading-none focus:ring-blue-500 focus:border-blue-500 block flex-1 w-full text-sm rounded-s-lg p-2.5" required>
                                        <span class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-s-0 border-gray-300 rounded-e-lg dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
                                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z" clip-rule="evenodd"/>
                                            </svg>
                                        </span>
                                    </div>
                                </div>

                                <div class="flex-shrink-0 px-2">
                                    <span class="text-gray-500 dark:text-gray-400 font-medium text-sm whitespace-nowrap">sampai</span>
                                </div>

                                <div class="flex-1 space-y-2">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                                            </svg>
                                        </div>
                                        <input id="datepicker-range-end" datepicker datepicker-orientation="top" name="end" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5" placeholder="Tanggal selesai">
                                    </div>
                                    
                                    <div class="flex">
                                        <input type="time" id="time-end" name="time_end" class="bg-gray-50 border border-gray-300 text-gray-900 leading-none focus:ring-blue-500 focus:border-blue-500 block flex-1 w-full text-sm rounded-s-lg p-2.5" required>
                                        <span class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-s-0 border-gray-300 rounded-e-lg dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
                                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z" clip-rule="evenodd"/>
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label for="duration" class="block mb-2 text-sm font-medium text-gray-900">Durasi</label>
                            <input type="text" inputmode="numeric" name="duration" id="duration" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                            <p class="text-red-300 text-xs pt-1">nb : tulis dalam satuan menit</p>
                        </div>
                        <button type="submit" class="w-full text-white mt-1 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Simpan</button>
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

        document.addEventListener('DOMContentLoaded', function () {
            @if ($data_pertemuan->type === 'modul')
                @if ($pretest->isEmpty())
                    Swal.fire({
                        title: 'Peringatan!',
                        text: 'Harap buat pretest terlebih dahulu.',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const modalButton = document.querySelector('[data-modal-target="add-task-modal"]');
                            if (modalButton) {
                                modalButton.click();
                            }
                        }
                    });
                @endif
            @endif
            
        });
        
        if (document.getElementById("meeting-table") && typeof simpleDatatables.DataTable !== 'undefined') {
            const dataTable = new simpleDatatables.DataTable("#meeting-table", {
                searchable: true,
                sortable: false
            });
        }

        $(document).ready(function() {
            $("#type").select2({
                placeholder: 'Pilih Tipe',
                language: 'id',
                allowClear: true,
                width: '100%',
                theme: "classic"
            });
        })
    </script>
@endsection