@extends('layout-admins.app')

@section('content')
    <div class="w-full rounded-bl-xl p-5 pl-8 bg-white">   
        <h1>Hasil Pengerjaan</h1>
    </div>

    <div class="w-full bg-white mt-10 rounded-l-xl">
        <div class="w-full bg-white mt-10 rounded-l-xl shadow-lg border border-gray-200">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-6 text-white rounded-t-xl">
                <div class="flex items-center">
                    <div class="bg-white bg-opacity-20 rounded-xl p-3 mr-4 backdrop-blur-sm">
                        <i class="bi bi-clipboard-data text-blue-500 text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold">{{$taskSession->type == 'pretest' ? 'Pre-test' : 'Post-Test'}} ({{$taskSession->meeting->title}})</h1>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Nilai Card -->
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 border-l-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-600 text-sm font-medium uppercase tracking-wide">Nilai</p>
                                <p class="text-3xl font-bold text-green-700 mt-2">{{$studentSession->score}}</p>
                            </div>
                            <div class="bg-green-500 rounded-full p-3">
                                <i class="bi bi-trophy text-white text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Waktu Pengerjaan Card -->
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border-l-4 border-blue-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-600 text-sm font-medium uppercase tracking-wide">Waktu</p>
                                <p class="text-3xl font-bold text-blue-700 mt-2">{{gmdate('i:s', $studentSession->duration)}}</p>
                            </div>
                            <div class="bg-blue-500 rounded-full p-3">
                                <i class="bi bi-clock text-white text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Rasio Kesalahan Card -->
                    <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-6 border-l-4 border-orange-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-orange-600 text-sm font-medium uppercase tracking-wide">Rasio Kesalahan</p>
                                <p class="text-3xl font-bold text-orange-700 mt-2">{{$ratioError}}%</p>
                            </div>
                            <div class="bg-orange-500 rounded-full p-3">
                                <i class="bi bi-exclamation-triangle text-white text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Efisiensi Card -->
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 border-l-4 border-purple-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-600 text-sm font-medium uppercase">Hasil Evaluasi</p>
                                <p class="text-2xl font-bold text-purple-700 mt-2">{{$evaluation}}</p>
                            </div>
                            <div class="bg-purple-500 rounded-full p-3">
                                <i class="bi bi-lightning text-white text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Comparison Section -->
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <div class="bg-indigo-100 rounded-lg p-2 mr-3">
                                <i class="bi bi-diagram-3 text-indigo-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">Flowchart</h3>
                            </div>
                        </div>
                    </div>

                    <!-- Question Box -->
                    <div class="bg-white rounded-lg border border-gray-300 p-6 mb-6">
                        <div class="flex items-center mb-4">
                            <div class="bg-orange-100 rounded-lg p-2 mr-3">
                                <i class="bi bi-question-circle text-orange-600"></i>
                            </div>
                            <h4 class="font-semibold text-gray-800">Soal</h4>
                        </div>
                        <div class="text-gray-700 leading-relaxed">
                            <p id="current-question">
                                @if($taskQuestions->count() > 0)
                                    {{ $taskQuestions->first()->question ?? 'Buatlah flowchart untuk menyelesaikan permasalahan berikut...' }}
                                @else
                                    Buatlah flowchart untuk menyelesaikan permasalahan berikut...
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Question Number Navigation -->
                    <div class="bg-white rounded-lg border border-gray-300 p-4 mb-6">
                        <div class="flex items-center mb-3">
                            <div class="bg-purple-100 rounded-lg p-2 mr-3">
                                <i class="bi bi-list-ol text-purple-600"></i>
                            </div>
                            <h4 class="font-semibold text-gray-800">Nomor Soal</h4>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach($taskQuestions as $index => $question)
                                <button class="question-nav-btn w-10 h-10 rounded-lg border-2 font-semibold transition-all duration-200 
                                    {{ $index == 0 ? 'bg-blue-500 text-white border-blue-500' : 'bg-white text-gray-600 border-gray-300 hover:border-blue-400 hover:text-blue-600' }}"
                                    data-question-id="{{ $question->id }}"
                                    data-question-index="{{ $index }}">
                                    {{ $index + 1 }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Flowchart Display Area -->
                    <div id="flowchartContainer">
                        <div id="comparisonView" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Student Side -->
                            <div class="bg-white rounded-lg border-2 border-blue-300 p-4">
                                <div class="flex items-center mb-4 pb-2 border-b border-blue-200">
                                    <div class="bg-blue-100 rounded-lg p-2 mr-3">
                                        <i class="bi bi-person-fill text-blue-600"></i>
                                    </div>
                                    <h4 class="font-semibold text-blue-800">Pengerjaan Siswa</h4>
                                </div>
                                <div class="min-h-[350px] bg-blue-50 rounded-lg flex items-center justify-center" id="student-flowchart">
                                    @if($taskQuestions->count() > 0 && isset($answersMap[$taskQuestions->first()->id]) && $answersMap[$taskQuestions->first()->id]->flowchart_img)
                                        <img src="{{ asset('storage/assets/flowcharts/studentAnswers/' . $answersMap[$taskQuestions->first()->id]->flowchart_img) }}" 
                                            alt="Flowchart Siswa" 
                                            class="max-w-full max-h-full object-contain rounded-lg shadow-sm">
                                    @else
                                        <div class="text-center text-blue-400">
                                            <i class="bi bi-diagram-2 text-3xl mb-2"></i>
                                            <p>Tidak ada jawaban</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Teacher Side -->
                            <div class="bg-white rounded-lg border-2 border-green-300 p-4">
                                <div class="flex items-center mb-4 pb-2 border-b border-green-200">
                                    <div class="bg-green-100 rounded-lg p-2 mr-3">
                                        <i class="bi bi-person-badge text-green-600"></i>
                                    </div>
                                    <h4 class="font-semibold text-green-800">Kunci Jawaban</h4>
                                </div>
                                <div class="min-h-[350px] bg-green-50 rounded-lg flex items-center justify-center" id="teacher-flowchart">
                                    @if($taskQuestions->count() > 0 && $taskQuestions->first()->flowchart_img)
                                        <img src="{{ asset('storage/assets/flowcharts/keyAnswers/' . $taskQuestions->first()->flowchart_img) }}" 
                                            alt="Kunci Jawaban" 
                                            class="max-w-full max-h-full object-contain rounded-lg shadow-sm">
                                    @else
                                        <div class="text-center text-green-400">
                                            <i class="bi bi-diagram-3 text-3xl mb-2"></i>
                                            <p>Kunci jawaban belum tersedia</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Back Button -->
                    @php
                        $encryptedStudent = Illuminate\Support\Facades\Crypt::encrypt($studentSession->student_id);
                    @endphp
                    <div class="flex justify-center mt-6">
                        <a href="{{ route('detail-progress', $encryptedStudent) }}"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white font-semibold rounded-xl">
                            <i class="bi bi-arrow-left mr-3 text-lg"></i>
                            <span class="text-lg">Kembali</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    
@endsection