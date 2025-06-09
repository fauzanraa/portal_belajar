@extends('layout-admins.app')

@section('content')
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mt-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="w-full rounded-bl-xl p-5 pl-8 bg-white">   
        <h1>Manajemen User</h1>
        <p class="text-[10px] tracking-[5px] text-slate-200">Halaman untuk mendaftarkan user siswa dan guru</p>
    </div>

    <div class="mt-10 bg-white p-5 pl-8 rounded-l-xl">
        <div class="button mt-5 flex justify-self-end">
            <button class="px-5 py-2.5 text-sm rounded-lg bg-sky-500 text-sm hover:bg-sky-700 text-white cursor-pointer" onclick="document.getElementById('sync-users-form').submit();">
                <i class="bi bi-plus-lg mr-1"></i> Tambah akun
            </button>
        </div>
        <div class="mt-9">
            <table id="user-table">
                <thead>
                    <tr>
                        <th>
                            <span class="flex items-center">
                                No
                            </span>
                        </th>
                        <th>
                            <span class="flex items-center">
                                Nama
                            </span>
                        </th>
                        <th>
                            <span class="flex items-center">
                                Role
                            </span>
                        </th>
                        <th>
                            <span class="flex items-center">
                                Username
                            </span>
                        </th>
                        <th>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data_user as $data)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>
                                @if($data->userable)
                                    @if($data->userable_type == 'App\Models\Admin' )
                                        {{ $data->userable->name ?? 'Admin' }}
                                    @elseif($data->userable_type == 'App\Models\Teacher')
                                        {{ $data->userable->name ?? 'Teacher' }}
                                    @elseif($data->userable_type == 'App\Models\Student')
                                        {{ $data->userable->name ?? 'Student' }}
                                    @else
                                        {{ $data->userable->name ?? 'Unknown' }}
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($data->userable_type == 'App\Models\Admin' )
                                    Admin
                                @elseif($data->userable_type == 'App\Models\Teacher')
                                    Guru
                                @elseif($data->userable_type == 'App\Models\Student')
                                    Murid
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{$data->username ?? ''}}</td>
                            <td>
                                <button data-modal-target="edit-user-modal" data-modal-toggle="edit-user-modal" 
                                        class="p-2 text-sm rounded-lg bg-sky-500 text-sm hover:bg-sky-700 text-white cursor-pointer" 
                                        onclick="openEditModal({{ $data->id }}, '{{$data->username}}')">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <a href="{{ route('delete-users', ['id' => $data->id]) }}" 
                                class="p-2 text-sm rounded-lg bg-red-500 text-sm hover:bg-red-700 text-white cursor-pointer"
                                onclick="event.preventDefault(); showConfirmation('{{ route('delete-users', ['id' => $data->id]) }}')" 
                                title="Hapus data">
                                    <i class="bi bi-trash3"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- <div id="add-user-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Tambah data user
                    </h3>
                    <button type="button" class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="add-user-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5">
                    <form class="space-y-4" action="{{route('sync-users')}}" method="POST">
                        @csrf
                        <div>
                            <label for="school" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Sekolah</label>
                            <select id="school" name="school" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option value="" selected disabled>Pilihan Sekolah</option>
                                @foreach ($data_sekolah as $list)
                                    <option value="{{$list->id}}">{{$list->name_school}}</option>
                                @endforeach
                            </select>
                            <span class="text-[10px] text-red-400">nb : akun akan dibuat per sekolah</span>
                        </div>
                        <button type="submit" class="w-full text-white mt-5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Buat Akun</button>
                    </form>
                </div>
            </div>
        </div>
    </div>  --}}

    <div id="edit-user-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Edit data user
                    </h3>
                    <button type="button" class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="edit-user-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5">
                    <form class="space-y-4" id="edit-user-form" onsubmit="return validateForm()" action="{{route('update-users')}}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="text" id="user_id" name="user_id" hidden>
                        <div>
                            <label for="username" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Username</label>
                            <input type="text" name="username" id="username" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                        </div>
                        <div>
                            <label for="new_password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password Baru</label>
                            <input type="password" name="new_password" id="new_password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" onkeyup="checkPasswordMatch()" />
                        </div>
                        <div>
                            <label for="confirm_password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Konfirmasi Password</label>
                            <input type="password" name="confirm_password" id="confirm_password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" onkeyup="checkPasswordMatch()" />
                            <span class="text-xs text-red-500">nb: jika tidak ingin mengganti password kosongi bagian password</span>
                            <span id="password-match-message" class="text-xs block mt-1"></span>
                            @error('new_password')
                                <span class="text-xs text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                        <button type="submit" id="submit-btn" class="w-full text-white mt-5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Buat Akun</button>
                    </form>
                </div>
            </div>
        </div>
    </div> 

    <form id="sync-users-form" action="{{ route('sync-users') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@section('script')
    <script>   
        if (document.getElementById("user-table") && typeof simpleDatatables.DataTable !== 'undefined') {
            const dataTable = new simpleDatatables.DataTable("#user-table", {
                searchable: true,
                sortable: false
            });
        }

        $(document).ready(function() {
            $("#school").select2({
                placeholder: 'Pilih Sekolah',
                language: 'id',
                allowClear: true,
                width: '100%',
                theme: "classic"
            });
        })

        function openEditModal(id, username) {
            document.getElementById('user_id').value = id;
            document.getElementById('username').value = username;

            document.getElementById('edit-user-modal').classList.remove('hidden');
        }

        document.querySelector('[data-modal-hide="edit-user-modal"]').addEventListener('click', function () {
            document.getElementById('edit-user-modal').classList.add('hidden');
        });

        function checkPasswordMatch() {
            const password = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const messageElement = document.getElementById('password-match-message');
            const confirmInput = document.getElementById('confirm_password');
            const submitBtn = document.getElementById('submit-btn');
            
            if (password === '' && confirmPassword === '') {
                messageElement.textContent = '';
                confirmInput.classList.remove('border-red-500', 'border-green-500');
                submitBtn.disabled = false;
                return;
            }
            
            if (password !== confirmPassword) {
                messageElement.textContent = '❌ Password tidak cocok';
                messageElement.className = 'text-xs text-red-500 block mt-1';
                confirmInput.classList.remove('border-green-500');
                confirmInput.classList.add('border-red-500');
                submitBtn.disabled = true;
            } else if (password !== '') {
                if (password.length < 5) {
                    messageElement.textContent = '⚠️ Password minimal 5 karakter';
                    messageElement.className = 'text-xs text-yellow-500 block mt-1';
                    submitBtn.disabled = true;
                } else {
                    messageElement.textContent = '✅ Password cocok';
                    messageElement.className = 'text-xs text-green-500 block mt-1';
                    confirmInput.classList.remove('border-red-500');
                    confirmInput.classList.add('border-green-500');
                    submitBtn.disabled = false;
                }
            }
        }

        function validateForm() {
            const password = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('new_password_confirmation').value;
            
            if (password !== '' && password !== confirmPassword) {
                alert('Password dan konfirmasi password tidak cocok!');
                return false;
            }
            
            if (password !== '' && password.length < 8) {
                alert('Password minimal 8 karakter!');
                return false;
            }
            
            return true;
        }

        function showConfirmation(url) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Data ini akan dihapus!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Iya',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('delete-form');
                    form.action = url;
                    form.submit();
                }
            });
        }
    </script>
@endsection