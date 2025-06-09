@extends('layout-admins.app')

@section('content')
    <div class="w-full rounded-bl-xl p-5 pl-8 bg-white">   
        <h1>Halaman Modul</h1>
        <p class="text-[10px] tracking-[5px] text-slate-200">Halaman untuk daftar guru yang membuat modul</p>
    </div>

    <div class="w-full bg-white mt-10 rounded-l-xl">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-5 pl-8">
            @foreach ($sessionMeeting as $teacherName => $sessions)
                <div class="mt-5 group bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden">
                    <!-- Header Card dengan Gradient -->
                    <div class="bg-gradient-to-r from-sky-500 to-indigo-600 p-6 text-white relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-white opacity-10 rounded-full -mr-10 -mt-10"></div>
                        <div class="absolute bottom-0 left-0 w-16 h-16 bg-white opacity-10 rounded-full -ml-8 -mb-8"></div>
                        
                        <!-- Avatar Guru -->
                        <div class="flex items-center mb-4">
                            <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-4 backdrop-blur-sm">
                                <i class="bi bi-person text-2xl text-black"></i>
                            </div>
                            <div>
                                <h5 class="text-xl font-bold">{{$teacherName}}</h5>
                                @foreach ($sessions as $sessionTeacher)
                                    <p class="text-sm text-blue-100 opacity-90">NIP: {{$sessionTeacher->teacher->nip}}</p>
                                    @break
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Content Card -->
                    <div class="p-6">
                        <!-- Info Guru -->
                        <div class="mb-4">
                            <div class="flex items-center mb-2">
                                <i class="bi bi-book text-indigo-500 mr-2"></i>
                                <p class="text-gray-800 font-semibold">Pengajar</p>
                            </div>
                        </div>

                        <!-- Stats -->
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="text-center p-3 bg-green-50 rounded-lg">
                                <div class="text-lg font-bold text-green-600">{{count($sessions)}}</div>
                                <div class="text-xs text-green-500">Modul</div>
                            </div>

                            <div class="text-center p-3 bg-green-50 rounded-lg">
                                <div class="text-lg font-bold text-green-600">0%</div>
                                <div class="text-xs text-green-500">Progress</div>
                            </div>
                        </div>

                        <!-- Action Button -->
                        @foreach ($sessions as $sessionTeacher)
                            @php
                                $encryptedTeacher = Illuminate\Support\Facades\Crypt::encrypt($sessionTeacher->teacher->id);    
                            @endphp
                            <a href="{{route('list-meetings', $encryptedTeacher)}}" class="block w-full">
                                <button class="w-full bg-sky-500 hover:from-indigo-600 hover:bg-sky-700 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-300 transform group-hover:scale-105 shadow-md hover:shadow-lg">
                                    <div class="flex items-center justify-center">
                                        <span>Lihat</span>
                                        <i class="bi bi-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
                                    </div>
                                </button>
                            </a>
                            @break
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
