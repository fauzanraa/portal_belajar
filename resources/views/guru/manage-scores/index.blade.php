@extends('layout-admins.app')

@section('content')
    <div class="w-full rounded-bl-xl p-5 pl-8 bg-white">   
        <h1>Manajemen Nilai</h1>
        <p class="text-[10px] tracking-[5px] text-slate-200">Halaman untuk guru mengelola nilai siswa</p>
    </div>

    <div class="mt-10 rounded-tl-xl bg-white p-5 pl-8">
        <div class="space-y-4 p-5 pl-8">
            @foreach ($sessionMeeting as $session)
                <div class="mt-5 group bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100 overflow-hidden">
                    <div class="flex">
                        <div class="relative bg-gradient-to-r from-sky-500 to-indigo-600 p-6 text-white w-80">
                            <div class="absolute top-0 right-0 w-24 h-24 bg-white opacity-10 rounded-full -mr-12 -mt-12"></div>

                            <!-- Module Icon -->
                            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mb-4 backdrop-blur-sm">
                                <i class="bi bi-book text-xl text-black"></i>
                            </div>

                            <h5 class="text-xl font-bold mb-2 leading-tight">{{$session->title}} : {{$session->description}}</h5>
                            <div class="flex items-center text-sky-100">
                                <i class="bi bi-person-circle text-sm mr-1"></i>
                                <p class="text-sm">Oleh: {{$session->teacher->name}}</p>
                            </div>
                        </div>

                        <div class="flex-1 p-6">
                            <div class="flex justify-between items-center h-full">
                                <div class="flex-1 mr-6">
                                </div>

                                <!-- Action Section -->
                                <div class="w-80">
                                    <!-- Action Button -->
                                    @php
                                        // $encryptedTeacher = Illuminate\Support\Facades\Crypt::encrypt($session->created_by);
                                        $encryptedModul = Illuminate\Support\Facades\Crypt::encrypt($session->id);
                                    @endphp
                                    <a href="{{route('detail-moduls', ['idModul' => $encryptedModul])}}" class="block w-full">
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

@section('script')
    
@endsection