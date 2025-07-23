@extends('layout-admins.app')

@section('content')
    <div class="w-full rounded-bl-xl p-5 pl-8 bg-white">   
        <h1>Halaman Modul</h1>
        <p class="text-[10px] tracking-[5px] text-slate-200">Halaman untuk melihat detail per modul</p>
    </div>

    <div class="w-full bg-white mt-10 rounded-l-xl shadow-lg border border-gray-200">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center mr-4">
                        <i class="bi bi-book text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-l font-bold text-gray-800">{{$meeting->title}} : {{$meeting->description}}</h3>
                    </div>
                </div>
            </div>

            @php
                
            @endphp
            <div class="space-y-6">
                @php $stepNumber = 1; @endphp

                {{-- Pre-Test --}}
                @foreach ($sessionTask->where('type', 'pretest') as $task)
                    <div class="group relative">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center text-white font-bold text-sm mr-4 shadow-lg">
                                {{ $stepNumber++ }}
                            </div>

                            <div class="flex-1 bg-yellow-50 rounded-lg p-4 border-l-4 border-yellow-500 hover:shadow-md transition-all duration-300 cursor-pointer group-hover:bg-yellow-100">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-800 mb-1">Pre-Test</h4>
                                        <p class="text-sm text-gray-600 mb-2">Uji pemahaman awal sebelum mempelajari materi</p>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        @php
                                            $studentSession = $task->studentTaskSession->where('task_session_id', $task->id)->first();

                                            $encryptedTask = Illuminate\Support\Facades\Crypt::encrypt($studentSession->task_session_id);
                                        @endphp

                                        @if ($studentSession->status == 'finished')
                                            <span class="px-3 py-1 bg-green-200 text-green-800 rounded-full text-xs font-medium">Selesai</span>

                                            <button disabled class="bg-gray-300 text-gray-500 px-4 py-2 rounded-lg text-sm font-medium cursor-not-allowed flex items-center">
                                                <i class="bi bi-lock mr-1"></i> Terkunci
                                            </button>
                                        @else
                                            <span class="px-3 py-1 bg-yellow-200 text-yellow-800 rounded-full text-xs font-medium">Belum Dikerjakan</span>

                                            <a href="{{ route('draw-flowchart', ['idTask' => $encryptedTask]) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center startTask">
                                                <i class="bi bi-play-fill mr-1"></i> Mulai
                                            </a>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Materi --}}
                @foreach ($sessionMaterial as $material)
                    <div class="group relative">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-10 h-10 {{ $isPreTestDone ? 'bg-blue-500' : 'bg-gray-400' }} rounded-full flex items-center justify-center text-white font-bold text-sm mr-4 shadow-lg">
                                {{ $stepNumber++ }}
                            </div>

                            <div class="flex-1 bg-gray-50 rounded-lg p-4 border-l-4 {{ $isPreTestDone ? 'border-blue-500' : 'border-gray-500 opacity-60' }} transition-all duration-300">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-800 mb-1">Materi Pembelajaran</h4>
                                        <p class="text-sm text-gray-600 mb-2">Pelajari materi yang tersedia</p>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        @if ($isPreTestDone)
                                            <a href="{{asset('storage/assets/materials/' .$material->file)}}" target="_blank" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center">
                                                <i class="bi bi-book mr-1"></i> Lihat
                                            </a>
                                        @else
                                            <span class="px-3 py-1 bg-gray-200 text-gray-600 rounded-full text-xs font-medium">Terkunci</span>
                                            <button disabled class="bg-gray-300 text-gray-500 px-4 py-2 rounded-lg text-sm font-medium cursor-not-allowed flex items-center">
                                                <i class="bi bi-lock mr-1"></i> Terkunci
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Post-Test --}}
                @foreach ($sessionTask->where('type', 'posttest') as $task)
                    <div class="group relative">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-10 h-10 {{ $isPreTestDone ? 'bg-purple-500' : 'bg-gray-400' }} rounded-full flex items-center justify-center text-white font-bold text-sm mr-4 shadow-lg">
                                {{ $stepNumber++ }}
                            </div>

                            <div class="flex-1 bg-gray-50 rounded-lg p-4 border-l-4 {{ $isPreTestDone ? 'border-purple-500' : 'border-gray-500 opacity-60' }} transition-all duration-300">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-800 mb-1">Post-Test</h4>
                                        <p class="text-sm text-gray-600 mb-2">Evaluasi pemahaman setelah belajar</p>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        @php
                                            $studentSession = $task->studentTaskSession->where('task_session_id', $task->id)->first();

                                            $encryptedTask = Illuminate\Support\Facades\Crypt::encrypt($studentSession->task_session_id);
                                        @endphp

                                        @if ($isPreTestDone)
                                            @if ($studentSession->status == 'finished')
                                                <span class="px-3 py-1 bg-green-200 text-green-800 rounded-full text-xs font-medium">Selesai</span>

                                                <button disabled class="bg-gray-300 text-gray-500 px-4 py-2 rounded-lg text-sm font-medium cursor-not-allowed flex items-center">
                                                    <i class="bi bi-lock mr-1"></i> Terkunci
                                                </button>
                                            @else
                                                <span class="px-3 py-1 bg-yellow-200 text-yellow-800 rounded-full text-xs font-medium">Belum Dikerjakan</span>

                                                <a href="{{ route('draw-flowchart', ['idTask' => $encryptedTask]) }}" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center startTask">
                                                    <i class="bi bi-play-fill mr-1"></i> Mulai
                                                </a>
                                            @endif
                                        @else 
                                            <span class="px-3 py-1 bg-gray-200 text-gray-600 rounded-full text-xs font-medium">Terkunci</span>

                                            <button disabled class="bg-gray-300 text-gray-500 px-4 py-2 rounded-lg text-sm font-medium cursor-not-allowed flex items-center">
                                                <i class="bi bi-lock mr-1"></i> Terkunci
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const startTaskLinks = document.querySelectorAll('.startTask'); 

            startTaskLinks.forEach(function(startTaskLink) {
                startTaskLink.addEventListener('click', function (e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Perhatian',
                        html: `
                            <span class="text-l"> Apakah anda yakin ingin mulai mengerjakan?
                            <div class="mt-2">
                                <span class="text-xs text-red-500"> nb: anda tidak disarankan untuk berpindah halaman karena pengerjaan tidak akan disimpan namun, timer akan terus berjalan
                            </div
                        `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Lanjutkan',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = startTaskLink.href;
                        }
                    });
                });
            });
        });
    </script>
@endsection
