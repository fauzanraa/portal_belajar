@extends('layout-admins.app')

@section('content')
    <div class="w-full rounded-bl-xl p-5 pl-8 bg-white">   
        <h1>Halaman Modul</h1>
        <p class="text-[10px] tracking-[5px] text-slate-200">Halaman untuk melihat detail per modul</p>
    </div>

    <div class="w-full bg-white mt-10 rounded-xl shadow-lg border border-gray-200">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center mr-4">
                        <i class="bi bi-book text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Detail Modul Pembelajaran</h3>
                        {{-- <p class="text-sm text-gray-500">Ikuti tahapan pembelajaran secara berurutan</p> --}}
                    </div>
                </div>
                {{-- <div class="flex items-center bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-medium">
                    <div class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                    Aktif
                </div> --}}
            </div>

            @php
                
            @endphp
            <div class="space-y-6">
                @foreach ($sessionTask as $task)
                    @if (isset($task) && $task->type === 'pretest')
                        <!-- Pre-Test Section -->
                        <div class="group relative">
                            <div class="flex items-center">
                                <!-- Step Number -->
                                <div class="flex-shrink-0 w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center text-white font-bold text-sm mr-4 shadow-lg">
                                    1
                                </div>
                                
                                <!-- Content -->
                                <div class="flex-1 bg-yellow-50 rounded-lg p-4 border-l-4 border-yellow-500 hover:shadow-md transition-all duration-300 cursor-pointer group-hover:bg-yellow-100">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <h4 class="text-lg font-semibold text-gray-800 mb-1">Pre-Test</h4>
                                            <p class="text-sm text-gray-600 mb-2">Uji pemahaman awal sebelum mempelajari materi</p>
                                            <div class="flex items-center text-xs text-gray-500">
                                                <i class="bi bi-clock mr-1"></i>
                                                <span class="mr-4">{{$task->duration}} menit</span>
                                                {{-- <i class="bi bi-question-circle mr-1"></i>
                                                <span>10 soal</span> --}}
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            <span class="px-3 py-1 bg-yellow-200 text-yellow-800 rounded-full text-xs font-medium">Belum Dikerjakan</span>
                                            <button class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center" onclick="window.location.href='{{ route('draw-flowchart', ['idTask' => $task->id]) }}'">
                                                <i class="bi bi-play-fill mr-1"></i>
                                                Mulai
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach

                <!-- Materi Section -->
                <div class="group relative">
                    <div class="flex items-center">
                        <!-- Step Number -->
                        <div class="flex-shrink-0 w-10 h-10 bg-gray-400 rounded-full flex items-center justify-center text-white font-bold text-sm mr-4 shadow-lg">
                            2
                        </div>
                        
                        <!-- Content -->
                        <div class="flex-1 bg-gray-50 rounded-lg p-4 border-l-4 border-gray-500 hover:shadow-md transition-all duration-300 opacity-60">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h4 class="text-lg font-semibold text-gray-800 mb-1">Materi Pembelajaran</h4>
                                    <p class="text-sm text-gray-600 mb-2">Pelajari materi untuk mendapatkan pemahaman terkait modul yang diberikan</p>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="px-3 py-1 bg-gray-200 text-gray-600 rounded-full text-xs font-medium">Terkunci</span>
                                    <button disabled class="bg-gray-300 text-gray-500 px-4 py-2 rounded-lg text-sm font-medium cursor-not-allowed flex items-center">
                                        <i class="bi bi-lock mr-1"></i>
                                        Terkunci
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Post-Test Section -->
                <div class="group relative">
                    <div class="flex items-center">
                        <!-- Step Number -->
                        <div class="flex-shrink-0 w-10 h-10 bg-gray-400 rounded-full flex items-center justify-center text-white font-bold text-sm mr-4 shadow-lg">
                            3
                        </div>
                        
                        <!-- Content -->
                        <div class="flex-1 bg-gray-50 rounded-lg p-4 border-l-4 border-gray-400 hover:shadow-md transition-all duration-300 opacity-60">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h4 class="text-lg font-semibold text-gray-800 mb-1">Post-Test</h4>
                                    <p class="text-sm text-gray-600 mb-2">Evaluasi pemahaman setelah mempelajari materi</p>
                                    <div class="flex items-center text-xs text-gray-500">
                                        <i class="bi bi-clock mr-1"></i>
                                        <span class="mr-4">20 menit</span>
                                        {{-- <i class="bi bi-question-circle mr-1"></i>
                                        <span class="mr-4">15 soal</span> --}}
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="px-3 py-1 bg-gray-200 text-gray-600 rounded-full text-xs font-medium">Terkunci</span>
                                    <button disabled class="bg-gray-300 text-gray-500 px-4 py-2 rounded-lg text-sm font-medium cursor-not-allowed flex items-center">
                                        <i class="bi bi-lock mr-1"></i>
                                        Terkunci
                                    </button>
                                </div>
                            </div>
                            <!-- Unlock Requirements -->
                            {{-- <div class="mt-3 p-2 bg-gray-100 rounded text-xs text-gray-600">
                                <i class="bi bi-info-circle mr-1"></i>
                                Selesaikan semua materi untuk membuka post-test
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>

            {{-- <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="grid grid-cols-4 gap-4">
                    <div class="text-center p-3 bg-gradient-to-br from-green-50 to-green-100 rounded-lg">
                        <div class="text-lg font-bold text-green-600">1/3</div>
                        <div class="text-xs text-green-500">Tahap Selesai</div>
                    </div>
                    <div class="text-center p-3 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
                        <div class="text-lg font-bold text-blue-600">60%</div>
                        <div class="text-xs text-blue-500">Progress Total</div>
                    </div>
                    <div class="text-center p-3 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg">
                        <div class="text-lg font-bold text-purple-600">2.5</div>
                        <div class="text-xs text-purple-500">Jam Belajar</div>
                    </div>
                    <div class="text-center p-3 bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg">
                        <div class="text-lg font-bold text-orange-600">85</div>
                        <div class="text-xs text-orange-500">Poin Earned</div>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>

@endsection
