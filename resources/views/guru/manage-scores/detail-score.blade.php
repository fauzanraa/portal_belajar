@extends('layout-admins.app')

@section('csrf-token')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
    <div class="w-full rounded-bl-xl p-5 pl-8 bg-white">   
        <h1>Detail Progress</h1>
        <p class="text-[10px] tracking-[5px] text-slate-200">Halaman untuk guru melihat detail pengerjaan siswa</p>
    </div>

    <div class="mt-10 rounded-tl-xl bg-white p-5 pl-8">
        <div class="mt-10">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($dataSiswa as $data)
                    <div onclick="openModal({{ $data->meeting_id }}, {{ $data->student_id }}, {{$data->task_session_id}})">
                        <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transform hover:-translate-y-3 transition-all duration-300 overflow-hidden relative group cursor-pointer border-l-4 border-green-500 h-64">
                            <div class="p-8 h-full flex flex-col justify-center">
                                <div class="flex items-center mb-6">
                                    @php
                                        if ($jumlahSelesai == $jumlahTugas && $jumlahSelesai > 0) {
                                            $textClass = 'bg-green-500';
                                            $iconText = '<i class="bi bi-check-lg text-white text-2xl font-bold"></i>';
                                            $textHover = 'group-hover:text-green-600';
                                        } elseif ($jumlahSelesai == 0) {
                                            $textClass = 'bg-orange-500';
                                            $iconText = '<i class="bi bi-book-fill text-white text-2xl font-bold"></i>';
                                            $textHover = 'group-hover:text-orange-600';
                                        } else {
                                            $textClass = 'bg-sky-500';
                                            $iconText = '<i class="bi bi-book-fill text-white text-2xl font-bold"></i>';
                                            $textHover = 'group-hover:text-sky-600';
                                        }
                                    @endphp
    
                                    <div class="{{$textClass}} rounded-full p-3 mr-4 group-hover:scale-110 transition-transform duration-300">
                                        {!! $iconText !!}
                                    </div>
                                    <h3 class="text-l font-bold text-gray-800 {{ $textHover }} transition-colors duration-200">
                                        {{$data->title}} : {{$data->description}}
                                    </h3>
                                </div>
                                
                                <div class="mb-4">
                                    @php
                                        if ($jumlahSelesai == $jumlahTugas && $jumlahSelesai > 0) {
                                            $badgeClass = 'bg-green-100 text-green-800';
                                            $badgeText = '<i class="bi bi-check-circle-fill mr-2"></i> Selesai';
                                        } elseif ($jumlahSelesai == 0) {
                                            $badgeClass = 'bg-orange-100 text-orange-800';
                                            $badgeText = '<i class="bi bi-clipboard-fill"></i> Belum Dikerjakan';
                                        } else {
                                            $badgeClass = 'bg-sky-100 text-sky-800';
                                            $badgeText = '<i class="bi bi-clock-fill mr-2"></i> Sedang Berlangsung';
                                        }
                                    @endphp
    
                                    <span class="inline-flex items-center px-4 py-2 text-base font-semibold rounded-full {{ $badgeClass }}">
                                        {!! $badgeText !!}
                                    </span>
                                </div>
                                
                                <div>
                                    <p class="text-gray-600 text-xl font-normal">
                                        {{$jumlahSelesai}}/{{$jumlahTugas}} Tugas
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden opacity-0 transition-opacity duration-300">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div id="modalContent" class="bg-white rounded-2xl shadow-2xl max-w-5xl w-full max-h-[90vh] overflow-hidden transform scale-95 transition-transform duration-300">
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4 relative">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <i class="bi bi-graph-up text-2xl text-sky-500"></i>
                            </div>
                            <div>
                                <h3 id="modalTitle" class="text-xl font-bold text-white">Nilai</h3>
                                <p id="modalSubtitle" class="text-blue-100 text-sm">Loading...</p>
                                <p id="modalStudentInfo" class="text-blue-200 text-xs mt-1" style="display: none;"></p>
                            </div>
                        </div>
                        <button onclick="closeModal()" class="text-white hover:text-gray-500 transition-colors p-2 hover:bg-white hover:bg-opacity-10 rounded-full">
                            <i class="bi bi-x-lg text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <div id="modalBody" class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                    <!-- Loading State -->
                    <div id="loadingState" class="flex items-center justify-center py-12">
                        <div class="text-center">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                            <p class="mt-4 text-gray-600">Memuat data...</p>
                        </div>
                    </div>

                    <div id="modalContent" class="space-y-4" style="display: none;">
                    </div>

                    <!-- Error State -->
                    <div id="errorState" class="text-center py-12" style="display: none;">
                        <div class="text-red-600">
                            <i class="bi bi-exclamation-triangle text-4xl"></i>
                            <p id="errorMessage" class="mt-4 text-lg font-medium">Terjadi kesalahan</p>
                            <button onclick="closeModal()" class="mt-4 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        let currentMeetingId = null;
        let currentStudentId = null;
        let currentTaskId = null;

        function openModal(meetingId, studentId, taskId) {
            currentMeetingId = meetingId;
            currentStudentId = studentId;
            currentTaskId = taskId;
            
            // Show modal with loading state
            const modal = document.getElementById('detailModal');
            const modalContent = document.getElementById('modalContent');
            
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modalContent.classList.remove('scale-95');
            }, 10);

            if (studentId && studentId && taskId) {
                loadStudentModalData(meetingId, studentId, taskId);
            } else {
                loadModalData(meetingId);
            }
        }

        function closeModal() {
            const modal = document.getElementById('detailModal');
            const modalContent = document.getElementById('modalContent');
            
            modal.classList.add('opacity-0');
            modalContent.classList.add('scale-95');
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        function loadModalData(meetingId) {
            // Show loading state
            const modalBody = document.querySelector('#detailModal .p-6');
            modalBody.innerHTML = `
                <div class="flex items-center justify-center py-8">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                    <span class="ml-3 text-gray-600">Memuat data...</span>
                </div>
            `;

            // AJAX request to get meeting data
            fetch(`/guru/manage-scores/detail-modal/${meetingId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateModalContent(data.data);
                } else {
                    showError('Gagal memuat data');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Terjadi kesalahan saat memuat data');
            });
        }

        function loadStudentModalData(meetingId, studentId, taskId) {
            // Show loading state
            const modalBody = document.querySelector('#detailModal .p-6');
            modalBody.innerHTML = `
                <div class="flex items-center justify-center py-8">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                    <span class="ml-3 text-gray-600">Memuat data siswa...</span>
                </div>
            `;

            fetch(`/teacher/manage-scores/detail-modal/${studentId}/${taskId}/${meetingId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateStudentModalContent(data.data);
                } else {
                    showError('Gagal memuat data siswa');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Terjadi kesalahan saat memuat data siswa');
            });
        }

        function updateStudentModalContent(data) {
            document.querySelector('#detailModal h3').textContent = 'Nilai';
            document.querySelector('#detailModal p.text-blue-100').textContent = `${data.title}: ${data.description}`;

            const modalBody = document.querySelector('#detailModal .p-6');
            let tasksHtml = '';

            if (data.tasks && data.tasks.length > 0) {
                data.tasks.forEach(task => {
                    const type = task.type === 'pretest' ? 'Pre-Test' : 'Post-Test';
                    const statusClass = task.status === 'finished' ? 'bg-green-100' : 'bg-yellow-100';
                    const statusIcon = task.status === 'finished' ? 'bi-check-circle-fill text-green-600' : 'bi-clock-fill text-yellow-600';
                    const scoreColor = task.status === 'finished' ? 'text-green-600' : 'text-yellow-600';

                    tasksHtml += `
                        <div class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 ${statusClass} rounded-full flex items-center justify-center">
                                        <i class="bi ${statusIcon}"></i>
                                    </div>
                                    <div>
                                        <h5 class="font-semibold text-gray-800">${type}</h5>
                                        <div class="flex items-center mt-1 space-x-3">
                                            <span class="text-xs text-gray-500">
                                                <i class="bi bi-calendar mr-1"></i>
                                                ${task.date}
                                            </span>
                                            <span class="text-xs text-gray-500">
                                                <i class="bi bi-clock mr-1"></i>
                                                ${task.duration}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold ${scoreColor}">${task.score || 0}</div>
                                </div>
                            </div>
                            
                            ${task.accuracy > 0 ? `
                            <div class="mt-5">
                                <div class="flex justify-between text-xs text-gray-600 mb-1">
                                    <span>Rasio Kesalahan:</span>
                                    <span>${task.accuracy}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-${task.accuracy >= 70 ? 'green' : task.accuracy >= 50 ? 'yellow' : 'red'}-500 h-2 rounded-full" style="width: ${task.accuracy}%"></div>
                                </div>
                            </div>
                            ` : ''}

                            ${task.efficiency ? `
                            <div class="mt-5">
                                <div class="flex justify-between text-xs text-gray-600 mb-1">
                                    <span>Efisiensi Pengerjaan:</span>
                                    <span>${task.efficiency}</span>
                                </div>
                            </div>
                            ` : ''}
                            
                            <div class="mt-3 pt-3 border-t border-gray-100">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500">Status:</span>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full ${getStatusBadgeClass(task.status)}">
                                        ${getStatusText(task.status)}
                                    </span>
                                </div>
                            </div>
                        </div>
                    `;
                });
            } else {
                tasksHtml = `
                    <div class="text-center py-8">
                        <i class="bi bi-inbox text-4xl text-gray-400"></i>
                        <p class="mt-4 text-gray-500">Tidak ada tugas ditemukan untuk siswa ini</p>
                    </div>
                `;
            }

            modalBody.innerHTML = `<div class="space-y-4">${tasksHtml}</div>`;
        }

        function showError(message) {
            const modalBody = document.querySelector('#detailModal .p-6');
            modalBody.innerHTML = `
                <div class="flex items-center justify-center py-8">
                    <div class="text-red-600 text-center">
                        <i class="bi bi-exclamation-triangle text-2xl"></i>
                        <p class="mt-2">${message}</p>
                    </div>
                </div>
            `;
        }

        // Helper functions
        function formatDateTime(dateTimeString) {
            if (!dateTimeString) return '-';
            
            try {
                const date = new Date(dateTimeString);
                return date.toLocaleString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } catch (error) {
                return dateTimeString;
            }
        }

        function getStatusBadgeClass(status) {
            switch (status) {
                case 'finished':
                    return 'bg-green-100 text-green-800';
                case 'in_progress':
                    return 'bg-blue-100 text-blue-800';
                default:
                    return 'bg-gray-100 text-gray-800';
            }
        }

        function getStatusText(status) {
            switch (status) {
                case 'finished':
                    return 'Selesai';
                case 'in_progress':
                    return 'Berlangsung';
                default:
                    return 'Tidak Diketahui';
            }
        }

        // Close modal when clicking outside
        document.getElementById('detailModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>
@endsection