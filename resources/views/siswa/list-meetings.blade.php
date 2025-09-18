@extends('layout-admins.app')

@section('content')
    <div class="w-full rounded-bl-xl p-5 pl-8 bg-white">   
        <h1>Halaman Modul</h1>
        <p class="text-[10px] tracking-[5px] text-slate-200">Halaman untuk melihat daftar modul</p>
    </div>

    <div class="w-full bg-white mt-10 rounded-xl">
    <div class="space-y-4 p-4 sm:p-5">
        @foreach ($sessionMeeting as $session)
            <div class="group bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100 overflow-hidden">
                <div class="flex flex-col lg:flex-row">
                    
                    <!-- Bagian Kiri: Info Sesi -->
                    <div class="relative bg-gradient-to-r from-sky-500 to-indigo-600 p-6 text-white w-full lg:w-80">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-white opacity-10 rounded-full -mr-12 -mt-12"></div>

                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mb-4 backdrop-blur-sm">
                            <i class="bi bi-book text-xl text-black"></i>
                        </div>

                        <h5 class="text-lg lg:text-xl font-bold mb-2 leading-tight">
                            {{ $session->title }} : {{ $session->description }}
                        </h5>

                        <div class="flex items-center text-sky-100">
                            <i class="bi bi-person-circle text-sm mr-1"></i>
                            <p class="text-sm">Oleh: {{ $session->teacher->name }}</p>
                        </div>
                    </div>

                    <!-- Bagian Kanan: Detail, Materi, Progress -->
                    <div class="flex-1 p-4 sm:p-6">
                        <div class="flex flex-col lg:flex-row justify-between gap-4">
                            
                            <!-- Materi & Tugas -->
                            <div class="flex-1">
                                <div class="grid grid-cols-2 gap-3 mb-4">
                                    <div class="text-center p-2 bg-blue-50 rounded-lg">
                                        <div class="text-sm font-bold text-blue-600">
                                            {{ isset($materialSession[$session->id]) ? $materialSession[$session->id]->count() : 0 }}
                                        </div>
                                        <div class="text-xs text-blue-500">Materi</div>
                                    </div>
                                    <div class="text-center p-2 bg-green-50 rounded-lg">
                                        <div class="text-sm font-bold text-green-600">
                                            {{ isset($taskSession[$session->id]) ? $taskSession[$session->id]->count() : 0 }}
                                        </div>
                                        <div class="text-xs text-green-500">Tugas</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Progress dan Tombol -->
                            <div class="w-full lg:w-80">
                                <div class="mb-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-gray-700">Progress Pengerjaan</span>
                                        <span class="text-sm font-bold text-sky-600">
                                            {{ $progressMeeting[$session->id] ?? 0 }}%
                                        </span>
                                    </div>

                                    @php
                                        $progress = $progressMeeting[$session->id] ?? 0;
                                        $colorClass = $progress >= 50 ? 'sky' : 'red';
                                        $gradientClass = $progress >= 50 ? 'blue' : 'orange';
                                    @endphp

                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-gradient-to-r from-{{ $colorClass }}-500 to-{{ $gradientClass }}-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                                    </div>
                                </div>

                                <!-- Tombol -->
                                @php
                                    $encryptedTeacher = Illuminate\Support\Facades\Crypt::encrypt($session->created_by);
                                    $encryptedMeeting = Illuminate\Support\Facades\Crypt::encrypt($session->id);
                                @endphp
                                <a href="{{ route('detail-meetings', ['idTeacher' => $encryptedTeacher, 'idMeeting' => $encryptedMeeting]) }}" class="block w-full">
                                    <button class="w-full bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-300 transform group-hover:scale-105 shadow-md hover:shadow-lg">
                                        <div class="flex items-center justify-center">
                                            <span>Lihat</span>
                                            <i class="bi bi-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
                                        </div>
                                    </button>
                                </a>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        @endforeach
    </div>
</div>

@endsection
