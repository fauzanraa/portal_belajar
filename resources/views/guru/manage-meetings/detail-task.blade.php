@extends('layout-admins.app')

@section('csrf-token')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

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
        <h1>Detail Tugas</h1>
        <p class="text-[10px] tracking-[5px] text-slate-200">Halaman untuk mengatur sesi tugas</p>
    </div>

    <div class="mt-10 bg-white p-5 pl-8 rounded-l-xl">
        <div class="button mt-5 px-5 flex justify-self-end">
            <button data-modal-target="edit-task-modal" data-modal-toggle="edit-task-modal" class="px-5 py-2.5 text-sm rounded-lg bg-sky-500 text-sm hover:bg-sky-700 text-white cursor-pointer" onclick="openEditModal({{ $dataTugas->id }}, '{{ $dataTugas->name }}', '{{ $dataTugas->open_at }}', '{{ $dataTugas->close_at }}', '{{ $dataTugas->duration }}')">
                <i class="bi bi-pencil mr-1"></i> Edit data
            </button>
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
                                {{$dataTugas->name}}
                            </td>
                        </tr>
                        <tr class="bg-white">
                            <th scope="row" class="px-6 py-4 w-1/4 font-medium border border-gray-400 bg-slate-300 text-black">
                                Tipe
                            </th>
                            @if ($dataTugas->type == 'pretest')
                                <td class="px-6 py-4 border-y border-gray-200 border-r border-gray-200">
                                    Pre-test
                                </td>
                            @else
                                <td class="px-6 py-4 border-y border-gray-200 border-r border-gray-200">
                                    Post-test
                                </td>
                            @endif
                        </tr>
                        <tr class="bg-white">
                            <th scope="row" class="px-6 py-4 font-medium border border-gray-400 bg-slate-300 text-black">
                                Jadwal
                            </th>
                            <td class="px-6 py-4 border-y border-gray-200 border-r border-gray-200">
                                {{$dataTugas->open_at ? \Carbon\Carbon::parse($dataTugas->open_at)->locale('id')->isoFormat('D MMMM YYYY') : '-'}} ({{$dataTugas->open_at ? \Carbon\Carbon::parse($dataTugas->open_at)->locale('id')->isoFormat('HH:mm') : '-'}}) - {{$dataTugas->close_at ? \Carbon\Carbon::parse($dataTugas->close_at)->locale('id')->isoFormat('D MMMM YYYY') : '-'}} ({{$dataTugas->close_at ? \Carbon\Carbon::parse($dataTugas->close_at)->locale('id')->isoFormat('HH:mm') : '-'}})
                                {{-- {{$dataTugas->open_at}} - {{$dataTugas->close_at}} --}}
                            </td>
                        </tr>
                        <tr class="bg-white">
                            <th scope="row" class="px-6 py-4 w-1/4 font-medium border border-gray-400 bg-slate-300 text-black">
                                Durasi
                            </th>
                            <td class="px-6 py-4 border-y border-gray-200 border-r border-gray-200">
                                {{$dataTugas->duration}} menit
                            </td>
                        </tr>
                        <tr class="bg-white">
                            <th scope="row" colspan="2" class="px-6 py-4 w-1/4 font-medium border border-gray-400 bg-slate-300 text-black text-center">
                                Pengaturan soal
                            </th>
                        </tr>
                        <tr class="bg-white">
                            <th scope="row" colspan="2" class="px-6 py-4 w-1/4 font-medium border border-gray-200 text-black text-center">
                                @php
                                    $encryptedTask = Illuminate\Support\Facades\Crypt::encrypt($dataTugas->id);
                                @endphp
                                <div class="button flex gap-4 justify-self-end">
                                    @if ($dataSoal->count() > 0)
                                        <button data-modal-target="component-settings-modal" data-modal-toggle="component-settings-modal" 
                                            class="px-5 py-2.5 text-sm rounded-lg bg-sky-500 hover:bg-sky-700 text-white cursor-pointer">
                                            <i class="bi bi-gear mr-1"></i> Atur Komponen
                                        </button>
                                    @endif
                                    <button class="px-5 py-2.5 text-sm rounded-lg bg-sky-500 text-sm hover:bg-sky-700 text-white cursor-pointer" onclick="window.location.href='{{route('question-tasks', $encryptedTask)}}'">
                                        <i class="bi bi-plus-lg mr-1"></i> Tambah Soal
                                    </button>
                                </div>
                                <div class="mt-3">
                                    <table id="question-table">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <span class="flex items-center">
                                                        No
                                                    </span>
                                                </th>
                                                <th>
                                                    <span class="flex items-center">
                                                        Soal
                                                    </span>
                                                </th>
                                                <th>
                                                    <span class="flex items-center">
                                                        Kunci Jawaban <span class="italic">(Expected)</span>
                                                    </span>
                                                </th>
                                                <th>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($dataSoal as $data)
                                                <tr>
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{{$data->question}}</td>
                                                    <td>
                                                        @php
                                                            $encryptedQuestion = Illuminate\Support\Facades\Crypt::encrypt($data->id);
                                                        @endphp

                                                        <button onclick="window.location.href='{{ route('expected-answer', $encryptedQuestion) }}'" class="cursor-pointer">
                                                            <i class="bi bi-eye p-2 text-sm rounded-lg bg-sky-500 hover:bg-sky-700 text-white cursor-pointer mr-4"> Lihat</i>
                                                        </button>

                                                        {{-- @if (!empty($data->correct_answer))
                                                        @else 
                                                            <span>Tidak ada kunci jawaban</span>    
                                                        @endif --}}
                                                    <td>
                                                    @php
                                                        $encryptedQuestion = Illuminate\Support\Facades\Crypt::encrypt($data->id);
                                                    @endphp
                                                        @if (!empty($data->correct_answer))
                                                            <a href="{{ route('draw-correct-answer', ['id' => $encryptedQuestion]) }}" onclick="event.preventDefault(); showConfirmation('{{ route('draw-correct-answer', ['id' => $encryptedQuestion]) }}')" class="p-2 text-sm rounded-lg bg-sky-500 text-sm hover:bg-sky-700 text-white cursor-pointer">
                                                                <i class="bi bi-pencil"></i>
                                                            </a>
                                                        @else 
                                                            <a href="{{ route('draw-correct-answer', ['id' => $encryptedQuestion]) }}" class="p-2 text-sm rounded-lg bg-sky-500 text-sm hover:bg-sky-700 text-white cursor-pointer">
                                                                <i class="bi bi-plus-lg"></i>
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </th>
                        </tr>
                        {{-- @if ($dataTugas->type == 'posttest')  --}}
                        <tr class="bg-white">
                            <th scope="row" class="px-6 py-4 font-medium border border-gray-400 bg-slate-300 text-black">
                                Akses Tugas Via Non Sistem
                            </th>
                            <td class="px-6 py-4 border-y border-gray-200 border-r border-gray-200">
                                <div class="block">
                                    @if($sesiSiswaNonSistem->isEmpty())
                                        <p class="text-gray-500 italic">Belum ada sesi</p>
                                    @else
                                        @foreach($sesiSiswaNonSistem as $className => $students)
                                            <div class="mb-2">
                                                <button class="hover:text-sky-500 cursor-pointer">
                                                    Kelas {{ $className }} - {{ $students->count() }} Siswa
                                                </button>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                <div class="block mt-5 mb-2 flex gap-4">
                                    @php
                                        $encryptedTask = Illuminate\Support\Facades\Crypt::encrypt($dataTugas->id);
                                    @endphp
                                    <a href="{{route('session-tasks', ['id' => $encryptedTask, 'type' => 'non_system'])}}" class="text-sky-500 hover:text-sky-700">
                                        <div class="block">
                                            <i class="bi bi-people-fill"></i><span class="ml-3">Atur sesi</span>
                                        </div>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        {{-- @endif --}}
                        <tr class="bg-white">
                            <th scope="row" class="px-6 py-4 font-medium border border-gray-400 bg-slate-300 text-black">
                                Akses Tugas Via Sistem
                            </th>
                            <td class="px-6 py-4 border-y border-gray-200 border-r border-gray-200">
                                <div class="block">
                                    @if($sesiSiswaSistem->isEmpty())
                                        <p class="text-gray-500 italic">Belum ada sesi</p>
                                    @else
                                        @foreach($sesiSiswaSistem as $className => $students)
                                            <div class="mb-2">
                                                <button class="hover:text-sky-500 cursor-pointer">
                                                    Kelas {{ $className }} - {{ $students->count() }} Siswa
                                                </button>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                <div class="block mt-5 mb-2 flex gap-4">
                                    @php
                                        $encryptedTask = Illuminate\Support\Facades\Crypt::encrypt($dataTugas->id);
                                    @endphp
                                    <a href="{{route('session-tasks', ['id' => $encryptedTask, 'type' => 'system'])}}" class="text-sky-500 hover:text-sky-700">
                                        <div class="block">
                                            <i class="bi bi-people-fill"></i><span class="ml-3">Atur sesi</span>
                                        </div>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="button mt-8 flex justify-self-end">
                    @php
                        $encryptedMeeting = Illuminate\Support\Facades\Crypt::encrypt($dataTugas->meeting_id);
                    @endphp
                    <button type="button" class="px-5 py-2.5 text-sm rounded-lg bg-sky-500 text-sm hover:bg-sky-700 text-white cursor-pointer" onclick="window.location.href='{{ route('manage-materials', ['id' => $encryptedMeeting]) }}'">
                        <i class="bi bi-caret-left mr-1"></i> Kembali
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="edit-task-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Edit tugas
                    </h3>
                    <button type="button" class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="edit-task-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <div class="p-4 md:p-5">
                    <form class="space-y-4" action="{{route('update-tasks', ['id' => $dataTugas->id])}}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="text" name="task_id" id="task_id" hidden>
                        <div>
                            <label for="task" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Judul</label>
                            <input type="text" name="task" id="task" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
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

    <div id="component-settings-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Pengaturan Komponen Flowchart
                    </h3>
                    <button type="button" class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="component-settings-modal">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                    </button>
                </div>
                
                <div class="p-4 md:p-5">
                    <form id="component-settings-form">
                        @csrf
                        <div class="grid grid-cols-2 gap-4">
                            @php
                                $allComponents = [
                                    'Terminator' => 'Terminator',
                                    'Process' => 'Process',
                                    'Decision' => 'Decision',
                                    'OnPageReference' => 'On Page Reference',
                                    'OffPageReference' => 'Off Page Reference',
                                    'Comment' => 'Comment',
                                    'InputOutput' => 'Input/Output',
                                    'ManualOperation' => 'Manual Operation',
                                    'PredefinedProcess' => 'Predefined Process',
                                    'Display' => 'Display',
                                    'Preparation' => 'Preparation'
                                ];
                            @endphp
                            
                            @foreach($allComponents as $key => $label)
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                        id="component_{{ $key }}" 
                                        name="components[]" 
                                        value="{{ $key }}"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500"
                                        {{ in_array($key, $pengaturanKomponen ?? []) ? 'checked' : '' }}>
                                    <label for="component_{{ $key }}" class="ml-2 text-sm font-medium text-gray-900">
                                        {{ $label }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="flex justify-end mt-6 space-x-3">
                            <button type="button" data-modal-hide="component-settings-modal" 
                                    class="px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-100">
                                Batal
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="flowchart-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-4xl max-h-full">
            <div class="relative bg-white rounded-lg shadow-lg dark:bg-gray-700">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Kunci Jawaban Flowchart
                    </h3>
                    <button type="button" class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" onclick="closeFlowchartModal()">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <div class="p-4 md:p-5">
                    <div id="flowchart-container" class="w-full h-96 border border-gray-300 rounded-lg overflow-auto bg-gray-50 flex items-center justify-center">
                        @php
                            $flowchartImg = $data->flowchart_img ?? null;
                        @endphp
                        
                        @if($flowchartImg)
                            <img src="{{ asset('storage/assets/flowcharts/correctAnswer/' . $flowchartImg) }}" alt="Flowchart Siswa" class="max-w-full max-h-full object-contain rounded-lg shadow-sm">
                        @else
                            <div class="text-center text-gray-500">
                                <i class="bi bi-image text-4xl mb-2"></i>
                                <p>Tidak ada gambar flowchart</p>
                            </div>
                        @endif
                    </div>
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

        if (document.getElementById("question-table") && typeof simpleDatatables.DataTable !== 'undefined') {
            const dataTable = new simpleDatatables.DataTable("#question-table", {
                searchable: true,
                sortable: false
            });
        }

        function openEditModal(id, name, open_at, close_at, duration) {
            let dateStart = open_at.split(' ')[0];
            let timeStart = open_at.split(' ')[1];
            let dateEnd = open_at.split(' ')[0];
            let timeEnd = open_at.split(' ')[1];

            document.getElementById('task_id').value = id;
            document.getElementById('task').value = name;
            document.getElementById('datepicker-range-start').value = dateStart;
            document.getElementById('datepicker-range-end').value = dateEnd;
            document.getElementById('time-start').value = timeStart;
            document.getElementById('time-end').value = timeEnd;
            document.getElementById('duration').value = duration;

            document.getElementById('edit-task-modal').classList.remove('hidden');
        }

        document.querySelector('[data-modal-hide="edit-task-modal"]').addEventListener('click', function () {
            document.getElementById('edit-task-modal').classList.add('hidden');
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Add event listeners to all answer buttons
            const answerButtons = document.querySelectorAll('.view-answer-btn');
            
            answerButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const taskId = this.getAttribute('data-task-id');
                    const answerData = this.getAttribute('data-answer');
                    
                    showFlowchartModal(taskId, answerData);
                });
            });
        });

        document.getElementById('component-settings-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const components = formData.getAll('components[]');
            
            fetch(`{{ route("update-components", $dataTugas->id) }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ components: components })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessMessage('Berhasil menambah data!');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showErrorMessage(data.message || 'Gagal menambah data!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });

        function showConfirmation(url) {
            Swal.fire({
                title: 'Perhatian!!!',
                text: 'Anda sudah memiliki kunci jawaban yang tersimpan, apakah anda yakin ingin mengubah kunci jawaban?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Iya',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        }

        function openFlowchartModal(imageUrl) {
            const modal = document.getElementById('flowchart-modal');
            const container = document.getElementById('flowchart-container');

            container.innerHTML = '';

            const img = document.createElement('img');
            img.src = imageUrl;
            img.alt = 'Flowchart Siswa';
            img.className = 'max-w-full max-h-full object-contain rounded-lg shadow-sm';

            container.appendChild(img);

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeFlowchartModal() {
            const modal = document.getElementById('flowchart-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
@endsection