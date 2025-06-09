@extends('layout-admins.app')

@push('style')
    <script src="{{ asset('js/flowchart.js') }}"></script>
@endpush

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
        <h1>Detail Tugas</h1>
        <p class="text-[10px] tracking-[5px] text-slate-200">Halaman untuk mengatur sesi tugas</p>
    </div>

    <div class="mt-10 bg-white p-5 pl-8 rounded-l-xl">
        <div>
            <p class="font-bold mt-3">Pengaturan tugas :</p>
        </div>
        <div class="mt-5">
            <div class="relative overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <tbody>
                        <tr class="bg-white">
                            <th scope="row" class="px-6 py-4 w-1/4 font-medium border border-gray-400 bg-slate-300 text-black">
                                Judul
                            </th>
                            <td class="px-6 py-4 border-y border-gray-200 border-r border-gray-200">
                                {{$data_tugas->name}}
                            </td>
                        </tr>
                        <tr class="bg-white">
                            <th scope="row" class="px-6 py-4 w-1/4 font-medium border border-gray-400 bg-slate-300 text-black">
                                Tipe
                            </th>
                            @if ($data_tugas->type == 'pretest')
                                <td class="px-6 py-4 border-y border-gray-200 border-r border-gray-200">
                                    Pre-test
                                </td>
                            @else
                                <td class="px-6 py-4 border-y border-gray-200 border-r border-gray-200">
                                    Post-test
                                </td>
                            @endif
                        </tr>
                        <tr class="bg-white">
                            <th scope="row" class="px-6 py-4 font-medium border border-gray-400 bg-slate-300 text-black">
                                Jadwal
                            </th>
                            <td class="px-6 py-4 border-y border-gray-200 border-r border-gray-200">
                                {{$data_tugas->open_at ? \Carbon\Carbon::parse($data_tugas->open_at)->locale('id')->isoFormat('D MMMM YYYY') : '-'}} ({{$data_tugas->open_at ? \Carbon\Carbon::parse($data_tugas->open_at)->locale('id')->isoFormat('HH:mm') : '-'}}) - {{$data_tugas->close_at ? \Carbon\Carbon::parse($data_tugas->close_at)->locale('id')->isoFormat('D MMMM YYYY') : '-'}} ({{$data_tugas->close_at ? \Carbon\Carbon::parse($data_tugas->close_at)->locale('id')->isoFormat('HH:mm') : '-'}})
                                {{-- {{$data_tugas->open_at}} - {{$data_tugas->close_at}} --}}
                            </td>
                        </tr>
                        <tr class="bg-white">
                            <th scope="row" class="px-6 py-4 w-1/4 font-medium border border-gray-400 bg-slate-300 text-black">
                                Durasi
                            </th>
                            <td class="px-6 py-4 border-y border-gray-200 border-r border-gray-200">
                                {{$data_tugas->duration}} menit
                            </td>
                        </tr>
                        <tr class="bg-white">
                            <th scope="row" colspan="2" class="px-6 py-4 w-1/4 font-medium border border-gray-400 bg-slate-300 text-black text-center">
                                Pengaturan soal
                            </th>
                        </tr>
                        <tr class="bg-white">
                            <th scope="row" colspan="2" class="px-6 py-4 w-1/4 font-medium border border-gray-200 text-black text-center">
                                @php
                                    $encryptedTask = Illuminate\Support\Facades\Crypt::encrypt($data_tugas->id);
                                @endphp
                                <div class="button flex justify-self-end">
                                    <button class="px-5 py-2.5 text-sm rounded-lg bg-sky-500 text-sm hover:bg-sky-700 text-white cursor-pointer" onclick="window.location.href='{{route('question-tasks', $encryptedTask)}}'">
                                        <i class="bi bi-plus-lg mr-1"></i> Tambah Soal
                                    </button>
                                </div>
                                <div class="mt-3">
                                    <table id="question-table">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <span class="flex items-center">
                                                        No
                                                    </span>
                                                </th>
                                                <th>
                                                    <span class="flex items-center">
                                                        Soal
                                                    </span>
                                                </th>
                                                <th>
                                                    <span class="flex items-center">
                                                        Kunci Jawaban
                                                    </span>
                                                </th>
                                                <th>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($data_soal as $data)
                                                <tr>
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{{$data->question}}</td>
                                                    <td>
                                                        @if (!empty($data->correct_answer))
                                                            <span class="cursor-pointer text-blue-600 hover:text-blue-800 view-answer-btn" 
                                                                data-task-id="{{ $data->id }}" 
                                                                data-answer="{{ base64_encode($data->correct_answer) }}">
                                                                Lihat kunci jawaban
                                                            </span>
                                                        @else 
                                                            <span>Tidak ada kunci jawaban</span>    
                                                        @endif
                                                    <td>
                                                    @php
                                                        $encryptedQuestion = Illuminate\Support\Facades\Crypt::encrypt($data->id);
                                                    @endphp
                                                        @if (!empty($data->correct_answer))
                                                            <a href="{{ route('draw-correct-answer', ['id' => $encryptedQuestion]) }}" onclick="event.preventDefault(); showConfirmation('{{ route('draw-correct-answer', ['id' => $encryptedQuestion]) }}')" class="p-2 text-sm rounded-lg bg-sky-500 text-sm hover:bg-sky-700 text-white cursor-pointer">
                                                                <i class="bi bi-pencil"></i>
                                                            </a>
                                                        @else 
                                                            <a href="{{ route('draw-correct-answer', ['id' => $encryptedQuestion]) }}" class="p-2 text-sm rounded-lg bg-sky-500 text-sm hover:bg-sky-700 text-white cursor-pointer">
                                                                <i class="bi bi-plus-lg"></i>
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </th>
                        </tr>
                        <tr class="bg-white">
                            <th scope="row" class="px-6 py-4 font-medium border border-gray-400 bg-slate-300 text-black">
                                Akses Tugas
                            </th>
                            <td class="px-6 py-4 border-y border-gray-200 border-r border-gray-200">
                                <div class="block">
                                    @if($sesi_tugas_siswa->isEmpty())
                                        <p class="text-gray-500 italic">Belum ada sesi</p>
                                    @else
                                        @foreach($sesi_tugas_siswa as $className => $students)
                                            <div class="mb-2">
                                                <button data-modal-target="add-material-modal" data-modal-toggle="add-material-modal" class="hover:text-sky-500 cursor-pointer">
                                                    Kelas {{ $className }} - {{ $students->count() }} Siswa
                                                </button>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                <div class="block mt-5 mb-2 flex gap-4">
                                    @php
                                        $encryptedTask = Illuminate\Support\Facades\Crypt::encrypt($data_tugas->id);
                                    @endphp
                                    <a href="{{route('session-tasks', ['id' => $encryptedTask])}}" class="text-sky-500 hover:text-sky-700">
                                        <div class="block">
                                            <i class="bi bi-people-fill"></i><span class="ml-3">Atur sesi</span>
                                        </div>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="button mt-8 flex justify-self-end">
                    @php
                        $encryptedMeeting = Illuminate\Support\Facades\Crypt::encrypt($data_tugas->meeting_id);
                    @endphp
                    <button type="button" class="px-5 py-2.5 text-sm rounded-lg bg-sky-500 text-sm hover:bg-sky-700 text-white cursor-pointer" onclick="window.location.href='{{ route('manage-materials', ['id' => $encryptedMeeting]) }}'">
                        <i class="bi bi-caret-left mr-1"></i> Kembali
                    </button>
                </div>
            </div>
        </div>

        <div class="button mt-8 flex justify-self-end">
            {{-- @php
                $encryptedMeeting = Illuminate\Support\Facades\Crypt::encrypt($data_materi->meeting_id);
            @endphp
            <button type="button" class="px-5 py-2.5 text-sm rounded-lg bg-sky-500 text-sm hover:bg-sky-700 text-white cursor-pointer" onclick="window.location.href='{{ route('manage-materials', ['id' => $encryptedMeeting]) }}'">
                <i class="bi bi-caret-left mr-1"></i> Kembali
            </button> --}}
        </div>
    </div>

    <div id="add-material-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Tambah file materi
                    </h3>
                    <button type="button" class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="add-material-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5">
                    @php
                        // $encryptedId = Illuminate\Support\Facades\Crypt::encrypt($data_materi->id);
                    @endphp
                    {{-- <form class="space-y-4" action="{{route('file-materials', ['id' => $encryptedId])}}" method="POST" enctype="multipart/form-data"> --}}
                        @csrf
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900" for="file_input">Berkas Materi</label>
                            <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" aria-describedby="file_input_help" id="file_input" name="file_material" type="file">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">Pdf (max. 10mb)</p>
                        </div>
                        <button type="submit" class="w-full text-white mt-5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="flowchart-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-4xl max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow-lg dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Kunci Jawaban Flowchart
                    </h3>
                    <button type="button" class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" onclick="closeFlowchartModal()">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5">
                    <div id="flowchart-container" class="w-full h-96 border border-gray-300 rounded-lg overflow-auto bg-gray-50 flex items-center justify-center">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@section('script')

    <script> 
        if (document.getElementById("question-table") && typeof simpleDatatables.DataTable !== 'undefined') {
            const dataTable = new simpleDatatables.DataTable("#question-table", {
                searchable: true,
                sortable: false
            });
        }

        function showConfirmation(url) {
            Swal.fire({
                title: 'Perhatian!!!',
                text: 'Anda sudah memiliki kunci jawaban yang tersimpan, apakah anda yakin ingin mengubah kunci jawaban?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Iya',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        }

        // function showFlowchartModal(taskId, correctAnswerJson) {
        //     console.log('Task ID:', taskId);
        //     console.log('Raw JSON:', correctAnswerJson);
        //     console.log('Type of JSON:', typeof correctAnswerJson);
        //     const modal = document.getElementById('flowchart-modal');
        //     const container = document.getElementById('flowchart-container');
            
        //     try {
        //         // Parse JSON data
        //         const flowchartData = JSON.parse(correctAnswerJson);
                
        //         // Clear container dan buat element untuk flowchart
        //         container.innerHTML = '<div id="flowchart-display" style="width: 100%; height: 100%;"></div>';
                
        //         // Initialize flowchart dengan library yang sudah ada
        //         const flowchartElement = document.getElementById('flowchart-display');
                
        //         // Render flowchart menggunakan library yang sama dengan yang digunakan untuk create
        //         renderFlowchartFromJson(flowchartElement, flowchartData);
                
        //         // Show modal
        //         modal.classList.remove('hidden');
        //         modal.classList.add('flex');
                
        //     } catch (error) {
        //         console.error('Error rendering flowchart:', error);
        //         container.innerHTML = '<p class="text-red-500 text-center">Error loading flowchart</p>';
        //         modal.classList.remove('hidden');
        //         modal.classList.add('flex');
        //     }
        // }

        // function renderFlowchartFromJson(element, jsonData) {
        //     // Gunakan library flowchart yang sama dengan yang digunakan untuk membuat
        //     // Contoh jika menggunakan Flowchart.js atau library serupa
            
        //     if (window.flowchart) {
        //         // Jika menggunakan flowchart.js
        //         const chart = window.flowchart.parse(jsonData.flowchartCode || convertJsonToFlowchartCode(jsonData));
        //         chart.drawSVG(element);
        //     } else if (window.drawflow) {
        //         // Jika menggunakan Drawflow
        //         const editor = new Drawflow(element);
        //         editor.start();
        //         editor.import(jsonData);
        //         editor.zoom_out();
        //         editor.zoom_out();
        //     } else {
        //         // Fallback: render sebagai SVG langsung dari JSON
        //         renderAsCustomSVG(element, jsonData);
        //     }
        // }

        // function convertJsonToFlowchartCode(jsonData) {
        //     // Convert JSON structure ke format yang dibutuhkan library
        //     // Sesuaikan dengan struktur JSON yang tersimpan di database
        //     let flowchartCode = '';
            
        //     if (jsonData.nodes && jsonData.connections) {
        //         jsonData.nodes.forEach(node => {
        //             flowchartCode += `${node.id}=>${node.type}: ${node.text}\n`;
        //         });
                
        //         jsonData.connections.forEach(conn => {
        //             flowchartCode += `${conn.from}->${conn.to}\n`;
        //         });
        //     }
            
        //     return flowchartCode;
        // }

        // function renderAsCustomSVG(element, jsonData) {
        //     // Fallback rendering jika library tidak tersedia
        //     const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        //     svg.setAttribute('width', '100%');
        //     svg.setAttribute('height', '100%');
        //     svg.setAttribute('viewBox', '0 0 800 600');
            
        //     // Render nodes
        //     if (jsonData.nodes) {
        //         jsonData.nodes.forEach((node, index) => {
        //             const g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
                    
        //             // Rectangle
        //             const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
        //             rect.setAttribute('x', node.x || (100 + index * 150));
        //             rect.setAttribute('y', node.y || (50 + Math.floor(index / 3) * 100));
        //             rect.setAttribute('width', '120');
        //             rect.setAttribute('height', '60');
        //             rect.setAttribute('fill', '#e0f2fe');
        //             rect.setAttribute('stroke', '#0369a1');
        //             rect.setAttribute('stroke-width', '2');
        //             rect.setAttribute('rx', '5');
                    
        //             // Text
        //             const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        //             text.setAttribute('x', (node.x || (100 + index * 150)) + 60);
        //             text.setAttribute('y', (node.y || (50 + Math.floor(index / 3) * 100)) + 35);
        //             text.setAttribute('text-anchor', 'middle');
        //             text.setAttribute('font-family', 'Arial, sans-serif');
        //             text.setAttribute('font-size', '12');
        //             text.setAttribute('fill', '#1e40af');
        //             text.textContent = node.text || node.label || `Node ${index + 1}`;
                    
        //             g.appendChild(rect);
        //             g.appendChild(text);
        //             svg.appendChild(g);
        //         });
        //     }
            
        //     element.appendChild(svg);
        // }

        // function closeFlowchartModal() {
        //     const modal = document.getElementById('flowchart-modal');
        //     modal.classList.add('hidden');
        //     modal.classList.remove('flex');
            
        //     // Clean up flowchart instance jika perlu
        //     const container = document.getElementById('flowchart-container');
        //     container.innerHTML = '';
        // }

        document.addEventListener('DOMContentLoaded', function() {
            // Add event listeners to all answer buttons
            const answerButtons = document.querySelectorAll('.view-answer-btn');
            
            answerButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const taskId = this.getAttribute('data-task-id');
                    const answerData = this.getAttribute('data-answer');
                    
                    showFlowchartModal(taskId, answerData);
                });
            });
        });

        function showFlowchartModal(taskId, correctAnswerJson) {
            const modal = document.getElementById('flowchart-modal');
            const container = document.getElementById('flowchart-container');
            
            if (!modal || !container) {
                console.error('Modal or container not found!');
                return;
            }
            
            // Show loading
            container.innerHTML = `
                <div class="flex justify-center items-center h-full">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                        <p class="text-gray-600">Generating flowchart...</p>
                    </div>
                </div>
            `;
            
            // Show modal
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            try {
                // Decode base64 first
                let decodedJson;
                try {
                    decodedJson = atob(correctAnswerJson);
                } catch (e) {
                    // If not base64, treat as regular string
                    decodedJson = correctAnswerJson;
                }
                
                console.log('Decoded JSON string:', decodedJson);
                
                // Parse JSON data
                let flowchartData;
                if (typeof decodedJson === 'string') {
                    // Clean up any HTML entities that might remain
                    const cleanJson = decodedJson
                        .replace(/&quot;/g, '"')
                        .replace(/&#039;/g, "'")
                        .replace(/&lt;/g, '<')
                        .replace(/&gt;/g, '>')
                        .replace(/&amp;/g, '&');
                    
                    console.log('Cleaned JSON:', cleanJson);
                    flowchartData = JSON.parse(cleanJson);
                } else {
                    flowchartData = decodedJson;
                }
                
                console.log('Parsed Flowchart Data:', flowchartData);
                
                // Generate flowchart
                setTimeout(() => {
                    generateFlowchartSVG(container, flowchartData);
                }, 500);
                
            } catch (error) {
                console.error('Error parsing JSON:', error);
                console.error('Raw data:', correctAnswerJson);
                
                container.innerHTML = `
                    <div class="p-4 text-red-500 text-center">
                        <h4 class="font-bold mb-2">Error loading flowchart</h4>
                        <p class="mb-2">${error.message}</p>
                        <details class="text-left">
                            <summary class="cursor-pointer text-blue-600">Show raw data</summary>
                            <pre class="bg-gray-100 p-2 rounded text-xs mt-2 overflow-auto max-h-32">${correctAnswerJson}</pre>
                        </details>
                    </div>
                `;
            }
        }

        function generateFlowchartSVG(container, data) {
            // Clear container
            container.innerHTML = '';
            
            console.log('GoJS flowchart data:', data);
            
            // Create SVG element
            const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            svg.setAttribute('width', '100%');
            svg.setAttribute('height', '100%');
            svg.setAttribute('viewBox', '-200 -300 400 400');
            svg.style.background = '#ffffff';
            
            // Add styles
            const defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
            const style = document.createElementNS('http://www.w3.org/2000/svg', 'style');
            style.textContent = `
                .flowchart-node { font-family: Arial, sans-serif; font-size: 12px; }
                .flowchart-text { font-family: Arial, sans-serif; font-size: 11px; text-anchor: middle; dominant-baseline: middle; }
                .flowchart-line { stroke: #374151; stroke-width: 2; fill: none; }
                .flowchart-arrow { stroke: #374151; stroke-width: 2; fill: #374151; }
            `;
            defs.appendChild(style);
            svg.appendChild(defs);
            
            // Check for GoJS structure
            if (data.nodeDataArray && data.linkDataArray) {
                console.log('Processing GoJS structure');
                console.log('Nodes:', data.nodeDataArray);
                console.log('Links:', data.linkDataArray);
                
                const nodePositions = {};
                
                // First pass: draw all nodes and store positions
                data.nodeDataArray.forEach(node => {
                    const position = drawGoJSNode(svg, node);
                    nodePositions[node.key] = position;
                });
                
                // Second pass: draw connections
                data.linkDataArray.forEach(link => {
                    if (nodePositions[link.from] && nodePositions[link.to]) {
                        drawGoJSConnection(svg, nodePositions[link.from], nodePositions[link.to], link);
                    }
                });
            } else {
                console.log('Not a GoJS structure, showing debug info');
                drawDebugInfo(svg, data);
            }
            
            container.appendChild(svg);
        }

        function drawGoJSNode(svg, node) {
            // Parse location string "x y" to coordinates
            const [x, y] = node.loc.split(' ').map(coord => parseFloat(coord));
            
            console.log(`Drawing GoJS node ${node.key} at (${x}, ${y}): ${node.text}`);
            
            // Determine node dimensions and style based on category
            let width = 120;
            let height = 60;
            let nodeColor = '#e0f2fe';
            let borderColor = '#0369a1';
            let shape = 'rect';
            
            // Style based on category
            if (node.category === 'Terminator') {
                nodeColor = '#dcfce7';
                borderColor = '#16a34a';
                shape = 'ellipse';
                width = 100;
                height = 50;
            } else if (node.category === 'Decision') {
                nodeColor = '#fef3c7';
                borderColor = '#d97706';
                shape = 'diamond';
                width = 120;
                height = 80;
            } else if (node.category === 'Process' || !node.category) {
                nodeColor = '#e0f2fe';
                borderColor = '#0369a1';
                shape = 'rect';
                width = 120;
                height = 60;
            }
            
            const g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
            g.setAttribute('class', 'flowchart-node');
            
            // Draw shape based on category
            if (shape === 'ellipse') {
                const ellipse = document.createElementNS('http://www.w3.org/2000/svg', 'ellipse');
                ellipse.setAttribute('cx', x);
                ellipse.setAttribute('cy', y);
                ellipse.setAttribute('rx', width/2);
                ellipse.setAttribute('ry', height/2);
                ellipse.setAttribute('fill', nodeColor);
                ellipse.setAttribute('stroke', borderColor);
                ellipse.setAttribute('stroke-width', '2');
                g.appendChild(ellipse);
            } else if (shape === 'diamond') {
                const diamond = document.createElementNS('http://www.w3.org/2000/svg', 'polygon');
                const points = `${x},${y - height/2} ${x + width/2},${y} ${x},${y + height/2} ${x - width/2},${y}`;
                diamond.setAttribute('points', points);
                diamond.setAttribute('fill', nodeColor);
                diamond.setAttribute('stroke', borderColor);
                diamond.setAttribute('stroke-width', '2');
                g.appendChild(diamond);
            } else {
                const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                rect.setAttribute('x', x - width/2);
                rect.setAttribute('y', y - height/2);
                rect.setAttribute('width', width);
                rect.setAttribute('height', height);
                rect.setAttribute('rx', '8');
                rect.setAttribute('fill', nodeColor);
                rect.setAttribute('stroke', borderColor);
                rect.setAttribute('stroke-width', '2');
                g.appendChild(rect);
            }
            
            // Add text
            const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
            text.setAttribute('x', x);
            text.setAttribute('y', y);
            text.setAttribute('class', 'flowchart-text');
            text.setAttribute('fill', '#1f2937');
            text.setAttribute('font-weight', '500');
            
            // Handle text wrapping for longer text
            const nodeText = node.text || `Node ${node.key}`;
            if (nodeText.length > 15) {
                const words = nodeText.split(' ');
                let lines = [];
                let currentLine = '';
                
                words.forEach(word => {
                    if ((currentLine + ' ' + word).length <= 15) {
                        currentLine += (currentLine ? ' ' : '') + word;
                    } else {
                        if (currentLine) lines.push(currentLine);
                        currentLine = word;
                    }
                });
                if (currentLine) lines.push(currentLine);
                
                lines.forEach((line, index) => {
                    const tspan = document.createElementNS('http://www.w3.org/2000/svg', 'tspan');
                    tspan.setAttribute('x', x);
                    tspan.setAttribute('dy', index === 0 ? `-${(lines.length - 1) * 0.6}em` : '1.2em');
                    tspan.textContent = line;
                    text.appendChild(tspan);
                });
            } else {
                text.textContent = nodeText;
            }
            
            g.appendChild(text);
            svg.appendChild(g);
            
            return {
                x: x,
                y: y,
                width: width,
                height: height,
                key: node.key
            };
        }

        // function addTextToNode(parentGroup, text, centerX, centerY, maxWidth) {
        //     const textElement = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        //     textElement.setAttribute('x', centerX);
        //     textElement.setAttribute('y', centerY);
        //     textElement.setAttribute('class', 'flowchart-text');
        //     textElement.setAttribute('fill', '#1f2937');
        //     textElement.setAttribute('font-weight', '500');
            
        //     // Clean and truncate text
        //     const cleanText = text.replace(/\s+/g, ' ').trim();
        //     const maxChars = 20;
            
        //     if (cleanText.length <= maxChars) {
        //         textElement.textContent = cleanText;
        //     } else {
        //         // Split into multiple lines
        //         const words = cleanText.split(' ');
        //         let lines = [];
        //         let currentLine = '';
                
        //         words.forEach(word => {
        //             if ((currentLine + ' ' + word).length <= maxChars) {
        //                 currentLine += (currentLine ? ' ' : '') + word;
        //             } else {
        //                 if (currentLine) lines.push(currentLine);
        //                 currentLine = word;
        //             }
        //         });
        //         if (currentLine) lines.push(currentLine);
                
        //         // Limit to 3 lines
        //         lines = lines.slice(0, 3);
        //         if (lines.length > 2 && cleanText.length > maxChars * 2) {
        //             lines[2] = lines[2].substring(0, 15) + '...';
        //         }
                
        //         lines.forEach((line, index) => {
        //             const tspan = document.createElementNS('http://www.w3.org/2000/svg', 'tspan');
        //             tspan.setAttribute('x', centerX);
        //             tspan.setAttribute('dy', index === 0 ? `-${(lines.length - 1) * 0.6}em` : '1.2em');
        //             tspan.textContent = line;
        //             textElement.appendChild(tspan);
        //         });
        //     }
            
        //     parentGroup.appendChild(textElement);
        // }

        function drawGoJSConnection(svg, fromNode, toNode, link) {
            console.log(`Drawing connection from ${link.from} to ${link.to}`);
            
            // Calculate connection points (edge of shapes, not center)
            const fromPoint = getConnectionPoint(fromNode, toNode, true);
            const toPoint = getConnectionPoint(toNode, fromNode, false);
            
            // If link has points array, use it for curved path
            if (link.points && link.points.length > 0) {
                drawCurvedPath(svg, link.points);
            } else {
                // Draw straight line
                const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                line.setAttribute('x1', fromPoint.x);
                line.setAttribute('y1', fromPoint.y);
                line.setAttribute('x2', toPoint.x);
                line.setAttribute('y2', toPoint.y);
                line.setAttribute('class', 'flowchart-line');
                svg.appendChild(line);
                
                // Draw arrow at the end
                drawArrow(svg, fromPoint, toPoint);
            }
        }

        function getConnectionPoint(node, targetNode, isFrom) {
            // Calculate the point on the edge of the shape closest to the target
            const dx = targetNode.x - node.x;
            const dy = targetNode.y - node.y;
            const distance = Math.sqrt(dx * dx + dy * dy);
            
            if (distance === 0) return { x: node.x, y: node.y };
            
            const unitX = dx / distance;
            const unitY = dy / distance;
            
            // Offset by half the node size to get edge point
            const offsetX = unitX * (node.width / 2);
            const offsetY = unitY * (node.height / 2);
            
            return {
                x: node.x + (isFrom ? offsetX : -offsetX),
                y: node.y + (isFrom ? offsetY : -offsetY)
            };
        }

        function drawCurvedPath(svg, points) {
            if (points.length < 4) return;
            
            // GoJS points array contains [x1, y1, x2, y2, ...] coordinates
            let pathData = `M ${points[0]} ${points[1]}`;
            
            for (let i = 2; i < points.length; i += 2) {
                if (i + 1 < points.length) {
                    pathData += ` L ${points[i]} ${points[i + 1]}`;
                }
            }
            
            const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            path.setAttribute('d', pathData);
            path.setAttribute('class', 'flowchart-line');
            svg.appendChild(path);
            
            // Draw arrow at the end
            const lastIndex = points.length - 2;
            const secondLastIndex = points.length - 4;
            if (lastIndex >= 0 && secondLastIndex >= 0) {
                const fromPoint = { x: points[secondLastIndex], y: points[secondLastIndex + 1] };
                const toPoint = { x: points[lastIndex], y: points[lastIndex + 1] };
                drawArrow(svg, fromPoint, toPoint);
            }
        }

        function drawArrow(svg, fromPoint, toPoint) {
            const angle = Math.atan2(toPoint.y - fromPoint.y, toPoint.x - fromPoint.x);
            const arrowLength = 8;
            const arrowAngle = Math.PI / 6;
            
            const arrowX1 = toPoint.x - arrowLength * Math.cos(angle - arrowAngle);
            const arrowY1 = toPoint.y - arrowLength * Math.sin(angle - arrowAngle);
            const arrowX2 = toPoint.x - arrowLength * Math.cos(angle + arrowAngle);
            const arrowY2 = toPoint.y - arrowLength * Math.sin(angle + arrowAngle);
            
            const arrow = document.createElementNS('http://www.w3.org/2000/svg', 'polygon');
            arrow.setAttribute('points', `${toPoint.x},${toPoint.y} ${arrowX1},${arrowY1} ${arrowX2},${arrowY2}`);
            arrow.setAttribute('class', 'flowchart-arrow');
            svg.appendChild(arrow);
        }

        function drawSimpleFlowchart(svg, data) {
            // Fallback untuk struktur JSON yang berbeda
            const centerX = 500;
            const centerY = 400;
            
            const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
            rect.setAttribute('x', centerX - 70);
            rect.setAttribute('y', centerY - 30);
            rect.setAttribute('width', 140);
            rect.setAttribute('height', 60);
            rect.setAttribute('rx', '8');
            rect.setAttribute('fill', '#e0f2fe');
            rect.setAttribute('stroke', '#0369a1');
            rect.setAttribute('stroke-width', '2');
            svg.appendChild(rect);
            
            const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
            text.setAttribute('x', centerX);
            text.setAttribute('y', centerY);
            text.setAttribute('class', 'flowchart-text');
            text.setAttribute('fill', '#1f2937');
            text.textContent = 'Flowchart Data';
            svg.appendChild(text);
        }

        function closeFlowchartModal() {
            const modal = document.getElementById('flowchart-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

    </script>
@endsection