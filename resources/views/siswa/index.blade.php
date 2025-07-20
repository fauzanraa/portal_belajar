@extends('layout-admins.app')

@section('content')
    <div class="w-full rounded-bl-xl p-5 pl-8 bg-white">   
        <h1>Halaman Beranda</h1>
        <p class="text-[10px] tracking-[5px] text-slate-200">Ingin apa hari ini</p>
    </div>

    <div class="w-full">
        <div class="mt-10">
            <div class="p-5 px-8 rounded-2xl bg-white relative group min-h-[200px]">
                <p class="font-bold text-4xl">Halo, {{$user->userable->name}}!</p>
                <p class="text-slate-300 text-xs mt-2">Ayo lanjutkan pekerjaanmu 😃</p>
                @if ($student->gender == 'L')
                    <img src="{{asset('img/avatar-user-male.png')}}" alt="" class="absolute right-0 bottom-0 w-60 h-60 transform  -translate-x-5 translate-y-1">
                @else
                    <img src="{{asset('img/avatar-user-female.png')}}" alt="" class="absolute right-0 bottom-0 w-60 h-60 transform  -translate-x-5 translate-y-1">
                @endif
            </div>
        </div>
    </div>

    {{-- <div class="flex gap-6 w-full mt-10">
        <div class="w-2/3 space-y-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Modul Terbaru</h3>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center p-3 bg-blue-50 rounded-lg border-l-4 border-blue-500 hover:shadow-md transition-shadow cursor-pointer">
                        <div class="flex-shrink-0 w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                            <i class="bi bi-book text-white"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-semibold text-gray-800">Dasar Flowchart</h4>
                            <p class="text-xs text-gray-500">Modul 1 • Fauzan</p>
                        </div>
                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">Selesai</span>
                    </div>
                    
                    <div class="flex items-center p-3 bg-yellow-50 rounded-lg border-l-4 border-yellow-500 hover:shadow-md transition-shadow cursor-pointer">
                        <div class="flex-shrink-0 w-10 h-10 bg-yellow-500 rounded-lg flex items-center justify-center mr-3">
                            <i class="bi bi-book text-white"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-semibold text-gray-800">Dasar Flowchart</h4>
                            <p class="text-xs text-gray-500">Modul 2 • Fauzan</p>
                        </div>
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full">Berlangsung</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Tugas Mendekati Deadline</h3>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center p-3 bg-red-50 rounded-lg border-l-4 border-red-500 hover:shadow-md transition-shadow cursor-pointer">
                        <div class="flex-shrink-0 w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center mr-3">
                            <i class="bi bi-exclamation-triangle-fill text-white"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-semibold text-gray-800">Dasar Flowchart</h4>
                            <p class="text-xs text-gray-500">Deadline: 2 hari lagi</p>
                        </div>
                        <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded-full">Urgent</span>
                    </div>
                    
                    <div class="flex items-center p-3 bg-orange-50 rounded-lg border-l-4 border-orange-500 hover:shadow-md transition-shadow cursor-pointer">
                        <div class="flex-shrink-0 w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center mr-3">
                            <i class="bi bi-clock-fill text-white"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-semibold text-gray-800">Dasar Flowchart</h4>
                            <p class="text-xs text-gray-500">Deadline: 5 hari lagi</p>
                        </div>
                        <span class="px-2 py-1 bg-orange-100 text-orange-700 text-xs rounded-full">Segera</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-1/3">
            <div class="bg-white rounded-lg shadow-md p-6 h-full">
                <div class="flex items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">Progress Keseluruhan</h3>
                </div>
                
                <div class="flex flex-col items-center">
                    <div class="relative w-40 h-40 mb-6">
                        <div id="donut-chart" class="w-full h-full"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-800">65%</div>
                                <div class="text-xs text-gray-500">Selesai</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-2 w-full">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                                <span class="text-sm text-gray-600">Selesai</span>
                            </div>
                            <span class="text-sm font-medium text-gray-800">13 Modul</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                                <span class="text-sm text-gray-600">Berlangsung</span>
                            </div>
                            <span class="text-sm font-medium text-gray-800">2 Modul</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-gray-400 rounded-full mr-2"></div>
                                <span class="text-sm text-gray-600">Belum Mulai</span>
                            </div>
                            <span class="text-sm font-medium text-gray-800">5 Modul</span>
                        </div>
                    </div>

                    <div class="mt-6 p-4 bg-gradient-to-r from-purple-50 to-blue-50 rounded-lg w-full">
                        <div class="text-center">
                            <div class="text-lg font-bold text-purple-600">13/20</div>
                            <div class="text-xs text-gray-600">Modul Diselesaikan</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
@endsection

