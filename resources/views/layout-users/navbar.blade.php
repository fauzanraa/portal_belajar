<nav class="fixed p-2 px-25 w-full flex justify-between items-center bg-white mx-auto">
    <div class="px-12 mt-1 flex items-center">
        <i class="bi bi-terminal px-1 py-1 text-4xl text-black"></i>
        <p class="ml-3 text-xl text-black">Ini <span class="text-sky-500">Logo</span></p>
    </div>
    <div class="text-slate-400">|</div>
    <div class="">
        <ul class="flex items-center gap-16">
            <li>
                <a href="{{route("student-index")}}" class="text-black">Beranda</a>
            </li>
            <li>
                <a href="{{route("list-teachers")}}" class="text-black">Pertemuan</a>
            </li>
            <li>
                <a href="#" class="text-black">Nilai</a>
            </li>
        </ul>
    </div>
    <div class="cursor-pointer">
        <i class="bi bi-person-circle mr-3 text-black text-2xl"></i><span><i class="bi bi-caret-down-fill"></i></span>
    </div>
</nav>