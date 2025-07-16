@extends('layout-admins.app')

@section('csrf-token')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showSuccessMessage(@json(session('success')));
            });
        </script>
    @elseif(session('error')) 
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showErrorMessage(@json(session('error')));
            });
        </script>
    @endif

    <div class="w-full rounded-bl-xl p-5 pl-8 bg-white">   
        <h1>Manajemen Nilai</h1>
        <p class="text-[10px] tracking-[5px] text-slate-200">Halaman untuk guru mengelola nilai siswa</p>
    </div>

    <div class="mt-10 rounded-l-xl bg-white p-5 pl-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-sky-100 p-6 rounded-lg shadow-md">
            <div>
                <label for="student" class="block text-sm font-medium text-gray-700 mb-2">Nama Siswa</label>
                <input type="text" value="{{$dataSiswa->name}}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all duration-200 hover:shadow-sm" readonly>
            </div>

            @php
                if ($sessionSiswa->type == 'pretest')
                    $jenis = 'Pretest';
                else 
                    $jenis = 'Posttest';
            @endphp
            <div>
                <label for="assessment" class="block text-sm font-medium text-gray-700 mb-2">Jenis Tugas</label>
                <input type="text" value="{{$jenis}} - {{$sessionSiswa->title}}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all duration-200 hover:shadow-sm" readonly>
            </div>
        </div>

        @php
            $encryptedSession = Illuminate\Support\Facades\Crypt::encrypt($sessionSiswa->id);
        @endphp

        <form id="assessment" action="{{route('store-assessments', ['idModul' => $idModul, 'idSession' => $encryptedSession])}}" class="space-y-6" method="POST">
            @csrf
            <div class="bg-white p-6 rounded-lg shadow-md border border-sky-100">
                <h3 class="text-lg font-semibold text-sky-600 mb-4 border-b pb-2">📝 Data Nilai</h3>

                <input type="text" value="{{$sessionSiswa->id}}" name="student_session" hidden>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="score" class="block text-sm font-medium text-gray-700 mb-2">Skor (0-100)</label>
                        <input type="text" value="{{$sessionSiswa->score}}" inputmode="numeric" id="score" name="score" min="0" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all duration-200 hover:shadow-sm" placeholder="Masukkan skor" readonly>
                    </div>

                    <div>
                        <label for="time" class="block text-sm font-medium text-gray-700 mb-2">Waktu (menit)</label>
                        <div class="relative">
                            <input type="text" inputmode="numeric" id="time" name="time" min="1" value="{{$sessionSiswa->duration}}" class="w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all duration-200 hover:shadow-sm" placeholder="Waktu pengerjaan" readonly>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="correct" class="block text-sm font-medium text-gray-700 mb-2">Jawaban Benar</label>
                        <div class="relative">
                            <input type="text" inputmode="numeric" id="correct" name="correct_elements" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all duration-200 hover:shadow-sm" placeholder="Jumlah jawaban benar">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-4 pt-6">
                <a href="{{ route('detail-moduls', $idModul) }}"
                    class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-sky-100 focus:outline-none focus:ring-2 focus:ring-sky-500 transition-colors duration-200">
                    <i class="bi bi-arrow-left mr-2"></i> Kembali
                </a>
                <button type="submit"
                    class="px-6 py-2 bg-sky-500 text-white rounded-md hover:bg-sky-600 focus:outline-none focus:ring-2 focus:ring-sky-500 transition-colors duration-200 shadow">
                    <i class="bi bi-floppy-fill mr-2"></i> Simpan
                </button>
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script>
        document.getElementById('assessment').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = this;
            const url = form.action;
            const formData = new FormData(form);

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) throw data;
                return data;
            })
            .then(data => {
                if (data.status === 'success') {
                    showSuccessMessage(data.message);

                    setTimeout(() => {
                        window.location.href = '{{ route('detail-moduls', $idModul) }}';
                    }, 2000);
                }
            })
            .catch(err => {
                console.error('Error:', err);
                if (err.message) {
                    showErrorMessage(err.message);
                } else if (err.errors) {
                    const messages = Object.values(err.errors).flat().join('\n');
                    showErrorMessage(messages);
                } else {
                    showErrorMessage('Terjadi kesalahan, coba lagi!');
                }
            });
        });


        function showSuccessMessage(message) {
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full';
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="bi bi-check-lg mr-2"></i>
                    ${message}
                </div>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }

        function showErrorMessage(message) {
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full';
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="bi bi-x-circle-fill mr-2"></i>
                    ${message}
                </div>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 5000);
        }
    </script>
@endsection