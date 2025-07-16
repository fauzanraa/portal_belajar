@extends('layout-admins.app')

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
        <h1>Halaman Soal</h1>
        <p class="text-[10px] tracking-[5px] text-slate-200">Halaman untuk menambahkan soal</p>
    </div>

    <div class="mt-10 bg-white p-5 pl-8 rounded-l-xl">
        <div>
            <label for="total question" class="block mb-2 text-sm font-medium text-gray-900">Jumlah Soal</label>
            <div class="flex gap-5">
                <input type="text" id="total_question" inputmode="numeric" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-60 p-2.5">
                <button id="generate-btn" class="px-5 py-2.5 text-sm rounded-lg bg-sky-500 text-sm hover:bg-sky-700 text-white cursor-pointer">
                    Tambah
                </button>
            </div>
        </div>
        <div class="mt-5">
            @php
                $encryptedTask = Illuminate\Support\Facades\Crypt::encrypt($data_tugas->id); 
            @endphp
            <form id="questions-form" action="{{route('store-questions', ['id' => $encryptedTask])}}" method="POST">
                @csrf
                <input type="hidden" name="total_question" id="total_question_hidden">
                
                <div id="questions-container"></div>
                
                <div class="mt-6">
                    <button id="submit-form" type="submit" class="px-5 py-2.5 bg-sky-500 hover:bg-sky-700 text-white rounded-lg" hidden>
                        <i class="bi bi-floppy-fill mr-3"></i> Simpan
                    </button>
                </div>
            </form>
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
    
        if (document.getElementById("meeting-table") && typeof simpleDatatables.DataTable !== 'undefined') {
            const dataTable = new simpleDatatables.DataTable("#meeting-table", {
                searchable: true,
                sortable: false
            });
        }

        if (document.getElementById("question-table") && typeof simpleDatatables.DataTable !== 'undefined') {
            const dataTable = new simpleDatatables.DataTable("#question-table", {
                searchable: true,
                sortable: false
            });
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

        document.getElementById('generate-btn').addEventListener('click', function() {
            const totalQuestions = parseInt(document.getElementById('total_question').value);
            const container = document.getElementById('questions-container');
            const form = document.getElementById('questions-form');
            
            if (!totalQuestions || totalQuestions < 1) {
                alert('Masukkan jumlah soal yang valid (minimal 1)');
                return;
            }
            
            if (totalQuestions > 50) {
                alert('Maksimal 50 soal');
                return;
            }
            
            document.getElementById('submit-form').hidden = false;
            document.getElementById('total_question_hidden').value = totalQuestions;
    
            container.innerHTML = '';
            
            let questionsHTML = '';
            for (let i = 1; i <= totalQuestions; i++) {
                questionsHTML += `
                    <div class="question-item mb-6 p-4 border border-gray-300 rounded-lg">
                        <h3 class="text-lg font-semibold mb-3">Soal ${i}</h3>
                        
                        <div class="mb-4">
                            <label class="block mb-2 text-sm font-medium text-gray-900">Pertanyaan:</label>
                            <textarea name="question[]" rows="3" class="w-full p-2.5 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500" placeholder="Masukkan soal" required></textarea>
                            <div>
                                <label for="type" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tipe Soal</label>
                                <select id="type_${i}" name="type[]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                    <option value="" selected disabled>Pilihan Tipe</option>
                                    <option value="intro">Pengenalan</option>
                                    <option value="study_case">Studi Kasus</option>
                                </select>
                            </div>
                        </div>

                        <div id="flowchart_components_${i}" class="mt-6">
                            <label class="block mb-2 text-sm font-medium text-gray-900">
                                Komponen wajib:
                            </label>
                            <select id="components_${i}" name="flowchart_components[${i}][]" 
                                    class="flowchart-components-select bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" 
                                    multiple="multiple">
                                <option value="Terminator">Terminator</option>
                                <option value="Process">Process</option>
                                <option value="Decision">Decision</option>
                                <option value="OnPageReference">On Page Reference</option>
                                <option value="OffPageReference">Off Page Reference</option>
                                <option value="Comment">Comment</option>
                                <option value="InputOutput">Input/Output</option>
                                <option value="ManualOperation">Manual Operation</option>
                                <option value="PredefinedProcess">Predefined Process</option>
                                <option value="Display">Display</option>
                                <option value="Preparation">Preparation</option>
                            </select>
                        </div>
                    </div>
                `;
            }
            
            container.innerHTML = questionsHTML;
            
            form.style.display = 'block';
            
            for (let i = 1; i <= totalQuestions; i++) {
                $(`#type_${i}`).select2({
                    placeholder: "Pilihan tipe",
                    allowClear: true,
                    width: '100%',
                    theme: 'classic',
                });

                $(`#components_${i}`).select2({
                    placeholder: "Pilih komponen flowchart",
                    allowClear: true,
                    width: '100%',
                    theme: 'classic'
                });

                // $(`#type_${i}`).on('change', function() {
                //     const selectedType = $(this).val();
                //     const flowchartContainer = $(`#flowchart_components_${i}`);
                    
                //     if (selectedType === 'flowchart') {
                //         flowchartContainer.show();
                //         $(`#components_${i}`).attr('required', true);
                //     } else {
                //         flowchartContainer.hide();
                //         $(`#components_${i}`).removeAttr('required');
                //         $(`#components_${i}`).val(null).trigger('change');
                //     }
                // });
            }
            
            // form.style.display = 'block';
            form.scrollIntoView({behavior: 'smooth'});
        });

        function getFormData() {
            const totalQuestions = parseInt(document.getElementById('total_question_hidden').value);
            const formData = new FormData();
            
            formData.append('total_question', totalQuestions);
            
            for (let i = 1; i <= totalQuestions; i++) {
                const question = $(`textarea[name="question[]"]:eq(${i-1})`).val();
                const type = $(`#type_${i}`).val(); // Selalu "main"
                const subjects = $(`#subject_${i}`).val() || [];
                const components = $(`#components_${i}`).val() || [];
                
                formData.append(`question[${i}]`, question);
                formData.append(`type[${i}]`, type);
                formData.append(`subjects[${i}]`, JSON.stringify(subjects));
                
                // Simpan komponen flowchart jika ada yang dipilih
                if (components.length > 0) {
                    formData.append(`flowchart_components[${i}]`, JSON.stringify(components));
                }
            }
            
            return formData;
        }

    </script>
@endsection