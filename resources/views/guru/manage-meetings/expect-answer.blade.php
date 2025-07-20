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

    @php
        $encryptedTask = Illuminate\Support\Facades\Crypt::encrypt($expectedAnswer->task_session_id);
    @endphp

    <div class="w-full rounded-bl-xl p-5 pl-8 bg-white">   
        <h1>Detail Kunci Jawaban</h1>
        <p class="text-[10px] tracking-[5px] text-slate-200">Halaman untuk mengatur sesi tugas</p>
    </div>

    <div class="mt-10 bg-white p-5 pl-8 rounded-l-xl">
        <div class="mb-6">
            <h3 class="text-lg font-semibold italic text-gray-800 mb-4">Expected Answer</h3>

            <div id="nodeInputsContainer" class="space-y-4 mb-6">
            </div>

            <div class="flex gap-3 mb-4 flex justify-end">
                <button onclick="window.location.href='{{ route('detail-tasks', $encryptedTask) }}'" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition">
                    <i class="bi bi-arrow-left mr-2"></i>Kembali
                </button>
                <button id="saveChangesBtn" class="px-4 py-2 bg-sky-500 text-white rounded hover:bg-sky-600 transition">
                    <i class="bi bi-floppy mr-2"></i>Simpan
                </button>
            </div>

            <div id="jsonPreview" class="mb-4 hidden">
                <label class="block text-sm font-medium text-gray-700 mb-2">JSON Preview:</label>
                <textarea id="jsonOutput" class="w-full h-40 p-3 border border-gray-300 rounded-md font-mono text-sm bg-gray-50" readonly></textarea>
            </div>
        </div>
    </div>

    <template id="nodeInputTemplate">
        <div class="node-input-group border border-gray-200 rounded-lg p-4 bg-gray-50">
            <div class="flex justify-between items-center mb-3">
                <h4 class="font-medium text-gray-700">Node <span class="node-number"></span></h4>
                <button class="remove-node-btn text-red-500 hover:text-red-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Key:</label>
                    <input type="text" class="node-key w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Masukkan key">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Text:</label>
                    <input type="text" class="node-text w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Masukkan text">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Category:</label>
                    <input type="text" class="node-category w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Masukkan category">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Color:</label>
                    <input type="color" class="node-color w-full p-1 border border-gray-300 rounded h-10" value="#90CAF9">
                </div>
            </div>
        </div>
    </template>
@endsection

@section('script')
    <script> 
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

        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('nodeInputsContainer');
            const jsonOutput = document.getElementById('jsonOutput');
            const jsonPreview = document.getElementById('jsonPreview');
            
            const originalAnswer = @json($answer ?? '{}');
            let currentData = {};
            
            try {
                currentData = typeof originalAnswer === 'string' ? JSON.parse(originalAnswer) : originalAnswer;
            } catch (e) {
                console.error('Error parsing answer data:', e);
                currentData = { nodeDataArray: [] };
            }

            loadNodes();

            document.getElementById('saveChangesBtn').addEventListener('click', saveChanges);
            // document.getElementById('resetBtn').addEventListener('click', resetToOriginal);
            // document.getElementById('previewJsonBtn').addEventListener('click', toggleJsonPreview);

            function loadNodes() {
                container.innerHTML = '';
                
                if (!currentData.nodeDataArray || currentData.nodeDataArray.length === 0) {
                    container.innerHTML = '<p class="text-gray-500 text-center py-8">Tidak ada node data yang tersedia</p>';
                    return;
                }

                currentData.nodeDataArray.forEach((node, index) => {
                    createNodeInput(node, index);
                });
            }

            function createNodeInput(nodeData, index) {
                const nodeDiv = document.createElement('div');
                nodeDiv.className = 'node-input-group border border-gray-200 rounded-lg p-4 bg-gray-50';
                nodeDiv.innerHTML = `
                    <div class="mb-3">
                        <h4 class="font-medium text-gray-700">Node ${index + 1}</h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Key:</label>
                            <input type="text" class="node-key w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                value="${nodeData.key || ''}" placeholder="Masukkan text" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Text:</label>
                            <input type="text" class="node-text w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                value="${nodeData.text || ''}" placeholder="Masukkan text">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Category:</label>
                            <input type="text" class="node-category w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                value="${nodeData.category || ''}" placeholder="Masukkan category" readonly>
                        </div>
                    </div>
                `;

                const inputs = nodeDiv.querySelectorAll('input');
                inputs.forEach(input => {
                    input.addEventListener('input', updateCurrentData);
                });

                container.appendChild(nodeDiv);
            }

            function updateCurrentData() {
                const nodeGroups = container.querySelectorAll('.node-input-group');
                const updatedNodeDataArray = [];

                nodeGroups.forEach(group => {
                    const key = group.querySelector('.node-key').value || '';
                    const text = group.querySelector('.node-text').value || '';
                    const category = group.querySelector('.node-category').value || '';

                    updatedNodeDataArray.push({
                        key: key,
                        text: text,
                        category: category,
                    });
                });

                currentData.nodeDataArray = updatedNodeDataArray;
                
                if (!jsonPreview.classList.contains('hidden')) {
                    generateJsonPreview();
                }
            }

            function generateJsonPreview() {
                jsonOutput.value = JSON.stringify(currentData, null, 2);
            }

            function toggleJsonPreview() {
                jsonPreview.classList.toggle('hidden');
                if (!jsonPreview.classList.contains('hidden')) {
                    generateJsonPreview();
                }
            }

            function resetToOriginal() {
                if (confirm('Apakah Anda yakin ingin mereset ke data asli?')) {
                    try {
                        currentData = typeof originalAnswer === 'string' ? JSON.parse(originalAnswer) : originalAnswer;
                    } catch (e) {
                        currentData = { nodeDataArray: [] };
                    }
                    loadNodes();
                    showSuccessMessage('Data berhasil direset ke kondisi asli');
                }
            }

            function saveChanges() {
                const dataToSave = JSON.stringify(currentData);
                
                fetch('{{ route("update-expected-answer", $encryptedQuestion ?? 0) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        answer: dataToSave
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSuccessMessage('Perubahan berhasil disimpan!');

                        setTimeout(() => {
                            window.location.href = '{{ route('detail-tasks', $encryptedTask) }}';
                        }, 1500)
                    } else {
                        showErrorMessage('Gagal menyimpan perubahan: ' + (data.message || 'Unknown error'));
                        // showErrorMessage('Gagal menyimpan perubahan!');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showErrorMessage('Terjadi kesalahan saat menyimpan data' + (error.message || 'Unknown error'));
                });
            }
        });
    </script>
@endsection

    

    
