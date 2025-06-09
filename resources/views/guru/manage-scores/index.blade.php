@extends('layout-admins.app')

@section('content')
    <div class="w-full rounded-bl-xl p-5 pl-8 bg-white">   
        <h1>Manajemen Progress</h1>
        <p class="text-[10px] tracking-[5px] text-slate-200">Halaman untuk guru progress pengerjaan siswa</p>
    </div>

    <div class="mt-10 rounded-tl-xl bg-white p-5 pl-8">
        <div class="mt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Pengerjaan Siswa</h3>
            
            <!-- Student Progress Card -->
            <div class="space-y-4">
                <!-- Single Student Card -->
                <div class="flex items-center p-4 bg-gray-50 rounded-lg border border-gray-200 hover:shadow-md transition-shadow duration-200">
                    <!-- Avatar -->
                    <div class="flex-shrink-0 mr-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-lg">
                            JS
                        </div>
                    </div>
                    
                    <!-- Student Info and Progress -->
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <h4 class="text-sm font-semibold text-gray-800">John Smith</h4>
                                <p class="text-xs text-gray-500">Kelas XII RPL A</p>
                            </div>
                            <span class="text-sm font-medium text-blue-600">75%</span>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full transition-all duration-500" style="width: 75%"></div>
                        </div>
                    </div>
                </div>

                <!-- Another Student Card -->
                <div class="flex items-center p-4 bg-gray-50 rounded-lg border border-gray-200 hover:shadow-md transition-shadow duration-200">
                    <div class="flex-shrink-0 mr-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center text-white font-semibold text-lg">
                            AD
                        </div>
                    </div>
                    
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <h4 class="text-sm font-semibold text-gray-800">Alice Doe</h4>
                                <p class="text-xs text-gray-500">Kelas XII RPL B</p>
                            </div>
                            <span class="text-sm font-medium text-green-600">90%</span>
                        </div>
                        
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full transition-all duration-500" style="width: 90%"></div>
                        </div>
                    </div>
                </div>

                <!-- Student with Low Progress -->
                <div class="flex items-center p-4 bg-gray-50 rounded-lg border border-gray-200 hover:shadow-md transition-shadow duration-200">
                    <div class="flex-shrink-0 mr-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-red-400 to-red-600 rounded-full flex items-center justify-center text-white font-semibold text-lg">
                            MJ
                        </div>
                    </div>
                    
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <h4 class="text-sm font-semibold text-gray-800">Mike Johnson</h4>
                                <p class="text-xs text-gray-500">Kelas XII RPL A</p>
                            </div>
                            <span class="text-sm font-medium text-red-600">25%</span>
                        </div>
                        
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-red-500 to-red-600 h-2 rounded-full transition-all duration-500" style="width: 25%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>   
        if (document.getElementById("search-table") && typeof simpleDatatables.DataTable !== 'undefined') {
            const dataTable = new simpleDatatables.DataTable("#search-table", {
                searchable: true,
                sortable: false
            });
        }
    </script>
@endsection