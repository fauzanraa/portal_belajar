@extends('layout-admins.app')

@section('content')
    <div class="w-full rounded-bl-xl p-5 pl-8 bg-white">   
        <h1>Halaman Nilai</h1>
        <p class="text-[10px] tracking-[5px] text-slate-200">Halaman untuk melihat daftar nilai pengerjaan</p>
    </div>
        
    <div class="w-full bg-white mt-10 rounded-xl shadow-lg overflow-hidden">
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="bg-white bg-opacity-20 rounded-xl p-3 mr-4 backdrop-blur-sm">
                        <i class="bi bi-trophy text-blue-500 text-2xl"></i>
                    </div>
                    {{-- <i class="bi bi-trophy text-3xl text-yellow-300 mr-3"></i> --}}
                    <div>
                        <h2 class="text-xl font-bold text-white">List Nilai</h2>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-yellow-300 text-sm font-medium">Pertemuan Selesai</div>
                    <div class="text-white text-2xl font-bold">{{$completedModules->count()}}</div>
                </div>
            </div>
        </div>

        <!-- Meetings List -->
        @if (isset($completedModules) && $completedModules->count() > 0)
            <div class="divide-y divide-gray-200">
                <div class="group hover:bg-blue-50 transition-all duration-300 cursor-pointer">
                    @php $index = 1 @endphp
                    @foreach ($completedModules as $completedIndex => $completed)
                        <div class="px-6 py-5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <!-- Meeting Number -->
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                                            <span class="text-blue-600 font-bold text-lg">{{$index}}</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Meeting Info -->
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
                                            {{$completed->title}} : {{$completed->description}}
                                        </h3>
                                        {{-- <p class="text-sm text-gray-600 mt-1">
                                            Materi dasar algoritma dan flowchart • 3 Tugas • 1 Quiz
                                        </p> --}}
                                        <div class="flex items-center mt-2 space-x-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="bi bi-check-circle-fill mr-1"></i>
                                                Selesai
                                            </span>
                                            {{-- <span class="text-xs text-gray-500">
                                                <i class="bi bi-calendar mr-1"></i>
                                                Dikerjakan: 15 Nov 2024
                                            </span> --}}
                                        </div>
                                    </div>
                                </div>
                                
                                @php
                                    if ($completed->posttest_score >= 70){
                                        $colorClass = 'text-green-500';
                                    } elseif ($completed->posttest_score >= 50) {
                                        $colorClass = 'text-yellow-500';
                                    } else {
                                        $colorClass = 'text-red-500';
                                    } 
                                @endphp
                                <!-- Score Section -->
                                <div class="flex items-center space-x-6">
                                    @php
                                        $encryptedTask = Illuminate\Support\Facades\Crypt::encrypt($completed->task_session_id);
                                    @endphp
                                    <!-- Details Button -->
                                    <button onclick="window.location.href='{{ route('summary', ['idTask' => $encryptedTask, 'from' => 'list-scores']) }}'"  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium opacity-0 group-hover:opacity-100 transform translate-x-2 group-hover:translate-x-0 transition-all duration-300">
                                        <i class="bi bi-eye mr-1"></i>
                                        Detail
                                    </button>

                                    <div class="relative">
                                        <div class="w-16 h-16">
                                            <svg class="w-16 h-16 transform -rotate-90" viewBox="0 0 36 36">
                                                <path class="text-gray-200" stroke="currentColor" stroke-width="3" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"></path>
                                                <path class="{{ $colorClass }}" stroke="currentColor" stroke-width="3" fill="none" stroke-dasharray="{{$completed->posttest_score}}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"></path>
                                            </svg>
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <span class="text-lg font-bold {{ $colorClass }}">{{$completed->posttest_score}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Progress Details (Expandable) -->
                            <div class="mt-4 hidden group-hover:block transition-all duration-300">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="text-center">
                                            <div class="text-2xl font-bold text-blue-600">{{$completed->completed_tasks}}/{{$completed->total_tasks}}</div>
                                            <div class="text-xs text-gray-600">Tugas</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-2xl font-bold text-green-600">{{$completed->material->count()}}/{{$completed->material->count()}}</div>
                                            <div class="text-xs text-gray-600">Materi</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>          
                    @endforeach
                    @php $index++ @endphp
                </div>
            </div>
        @else
            <div class="flex justify-center items-center min-h-[400px] p-6">
                <div class="max-w-md w-full">
                    <!-- Main Card -->
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden transform hover:scale-105 transition-all duration-300">
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-100 px-8 py-6 text-center">
                            <div class="relative">
                                <div class="w-20 h-20 mx-auto bg-gradient-to-r from-blue-400 to-indigo-500 rounded-full flex items-center justify-center mb-4 shadow-lg">
                                    <i class="bi bi-journal-bookmark text-white text-3xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="px-6 py-4 text-center">
                            <h3 class="text-xl font-bold text-gray-800 mb-3">
                                Belum Ada Modul Terselesaikan
                            </h3>
                            <p class="text-gray-600 leading-relaxed mb-6">
                                Mulai mengerjakan tugas pada modul untuk melihat nilai dan progress anda di sini.
                            </p>

                            <!-- Action Button -->
                            <a href="{{route('list-teachers')}}" 
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-lg">
                                <i class="bi bi-arrow-left mr-2"></i>
                                <span>Kembali ke Dashboard</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- <div id="scoreModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden opacity-0 transition-opacity duration-300">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div id="modalContent" class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden transform scale-95 transition-transform duration-300">
                
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4 relative">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <i class="bi bi-graph-up text-2xl text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white">Detail Nilai Pertemuan</h3>
                                <p class="text-blue-100 text-sm">Pertemuan 1: Pengenalan Algoritma</p>
                            </div>
                        </div>
                        <button onclick="closeModal()" class="text-white hover:text-gray-200 transition-colors p-2 hover:bg-white hover:bg-opacity-10 rounded-full">
                            <i class="bi bi-x-lg text-xl"></i>
                        </button>
                    </div>
                </div>

                <div class="p-6 overflow-y-auto max-h-[calc(90vh-200px)]">
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-xl border border-green-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-green-600 text-sm font-medium">Nilai Terakhir</p>
                                    <p class="text-2xl font-bold text-green-700">20</p>
                                </div>
                                <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                    <i class="bi bi-trophy text-white"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-xl border border-blue-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-blue-600 text-sm font-medium">Total Percobaan</p>
                                    <p class="text-2xl font-bold text-blue-700">1</p>
                                </div>
                                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                                    <i class="bi bi-file-earmark-text text-white"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-xl border border-purple-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-purple-600 text-sm font-medium">Rasio Kesalahan</p>
                                    <p class="text-2xl font-bold text-purple-700"></p>
                                </div>
                                <div class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center">
                                    <i class="bi bi-question-circle text-white"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-4 rounded-xl border border-orange-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-orange-600 text-sm font-medium">Waktu Total</p>
                                    <p class="text-2xl font-bold text-orange-700">5 menit</p>
                                </div>
                                <div class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center">
                                    <i class="bi bi-clock text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h4 class="text-lg font-semibold text-gray-800 flex items-center">
                            <i class="bi bi-list-check mr-2 text-blue-600"></i>
                            Detail Pengerjaan
                        </h4>
                        
                        <div class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                        <i class="bi bi-check-circle-fill text-green-600"></i>
                                    </div>
                                    <div>
                                        <h5 class="font-semibold text-gray-800">Pre-Test</h5>
                                        <div class="flex items-center mt-1 space-x-3">
                                            <span class="text-xs text-gray-500">
                                                <i class="bi bi-calendar mr-1"></i>
                                                Tanggal
                                            </span>
                                            <span class="text-xs text-gray-500">
                                                <i class="bi bi-clock mr-1"></i>
                                                8 menit
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <div class="flex justify-between text-xs text-gray-600 mb-1">
                                    <span>Ketepatan Jawaban</span>
                                    <span>20%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-red-500 h-2 rounded-full" style="width: 20%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

@endsection

{{-- @section('script')
    <script>
        function openModal() {
            const modal = document.getElementById('scoreModal');
            const modalContent = document.getElementById('modalContent');
            
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            }, 10);
        }

        function closeModal() {
            const modal = document.getElementById('scoreModal');
            const modalContent = document.getElementById('modalContent');
            
            modal.classList.add('opacity-0');
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // Close modal when clicking outside
        document.getElementById('scoreModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
@endsection --}}
