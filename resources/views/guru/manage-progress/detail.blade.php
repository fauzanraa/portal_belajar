@extends('layout-admins.app')

@section('csrf-token')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
    <div class="w-full rounded-bl-xl p-5 pl-8 bg-white">   
        <h1>Detail Progress</h1>
        <p class="text-[10px] tracking-[5px] text-slate-200">Halaman untuk guru melihat detail pengerjaan siswa</p>
    </div>

    <div class="w-full bg-white mt-10 rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="bg-white bg-opacity-20 rounded-xl p-3 mr-4 backdrop-blur-sm">
                        <i class="bi bi-trophy text-blue-500 text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white">List Tugas</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="divide-y divide-gray-200">
            @php $index = 1 @endphp
            @foreach ($dataTugas as $data)
                <div class="group hover:bg-blue-50 transition-all duration-300 cursor-pointer">
                    <div class="px-6 py-5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                                        <span class="text-blue-600 font-bold text-lg">{{$index}}</span>
                                    </div>
                                </div>
                                
                                <!-- Meeting Info -->
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
                                        {{($data->type = $data->type ?? 'pretest') == 'pretest' ? 'Pre-Test' : 'Post-Test'}} : {{$data->title}}
                                    </h3>
                                    <div class="flex items-center mt-2 space-x-4">
                                        @if ($data->status == 'finished')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="bi bi-check-circle-fill mr-1"></i>
                                                Selesai
                                            </span>
                                            <span class="text-xs text-gray-500">
                                                <i class="bi bi-calendar mr-1"></i>
                                                Dikerjakan: {{$data->finished_at ? \Carbon\Carbon::parse($data->finished_at)->locale('id')->isoFormat('D MMMM YYYY') : '-'}}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="bi bi-x-circle-fill mr-1"></i>
                                                Belum Selesai
                                            </span>
                                        @endif

                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-6">
                                @php
                                    $encryptedSession = Illuminate\Support\Facades\Crypt::encrypt($data->id);
                                @endphp

                                @if ($data->status == 'finished')
                                    <button onclick="window.location.href='{{route('summary-progress', $encryptedSession)}}'"  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium opacity-0 group-hover:opacity-100 transform translate-x-2 group-hover:translate-x-0 transition-all duration-300">
                                        <i class="bi bi-eye mr-1"></i>
                                        Detail
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @php $index++ @endphp
            @endforeach
        </div>

    </div>
@endsection

@section('script')
    <script>
        
    </script>
@endsection