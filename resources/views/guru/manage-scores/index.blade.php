@extends('layout-admins.app')

@section('content')
    <div class="w-full rounded-bl-xl p-5 pl-8 bg-white">   
        <h1>Manajemen Progress</h1>
        <p class="text-[10px] tracking-[5px] text-slate-200">Halaman untuk guru melihat progress pengerjaan siswa</p>
    </div>

    <div class="mt-10 rounded-tl-xl bg-white p-5 pl-8">
        <div class="mt-1">
            <div class="button my-5">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Pilihan Kelas</h3>
                <button id="dropdownRadioButton" data-dropdown-toggle="dropdownRadio" class="inline-flex items-center text-gray-500 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-3 py-1.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700" type="button">
                    <i class="bi bi-person-circle mr-3"></i>
                    <span id="selectedClass">Semua Kelas</span>
                    <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                    </svg>
                </button>
                <div id="dropdownRadio" class="z-10 hidden w-48 bg-white divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-700 dark:divide-gray-600">
                    <ul class="p-3 space-y-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownRadioButton">
                        <li>
                            <div class="flex items-center p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                <input id="filter-radio-all" type="radio" value="all" name="filter-radio" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600" checked>
                                <label for="filter-radio-all" class="w-full ms-2 text-sm font-medium text-gray-900 rounded">Semua Kelas</label>
                            </div>
                        </li>
                        @foreach ($pilihan_kelas as $className)
                            <li>
                                <div class="flex items-center p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                    <input id="filter-radio-{{ $loop->index }}" type="radio" value="{{ $className->class_name }}" name="filter-radio" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                    <label for="filter-radio-{{ $loop->index }}" class="w-full ms-2 text-sm font-medium text-gray-900 rounded">Kelas {{ $className->class_name }}</label>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="w-full bg-white mt-10 rounded-xl shadow-lg overflow-hidden">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="bg-white bg-opacity-20 rounded-xl p-3 mr-4 backdrop-blur-sm">
                            <i class="bi bi-people text-blue-500 text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white">Pengerjaan Siswa</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Students List -->
            <div class="divide-y divide-gray-200" id="student-list">
                @foreach ($studentSession as $data)
                    <div class="group hover:bg-blue-50 transition-all duration-300 cursor-pointer student-card" data-class="{{ $data->class_name }}" data-student-id="{{ $data->student_id }}">
                        <div class="px-6 py-5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <!-- Student Avatar -->
                                    <div class="flex-shrink-0">
                                        @php
                                            $initials = collect(explode(' ', $data->name))
                                                ->map(fn($word) => strtoupper(substr($word, 0, 1)))
                                                ->implode('');
                                        @endphp
                                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-semibold text-lg group-hover:scale-110 transition-transform
                                            @if ($persentase_pengerjaan[$data->student_id] >= 0 && $persentase_pengerjaan[$data->student_id] <= 35) bg-gradient-to-br from-red-400 to-red-600
                                            @elseif ($persentase_pengerjaan[$data->student_id] >= 36 && $persentase_pengerjaan[$data->student_id] <= 70) bg-gradient-to-br from-sky-400 to-sky-600
                                            @else bg-gradient-to-br from-green-400 to-green-600
                                            @endif">
                                            {{$initials}}
                                        </div>
                                    </div>
                                    
                                    <!-- Student Info -->
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
                                            {{$data->name}}
                                        </h3>
                                        <p class="text-sm text-gray-600 mt-1">
                                            Kelas {{$data->class_name}}
                                        </p>
                                        <div class="flex items-center mt-2 space-x-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if ($persentase_pengerjaan[$data->student_id] >= 0 && $persentase_pengerjaan[$data->student_id] <= 35) bg-red-100 text-red-800
                                                @elseif ($persentase_pengerjaan[$data->student_id] >= 36 && $persentase_pengerjaan[$data->student_id] <= 70) bg-yellow-100 text-yellow-800
                                                @else bg-green-100 text-green-800
                                                @endif">
                                                @if ($persentase_pengerjaan[$data->student_id] >= 0 && $persentase_pengerjaan[$data->student_id] <= 70)
                                                    <i class="bi bi-clock-fill mr-1"></i>
                                                    Dalam Progress
                                                @else
                                                    <i class="bi bi-check-circle-fill mr-1"></i>
                                                    Selesai
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Progress Section -->
                                <div class="flex items-center space-x-6">
                                    <!-- Progress Circle -->
                                    <div class="relative">
                                        <div class="w-16 h-16">
                                            <svg class="w-16 h-16 transform -rotate-90" viewBox="0 0 36 36">
                                                <path class="text-gray-200" stroke="currentColor" stroke-width="3" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"></path>
                                                <path class="@if ($persentase_pengerjaan[$data->student_id] >= 0 && $persentase_pengerjaan[$data->student_id] <= 35) text-red-500
                                                    @elseif ($persentase_pengerjaan[$data->student_id] >= 36 && $persentase_pengerjaan[$data->student_id] <= 70) text-yellow-500
                                                    @else text-green-500
                                                    @endif" stroke="currentColor" stroke-width="3" fill="none" stroke-dasharray="{{$persentase_pengerjaan[$data->student_id]}}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"></path>
                                            </svg>
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <span class="text-lg font-bold @if ($persentase_pengerjaan[$data->student_id] >= 0 && $persentase_pengerjaan[$data->student_id] <= 35) text-red-500
                                                    @elseif ($persentase_pengerjaan[$data->student_id] >= 36 && $persentase_pengerjaan[$data->student_id] <= 70) text-yellow-500
                                                    @else text-green-500
                                                    @endif">{{$persentase_pengerjaan[$data->student_id]}}%</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {{-- @php
                                        $encryptedTask = Illuminate\Support\Facades\Crypt::encrypt($data->task_session_id);
                                        $encryptedStudent = Illuminate\Support\Facades\Crypt::encrypt($data->student_id);
                                    @endphp
                                    <!-- Details Button -->
                                    <button onclick="window.location.href='{{ route('detail-scores', ['idTask' => $encryptedTask, 'idStudent' => $encryptedStudent]) }}'" class="detail-btn px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium opacity-0 group-hover:opacity-100 transform translate-x-2 group-hover:translate-x-0 transition-all duration-300">
                                        <i class="bi bi-eye mr-1"></i>
                                        Detail
                                    </button> --}}
                                </div>
                            </div>
                            
                            <!-- Progress Details (Expandable) -->
                            <div class="mt-4 hidden group-hover:block transition-all duration-300">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="text-center">
                                            <div class="text-2xl font-bold text-gray-600">0/0</div>
                                            <div class="text-xs text-gray-600">Modul</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-2xl font-bold text-gray-600">{{$data->class_name}}</div>
                                            <div class="text-xs text-gray-600">Kelas</div>
                                        </div>
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
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownButton = document.getElementById('dropdownRadioButton');
            const dropdown = document.getElementById('dropdownRadio');
            
            dropdownButton.addEventListener('click', function() {
                dropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', function(event) {
                if (!dropdownButton.contains(event.target) && !dropdown.contains(event.target)) {
                    dropdown.classList.add('hidden');
                }
            });

            const radioButtons = document.querySelectorAll('input[name="filter-radio"]');
            const selectedClassSpan = document.getElementById('selectedClass');
            const studentCards = document.querySelectorAll('.student-card');

            radioButtons.forEach(radio => {
                radio.addEventListener('change', function() {
                    const selectedValue = this.value;
                    const selectedLabel = this.nextElementSibling.textContent;
                    
                    selectedClassSpan.textContent = selectedLabel;
                    
                    filterStudents(selectedValue);
                    
                    dropdown.classList.add('hidden');
                });
            });

            function filterStudents(classFilter) {
                studentCards.forEach(card => {
                    if (classFilter === 'all') {
                        card.style.display = 'flex';
                        setTimeout(() => {
                            card.style.opacity = '1';
                            card.style.transform = 'translateY(0)';
                        }, 10);
                    } else {
                        const cardClass = card.getAttribute('data-class');
                        if (cardClass === classFilter) {
                            card.style.display = 'flex';
                            setTimeout(() => {
                                card.style.opacity = '1';
                                card.style.transform = 'translateY(0)';
                            }, 10);
                        } else {
                            card.style.opacity = '0';
                            card.style.transform = 'translateY(-10px)';
                            setTimeout(() => {
                                card.style.display = 'none';
                            }, 300);
                        }
                    }
                });
            }

            studentCards.forEach(card => {
                card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            });
        });
    </script>
@endsection