@php
    $user = Auth::user();
    $roles = $user->roles->pluck('name')->toArray();
    // dd($roles);
@endphp

<div class="sidebar fixed top-0 bottom-0 lg:left-0 p-2 w-[300px] overflow-auto text-center bg-white flex flex-col"> 
    <div class="text-black text-xl flex flex-col h-full">
        <div class="px-12 mt-3 flex items-center">
            <i class="bi bi-diagram-2 px-1 py-2 text-6xl text-sky-500"></i>
            <p class="text-xl">Flow<span class="text-sky-500">Matic</span></p>
        </div>
        <div class="mt-2 py-2 text-slate-200"><hr></div>
        
        @if (in_array('admin', $roles))
            <a href="{{route('admin-index')}}">
                <div class="sidebar-menu group">
                    <i class="bi bi-house text-black group-hover:text-white"></i>
                    <span class="text-[18px] ml-5 text-black group-hover:text-white">Beranda</span>
                </div>
            </a>
        @elseif (in_array('guru', $roles))
            <a href="{{route('teacher-index')}}">
                <div class="sidebar-menu group">
                    <i class="bi bi-house text-black group-hover:text-white"></i>
                    <span class="text-[18px] ml-5 text-black group-hover:text-white">Beranda</span>
                </div>
            </a>
        @elseif (in_array('siswa', $roles))
            <a href="{{route('student-index')}}">
                <div class="sidebar-menu group">
                    <i class="bi bi-house text-black group-hover:text-white"></i>
                    <span class="text-[18px] ml-5 text-black group-hover:text-white">Beranda</span>
                </div>
            </a>
        @endif
        <!-- Admin -->
        @if (in_array('admin', $roles))
        <a href="{{route('manage-class')}}">
            <div class="sidebar-menu group">
                <i class="bi bi-building text-black group-hover:text-white"></i>
                <span class="text-[18px] ml-5 text-black group-hover:text-white">Manajemen Kelas</span>
            </div>
        </a>
        <a href="{{route('manage-teachers')}}">
            <div class="sidebar-menu group">
                <i class="bi bi-person-video3 text-black group-hover:text-white"></i>
                <span class="text-[18px] ml-5 text-black group-hover:text-white">Manajemen Guru</span>
            </div>
        </a>
        <a href="{{route('manage-students')}}">
            <div class="sidebar-menu group">
                <i class="bi bi-people text-black group-hover:text-white"></i>
                <span class="text-[18px] ml-5 text-black group-hover:text-white">Manajemen Siswa</span>
            </div>
        </a>
        <a href="{{route('manage-users')}}">
            <div class="sidebar-menu group">
                <i class="bi bi-person text-black group-hover:text-white"></i>
                <span class="text-[18px] ml-5 text-black group-hover:text-white">Manajemen User</span>
            </div>
        </a>
        @elseif (in_array('guru', $roles))
        <!-- Guru -->
        <a href="{{route('manage-meetings')}}">
            <div class="sidebar-menu group">
                <i class="bi bi-list-task text-black group-hover:text-white"></i>
                <span class="text-[18px] ml-5 text-black group-hover:text-white">Manajemen Pertemuan</span>
            </div>
        </a>
        <a href="{{route('manage-scores')}}">
            <div class="sidebar-menu group">
                <i class="bi bi-clipboard-data text-black group-hover:text-white"></i>
                <span class="text-[18px] ml-5 text-black group-hover:text-white">Manajemen Progress</span>
            </div>
        </a>
        <div class="sidebar-menu group">
            <i class="bi bi-people text-black group-hover:text-white"></i>
            <span class="text-[18px] ml-5 text-black group-hover:text-white">Manajemen Hasil</span>
        </div>
        @elseif (in_array('siswa', $roles))
        <!-- Siswa -->
        <a href="{{route('list-teachers')}}">
            <div class="sidebar-menu group">
                <i class="bi bi-list-task text-black group-hover:text-white"></i>
                <span class="text-[18px] ml-5 text-black group-hover:text-white">Modul</span>
            </div>
        </a>
        <a href="{{route('list-scores')}}">
            <div class="sidebar-menu group">
                <i class="bi bi-clipboard-data text-black group-hover:text-white"></i>
                <span class="text-[18px] ml-5 text-black group-hover:text-white">Nilai</span>
            </div>
        </a>
        @endif

        <div class="mt-auto">
            <div class="mt-2 py-2 text-slate-200"><hr></div>
            <form method="POST" id="logout-form" action="{{ route('logout') }}">
                @csrf
                <div class="sidebar-menu flex group mt-auto" onclick="confirmLogout()">
                    <i class="bi bi-box-arrow-left text-black  group-hover:text-white"></i>
                    <span class="text-[18px] ml-5 text-black group-hover:text-white">Keluar</span>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function confirmLogout() {
    Swal.fire({
        title: 'Konfirmasi',
        text: 'Apakah Anda yakin ingin keluar dari sistem?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Keluar',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Logging out...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            document.getElementById('logout-form').submit();
        }
    });
}
</script>