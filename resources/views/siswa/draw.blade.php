@extends('layout-admins.app')

@section('csrf-token')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('onload')
    onload="init()"
@endsection

@section('content')
    <div class="container mx-auto px-4 py-8 min-h-screen">
        <div class="bg-white rounded-2xl shadow-2xl mb-8 border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-6 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="bg-white bg-opacity-20 rounded-xl p-3 mr-4 backdrop-blur-sm">
                            <i class="bi bi-book text-2xl text-sky-500"></i>
                        </div>
                        <h1 class="text-2xl font-bold">Modul</h1>
                    </div>
                    <div id="timer" class="font-bold text-lg"></div>
                </div>
            </div>

            <div class="p-8">
                <div class="mb-8">
                  <div class="flex items-center justify-between mb-6">
                      <div class="flex items-center">
                          <div class="bg-indigo-100 rounded-lg p-2 mr-3">
                              <i class="bi bi-question-circle-fill text-indigo-600"></i>
                          </div>
                          <div>
                              <h3 class="text-lg font-bold text-gray-800">Soal</h3>
                          </div>
                      </div>
                  </div>
                    <div id="questionContent" class="min-h-[250px] bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl p-6 border-l-4 border-blue-500 shadow-sm">
                        @php $questionIndex = 1; @endphp
                        @foreach ($questionTask as $data)
                            <div class="question-item {{ $questionIndex == 1 ? 'block' : 'hidden' }} transition-all duration-500 ease-in-out" 
                                data-question="{{ $questionIndex }}"
                                data-task-id="{{ $sessionTask->id }}"
                                data-question-id="{{ $data->id }}">
                                <div class="flex items-start">
                                    <div class="flex-1">
                                        <p class="text-gray-800 leading-relaxed text-lg">{{ $data->question }}</p>
                                    </div>
                                </div>
                            </div>
                            @php $questionIndex++; @endphp
                        @endforeach
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-6">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <div class="bg-indigo-100 rounded-lg p-2 mr-3">
                                <i class="bi bi-grid-3x3-gap-fill text-indigo-600"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-800">Navigasi Soal</h3>
                            </div>
                        </div>
                    </div>

                    <div class="relative mb-6">
                        <button id="scrollLeft" 
                                class="absolute left-0 top-1/2 transform -translate-y-1/2 -translate-x-4 z-20 bg-white shadow-md rounded-full p-2 text-gray-500 hover:text-gray-700 transition-colors duration-200 border border-gray-200 opacity-0 pointer-events-none">
                            <i class="bi bi-chevron-left"></i>
                        </button>

                        <div class="flex justify-center">
                            <div class="overflow-hidden max-w-md">
                                <div id="questionScroller" 
                                    class="flex space-x-3 transition-transform duration-300 ease-out py-2"
                                    style="transform: translateX(0px)">
                                    @php $questionCount = 1; @endphp
                                    @foreach ($questionTask as $data)
                                      <button class="question-btn flex-shrink-0 w-12 h-12 rounded-lg border-2 border-gray-200 bg-white hover:bg-blue-50 hover:border-blue-300 text-gray-700 hover:text-blue-600 font-medium transition-colors duration-200 flex items-center justify-center {{ $questionCount == 1 ? 'bg-sky-500 text-white border-sky-500' : '' }}" 
                                              data-question="{{ $questionCount }}"
                                              data-task-id="{{ $sessionTask->id }}"
                                              data-question-id="{{ $data->id }}"
                                              data-answered="false">
                                          
                                          <span>{{ $questionCount }}</span>
                                          
                                          <div class="absolute -top-1 -right-1 w-4 h-4 bg-green-500 rounded-full opacity-0 transition-opacity duration-200 flex items-center justify-center answered-indicator">
                                              <i class="bi bi-check text-white text-xs"></i>
                                          </div>
                                      </button>
                                        @php $questionCount++; @endphp
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <button id="scrollRight" 
                                class="absolute right-0 top-1/2 transform -translate-y-1/2 translate-x-4 z-20 bg-white shadow-md rounded-full p-2 text-gray-500 hover:text-gray-700 transition-colors duration-200 border border-gray-200 opacity-0 pointer-events-none">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>

                    <div class="flex items-center justify-center gap-4">
                      <button id="prevQuestion" 
                              class="flex items-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-800 rounded-xl transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed group transform hover:scale-105 disabled:hover:scale-100 shadow-sm hover:shadow-md"
                              disabled>
                          <i class="bi bi-chevron-left mr-2 group-hover:-translate-x-1 transition-transform duration-300"></i>
                          <span class="font-medium">Sebelumnya</span>
                      </button>

                      <button id="nextQuestion" 
                              class="flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed group transform hover:scale-105 disabled:hover:scale-100 shadow-lg hover:shadow-xl">
                          <span class="font-medium">Selanjutnya</span>
                          <i class="bi bi-chevron-right ml-2 group-hover:translate-x-1 transition-transform duration-300"></i>
                      </button>
                    </div>

                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-6">
            <div class="lg:w-1/4 w-full">
                <div class="bg-white rounded-xl shadow-lg p-4 h-[600px] border border-gray-200">
                    <div class="flex items-center mb-4">
                        <i class="bi bi-tools text-xl text-indigo-500 mr-2"></i>
                        <h2 class="text-lg font-semibold text-gray-700">Komponen Flowchart</h2>
                    </div>
                    <div id="myPaletteDiv" class="border-2 border-dashed border-gray-300 rounded-lg h-[520px] bg-gray-50 hover:border-indigo-300 transition-colors duration-300"></div>
                </div>
            </div>
            
            <div class="lg:w-3/4 w-full flex flex-col gap-4">
                <div class="bg-white rounded-xl shadow-lg p-4 h-full border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <i class="bi bi-pencil-square text-xl text-green-500 mr-2"></i>
                            <h2 class="text-lg font-semibold text-gray-700">Area Gambar Flowchart</h2>
                        </div>
                    </div>
                    <div id="myDiagramDiv" class="border-2 border-dashed border-gray-300 rounded-lg h-[520px] bg-gradient-to-br from-gray-50 to-white hover:border-green-300 transition-colors duration-300 relative overflow-hidden">
                        <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle, #e5e7eb 1px, transparent 1px); background-size: 20px 20px;"></div>
                    </div>
                </div>  
            </div>
        </div>

        <div class="mt-6 bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <div class="grid gap-4">
                <button onclick="event.preventDefault(); showConfirmation(saveFlowchartToDatabase)" class="group relative overflow-hidden bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    <div class="flex items-center justify-center">
                        <span>Selesai</span>
                    </div>
                    <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                </button>
            </div>
        </div>

        <div class="hidden mt-8">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Model JSON:</h3>
            <textarea id="mySavedModel" class="w-full h-64 p-2 border border-gray-300 rounded-md bg-gray-50">
                { 
                    "nodeDataArray": [],
                    "linkDataArray": []
                }
            </textarea>
        </div>

        <div class="fixed bottom-6 right-6 z-50">
            <button class="bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white p-4 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110" onclick="showHelp()">
                <i class="bi bi-person-raised-hand"></i>
            </button>
        </div>
    </div>

    @php
        $encryptedId = Illuminate\Support\Facades\Crypt::encrypt($sessionTask->id);
    @endphp

    <style>
        /* Custom animations */
        @keyframes pulse-border {
            0%, 100% { border-color: #e5e7eb; }
            50% { border-color: #6366f1; }
        }
        
        .animate-pulse-border {
            animation: pulse-border 2s ease-in-out infinite;
        }
        
        /* Hover effects for diagram area */
        #myDiagramDiv:hover {
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.1);
        }
        
        #myPaletteDiv:hover {
            box-shadow: 0 0 20px rgba(129, 140, 248, 0.1);
        }
    </style>
@endsection


@section('script')
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    
    <script src="{{ asset('js/flowchart.js') }}"></script>

    <script type="text/javascript" src="{{ asset('js/mxgraph/javascript/mxClient.js') }}"></script>

    <script>
        function showHelp() {
            Swal.fire({
                title: 'Panduan',
                icon: 'info',
                html: `
                    <ul class="text-sm text-justify leading-relaxed">
                        <li>- Drag komponen dari panel kiri ke area gambar</li>
                        <li>- Hubungkan komponen dengan menarik garis</li>
                        <li>- Klik dua kali pada komponen untuk mengedit teks</li>
                        <li>- Navigasi soal digunakan untuk memilih soal</li>
                        <li>- Tombol selesai digunakan untuk menyimpan jawaban</li>
                    </ul>

                    <div class="mt-2">
                        <span class="text-xs text-red-500"> nb: segera lapor jika terjadi gangguan saat pengerjaan
                    </div
                `,
                confirmButtonText: 'Ok'
            });
        }

        setInterval(() => {
            if (typeof myDiagram !== 'undefined' && myDiagram.model.nodeDataArray.length > 0) {
                // updateProgress();
                // Auto-save logic here
                console.log('Auto-saving...');
            }
        }, 30000);

        $(document).ready(function() {
            let totalDuration = {{$sessionTask->taskSession->duration}} * 60; 
            let storedTime = localStorage.getItem('timer_{{$sessionTask->id}}_{{$studentId}}');
            let startTime = localStorage.getItem('startTime_{{$sessionTask->id}}_{{$studentId}}');
            
            if (!startTime) {
                startTime = Date.now(); 
                localStorage.setItem('startTime_{{$sessionTask->id}}_{{$studentId}}', startTime); 
            }
            
            let timerInterval = setInterval(function() {
                let currentTime = Date.now(); 
                let elapsedTime = Math.floor((currentTime - startTime) / 1000); 
                
                let remainingTime = totalDuration - elapsedTime; 
            
                let duration = totalDuration - remainingTime;
                
                if (remainingTime <= 0) {
                    clearInterval(timerInterval); 

                    showTimeoutMessage('Waktu anda telah habis!');
                    
                    setTimeout(function() {
                        let remainingTime = localStorage.getItem('timer_{{ $sessionTask->id }}_{{ $studentId}}'); 
                        let duration = totalDuration - remainingTime;

                        localStorage.removeItem('timer_{{ $sessionTask->id }}_{{ $studentId }}');
                        localStorage.removeItem('startTime_{{ $sessionTask->id }}_{{ $studentId }}');
                        
                        saveFlowchartToDatabase();
                    }, 3000); 
                }
            
                let hours = Math.floor(remainingTime / 3600); 
                let minutes = Math.floor((remainingTime % 3600) / 60); 
                let seconds = remainingTime % 60; 
                
                let timerElement = document.getElementById('timer');
                timerElement.innerHTML = hours + ":" + (minutes < 10 ? '0' : '') + minutes + ":" + (seconds < 10 ? '0' : '') + seconds;
            
                localStorage.setItem('timer_{{$sessionTask->id}}_{{$studentId}}', remainingTime);
            }, 1000);

        })

        document.addEventListener('DOMContentLoaded', function() {
            const scroller = document.getElementById('questionScroller');
            const scrollLeft = document.getElementById('scrollLeft');
            const scrollRight = document.getElementById('scrollRight');
            const questionButtons = document.querySelectorAll('.question-btn');
            
            // Show scroll buttons only if more than 5 questions
            if (questionButtons.length > 5) {
                scrollRight.classList.remove('opacity-0', 'pointer-events-none');
                
                scrollLeft.addEventListener('click', function() {
                    scroller.scrollBy({ left: -200, behavior: 'smooth' });
                    updateScrollButtons();
                });
                
                scrollRight.addEventListener('click', function() {
                    scroller.scrollBy({ left: 200, behavior: 'smooth' });
                    updateScrollButtons();
                });
                
                function updateScrollButtons() {
                    const maxScroll = scroller.scrollWidth - scroller.clientWidth;
                    
                    if (scroller.scrollLeft <= 0) {
                        scrollLeft.classList.add('opacity-0', 'pointer-events-none');
                    } else {
                        scrollLeft.classList.remove('opacity-0', 'pointer-events-none');
                    }
                    
                    if (scroller.scrollLeft >= maxScroll) {
                        scrollRight.classList.add('opacity-0', 'pointer-events-none');
                    } else {
                        scrollRight.classList.remove('opacity-0', 'pointer-events-none');
                    }
                }
                
                scroller.addEventListener('scroll', updateScrollButtons);
            }
        });

        let flowchartProgress = {};
        let currentQuestionId = null;

        // Inisialisasi progress untuk semua soal
        function initializeProgress() {
            @php $questionIndex = 1; @endphp
            @foreach ($questionTask as $data)
                flowchartProgress['{{ $questionIndex }}'] = {
                    nodeDataArray: [],
                    linkDataArray: []
                };
                @php $questionIndex++; @endphp
            @endforeach
            
            currentQuestionId = '1'; // Set soal pertama sebagai aktif
        };

        function cleanFlowchartData(data) {
            if (!data || typeof data !== 'object') return data;
            
            try {
                // Jika data sudah berupa string JSON, parse dulu
                if (typeof data === 'string') {
                    data = JSON.parse(data);
                }
                
                // Buat objek bersih hanya dengan properti yang diperlukan
                const cleanData = {
                    nodeDataArray: [],
                    linkDataArray: []
                };
                
                // Bersihkan nodeDataArray
                if (data.nodeDataArray && Array.isArray(data.nodeDataArray)) {
                    cleanData.nodeDataArray = data.nodeDataArray.map(node => ({
                        key: node.key,
                        text: node.text || '',
                        category: node.category || '',
                        figure: node.figure || '',
                        loc: node.loc || ''
                    }));
                }
                
                // Bersihkan linkDataArray
                if (data.linkDataArray && Array.isArray(data.linkDataArray)) {
                    cleanData.linkDataArray = data.linkDataArray.map(link => ({
                        from: link.from,
                        to: link.to,
                        text: link.text || '',
                        visible: link.visible || false,
                        points: link.points || []
                    }));
                }
                
                return cleanData;
            } catch (e) {
                console.error('Error cleaning flowchart data:', e);
                return { nodeDataArray: [], linkDataArray: [] };
            }
        }

        function saveCurrentProgress() {
            if (currentQuestionId && myDiagram) {
                try {
                    const modelJson = myDiagram.model.toJson();
                    const parsedData = JSON.parse(modelJson);
                    
                    flowchartProgress[currentQuestionId] = cleanFlowchartData(parsedData);
                    console.log('Progress saved for question:', currentQuestionId);
                } catch (e) {
                    console.error('Error saving progress:', e);
                    flowchartProgress[currentQuestionId] = { nodeDataArray: [], linkDataArray: [] };
                }
            }
        }

        // Muat progress untuk soal tertentu
        function loadProgressForQuestion(questionId) {
            if (myDiagram && flowchartProgress[questionId]) {
                try {
                    const progress = flowchartProgress[questionId];
                    const cleanedProgress = cleanFlowchartData(progress);
                    
                    myDiagram.model = new go.GraphLinksModel(
                        cleanedProgress.nodeDataArray, 
                        cleanedProgress.linkDataArray
                    );
                    console.log('Progress loaded for question:', questionId);
                } catch (e) {
                    console.error('Error loading progress:', e);
                    myDiagram.model = new go.GraphLinksModel([], []);
                }
            } else if (myDiagram) {
                // Reset diagram jika tidak ada progress
                myDiagram.model = new go.GraphLinksModel([], []);
            }
        }

        // Cek apakah soal sudah dikerjakan (ada komponen)
        function isQuestionAnswered(questionId) {
            return flowchartProgress[questionId] && 
                    flowchartProgress[questionId].nodeDataArray.length > 0;
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi progress saat halaman dimuat
            initializeProgress();
            
            // Event listener untuk tombol nomor soal
            document.querySelectorAll('.question-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const questionId = this.getAttribute('data-question');
                    switchToQuestion(questionId);
                });
            });
            
            // Event listener untuk tombol Previous/Next
            document.getElementById('prevQuestion').addEventListener('click', function() {
                const currentNum = parseInt(currentQuestionId);
                if (currentNum > 1) {
                    switchToQuestion((currentNum - 1).toString());
                }
            });
            
            document.getElementById('nextQuestion').addEventListener('click', function() {
                const currentNum = parseInt(currentQuestionId);
                const totalQuestions = Object.keys(flowchartProgress).length;
                if (currentNum < totalQuestions) {
                    switchToQuestion((currentNum + 1).toString());
                }
            });
        });

        function switchToQuestion(questionId) {
            console.log('Switching to question:', questionId);
            
            // Simpan progress soal saat ini
            if (typeof saveCurrentProgress === 'function') {
                saveCurrentProgress();
            }
            
            // Sembunyikan semua soal
            document.querySelectorAll('.question-item').forEach(item => {
                item.classList.add('hidden');
            });
            
            // Tampilkan soal yang dipilih
            const targetQuestion = document.querySelector(`[data-question="${questionId}"]`);
            if (targetQuestion) {
                targetQuestion.classList.remove('hidden');
            }
            
            // Reset semua tombol ke state normal
            document.querySelectorAll('.question-btn').forEach(btn => {
                btn.classList.remove('bg-sky-500', 'text-white', 'border-sky-500');
                btn.classList.add('bg-white', 'text-gray-700', 'border-gray-200');
            });
            
            // Set tombol aktif dengan warna sky-500
            const activeBtn = document.querySelector(`button.question-btn[data-question="${questionId}"]`);
            if (activeBtn) {
                activeBtn.classList.remove('bg-white', 'text-gray-700', 'border-gray-200');
                activeBtn.classList.add('bg-sky-500', 'text-white', 'border-sky-500');
            }
            
            // Muat progress untuk soal baru
            if (typeof loadProgressForQuestion === 'function') {
                loadProgressForQuestion(questionId);
            }
            
            // Update current question ID
            if (typeof currentQuestionId !== 'undefined') {
                currentQuestionId = questionId;
            }
            
            // Update tombol Previous/Next
            if (typeof updateNavigationButtons === 'function') {
                updateNavigationButtons();
            }
        }

        // Event listener untuk tombol nomor soal
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Setting up question navigation...');
            
            // Event listener untuk tombol nomor soal
            document.querySelectorAll('.question-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const questionId = this.getAttribute('data-question');
                    console.log('Question button clicked:', questionId);
                    switchToQuestion(questionId);
                });
            });
            
            // Event listener untuk tombol Previous/Next
            const prevBtn = document.getElementById('prevQuestion');
            const nextBtn = document.getElementById('nextQuestion');
            
            if (prevBtn) {
                prevBtn.addEventListener('click', function() {
                    const currentNum = parseInt(currentQuestionId || '1');
                    if (currentNum > 1) {
                        switchToQuestion((currentNum - 1).toString());
                    }
                });
            }
            
            if (nextBtn) {
                nextBtn.addEventListener('click', function() {
                    const currentNum = parseInt(currentQuestionId || '1');
                    // Hitung total soal
                    const totalQuestions = document.querySelectorAll('.question-btn').length;
                    if (currentNum < totalQuestions) {
                        switchToQuestion((currentNum + 1).toString());
                    }
                });
            }
            
            // Pastikan soal pertama aktif saat load
            setTimeout(() => {
                switchToQuestion('1');
            }, 100);
        });

        function updateQuestionIndicator(questionId) {
            const button = document.querySelector(`[data-question="${questionId}"]`);
            if (button && isQuestionAnswered(questionId)) {
                button.setAttribute('data-answered', 'true');
                const indicator = button.querySelector('.answered-indicator');
                if (indicator) {
                    indicator.classList.remove('opacity-0');
                    indicator.classList.add('opacity-100');
                }
            }
        }

        // Update status tombol Previous/Next
        function updateNavigationButtons() {
            const currentNum = parseInt(currentQuestionId);
            const totalQuestions = Object.keys(flowchartProgress).length;
            
            const prevBtn = document.getElementById('prevQuestion');
            const nextBtn = document.getElementById('nextQuestion');
            
            // Previous button
            if (currentNum <= 1) {
                prevBtn.disabled = true;
            } else {
                prevBtn.disabled = false;
            }
            
            // Next button
            if (currentNum >= totalQuestions) {
                nextBtn.disabled = true;
            } else {
                nextBtn.disabled = false;
            }
        }

        // Auto-save progress setiap ada perubahan pada diagram
        function setupAutoSave() {
            if (myDiagram) {
                myDiagram.addDiagramListener("Modified", function(e) {
                    // Auto-save setiap ada perubahan
                    setTimeout(() => {
                        saveCurrentProgress();
                        updateQuestionIndicator(currentQuestionId);
                    }, 500);
                });
            }
        }

        let isFormSubmitting = false;
        let isNavigating = false;

        document.addEventListener('DOMContentLoaded', function () {
            const finishButton = document.querySelector('button[onclick*="saveFlowchartToDatabase"]');
            if (finishButton) {
                finishButton.addEventListener('click', function () {
                    isFormSubmitting = true;
                });
            }
        });

        // window.addEventListener('beforeunload', function (e) {
        //     if (isFormSubmitting || isNavigating) return;

        //     const message = 'Apakah Anda yakin ingin meninggalkan halaman ini?';
        //     e.preventDefault();
        //     e.returnValue = message; 
        //     return message;
        // });

        document.addEventListener('click', function (e) {
            if (isFormSubmitting) return;

            const target = e.target.closest('a');
            if (
                target &&
                target.href &&
                !target.href.startsWith('#') &&
                !target.href.startsWith('javascript:') &&
                target.target !== '_blank'
            ) {
                e.preventDefault();

                Swal.fire({
                    title: 'Perhatian',
                    text: "Perubahan yang belum disimpan akan hilang, lalu timer yang berjalan tidak akan berhenti.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Lanjutkan',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        isNavigating = true;
                        window.location.href = target.href;
                    }
                });
            }
        });
    </script>

    <script id="code">
      let allowedComponents = @json($pengaturanKomponen ?? []);
      console.log(allowedComponents);

      function init() {
        if (window.goSamples) goSamples();  // init for these samples -- you don't need to call this
          var $ = go.GraphObject.make;  // for conciseness in defining templates
          myDiagram =
            $(go.Diagram, "myDiagramDiv",  // must name or refer to the DIV HTML element
              {
                initialContentAlignment: go.Spot.Center,
                allowDrop: true,  // must be true to accept drops from the Palette
                "LinkDrawn": showLinkLabel,  // this DiagramEvent listener is defined below
                "LinkRelinked": showLinkLabel,
                "animationManager.duration": 800, // slightly longer than default (600ms) animation
                "undoManager.isEnabled": true  // enable undo & redo
              });
          // when the document is modified, add a "*" to the title and enable the "Save" button
          myDiagram.addDiagramListener("Modified", function(e) {
            var button = document.getElementById("SaveButton");
            if (button) button.disabled = !myDiagram.isModified;
            var idx = document.title.indexOf("*");
            if (myDiagram.isModified) {
              if (idx < 0) document.title += "*";
            } else {
              if (idx >= 0) document.title = document.title.substr(0, idx);
            }
          });
          // helper definitions for node templates
          function nodeStyle() {
            return [
              // The Node.location comes from the "loc" property of the node data,
              // converted by the Point.parse static method.
              // If the Node.location is changed, it updates the "loc" property of the node data,
              // converting back using the Point.stringify static method.
              new go.Binding("location", "loc", go.Point.parse).makeTwoWay(go.Point.stringify),
              {
                // the Node.location is at the center of each node
                locationSpot: go.Spot.Center,
                //isShadowed: true,
                //shadowColor: "#888",
                // handle mouse enter/leave events to show/hide the ports
                mouseEnter: function (e, obj) { showPorts(obj.part, true); },
                mouseLeave: function (e, obj) { showPorts(obj.part, false); }
              }
            ];
          }
          // Define a function for creating a "port" that is normally transparent.
          // The "name" is used as the GraphObject.portId, the "spot" is used to control how links connect
          // and where the port is positioned on the node, and the boolean "output" and "input" arguments
          // control whether the user can draw links from or to the port.
          function makePort(name, spot, output, input) {
            // the port is basically just a small circle that has a white stroke when it is made visible
            return $(go.Shape, "Circle",
                    {
                        fill: "transparent",
                        stroke: null,  // this is changed to "white" in the showPorts function
                        desiredSize: new go.Size(8, 8),
                        alignment: spot, alignmentFocus: spot,  // align the port on the main Shape
                        portId: name,  // declare this object to be a "port"
                        fromSpot: spot, toSpot: spot,  // declare where links may connect at this port
                        fromLinkable: output, toLinkable: input,  // declare whether the user may draw links to/from here
                        cursor: "pointer"  // show a different cursor to indicate potential link point
                    });
          }
          // define the Node templates for regular nodes
          var lightText = 'whitesmoke';
          myDiagram.nodeTemplateMap.add("",  // the default category
            $(go.Node, "Spot", nodeStyle(),
              // the main object is a Panel that surrounds a TextBlock with a rectangular Shape
              $(go.Panel, "Auto",
                $(go.Shape, "Rectangle",
                  { fill: "#00A9C9", stroke: null },
                  new go.Binding("figure", "figure")),
                $(go.TextBlock,
                  {
                    font: "bold 11pt Helvetica, Arial, sans-serif",
                    stroke: lightText,
                    margin: 8,
                    maxSize: new go.Size(160, NaN),
                    wrap: go.TextBlock.WrapFit,
                    editable: true
                  },
                  new go.Binding("text").makeTwoWay())
              ),
              // four named ports, one on each side:
                makePort("T", go.Spot.Top, false, true),
                makePort("TL", go.Spot.TopLeft, true, true),      
                makePort("TR", go.Spot.TopRight, true, true),     
                makePort("L", go.Spot.Left, true, true),
                makePort("R", go.Spot.Right, true, true),
                makePort("BL", go.Spot.BottomLeft, true, true),   
                makePort("BR", go.Spot.BottomRight, true, true),  
                makePort("B", go.Spot.Bottom, true, false)
          ));
          myDiagram.nodeTemplateMap.add("Terminator",
            $(go.Node, "Spot", nodeStyle(),
              $(go.Panel, "Auto",
                $(go.Shape, "RoundedRectangle",  // Bentuk rounded rectangle untuk terminator
                  { 
                    fill: "#79C900", 
                    stroke: null,
                    parameter1: 20  // Mengatur tingkat kelengkungan sudut
                  },
                  new go.Binding("fill", "color")),  // Binding untuk mengubah warna berdasarkan data
                $(go.TextBlock,
                  {
                    font: "bold 11pt Helvetica, Arial, sans-serif",
                    stroke: lightText,
                    margin: 8,
                    maxSize: new go.Size(160, NaN),
                    wrap: go.TextBlock.WrapFit,
                    editable: true
                  },
                  new go.Binding("text").makeTwoWay())
              ),
              // Port untuk koneksi
              makePort("T", go.Spot.Top, false, true),
              makePort("L", go.Spot.Left, true, true),
              makePort("R", go.Spot.Right, true, true),
              makePort("B", go.Spot.Bottom, true, false)
          ));
          myDiagram.nodeTemplateMap.add("Start",
            $(go.Node, "Spot", nodeStyle(),
              $(go.Panel, "Auto",
                $(go.Shape, "Circle",
                  { minSize: new go.Size(40, 40), fill: "#79C900", stroke: null }),
                $(go.TextBlock, "Start",
                  { font: "bold 11pt Helvetica, Arial, sans-serif", stroke: lightText },
                  new go.Binding("text"))
              ),
              // three named ports, one on each side except the top, all output only:
              makePort("L", go.Spot.Left, true, false),
              makePort("R", go.Spot.Right, true, false),
              makePort("B", go.Spot.Bottom, true, false)
          ));
          myDiagram.nodeTemplateMap.add("End",
            $(go.Node, "Spot", nodeStyle(),
              $(go.Panel, "Auto",
                $(go.Shape, "Circle",
                  { minSize: new go.Size(40, 40), fill: "#DC3C00", stroke: null }),
                $(go.TextBlock, "End",
                  { font: "bold 11pt Helvetica, Arial, sans-serif", stroke: lightText },
                  new go.Binding("text"))
              ),
              // three named ports, one on each side except the bottom, all input only:
              makePort("T", go.Spot.Top, false, true),
              makePort("L", go.Spot.Left, false, true),
              makePort("R", go.Spot.Right, false, true)
          ));
          myDiagram.nodeTemplateMap.add("OnPageReference",
            $(go.Node, "Spot", nodeStyle(),
              $(go.Panel, "Auto",
                $(go.Shape, "Circle",
                  { 
                    fill: "#4B0082", 
                    stroke: "#4B0082",
                    strokeWidth: 2,
                    minSize: new go.Size(50, 50),
                    maxSize: new go.Size(80, 80)
                  }),
                $(go.TextBlock,
                  {
                    font: "bold 14pt Helvetica, Arial, sans-serif",
                    stroke: "#4B0082",
                    margin: 5,
                    maxSize: new go.Size(60, NaN),
                    wrap: go.TextBlock.WrapFit,
                    editable: true,
                    textAlign: "center"
                  },
                  new go.Binding("text").makeTwoWay())
              ),
              // Port untuk koneksi
              makePort("T", go.Spot.Top, false, true),
              makePort("L", go.Spot.Left, true, true),
              makePort("R", go.Spot.Right, true, true),
              makePort("B", go.Spot.Bottom, true, false)
          ));
          myDiagram.nodeTemplateMap.add("OffPageReference",
            $(go.Node, "Spot", nodeStyle(),
              $(go.Panel, "Auto",
                $(go.Shape, {
                  geometry: go.Geometry.parse("M0 0 L160 0 L160 80 L80 110 L0 80 Z", true),  // Parameter true untuk filled
                  fill: "#FF8C00",     // Orange untuk fill
                  stroke: "#FF8C00",   // Orange untuk stroke
                  strokeWidth: 2
                }),
                $(go.TextBlock,
                  {
                    font: "bold 12pt Helvetica, Arial, sans-serif",
                    stroke: "white",  // Teks putih agar kontras dengan background orange
                    margin: 12,
                    maxSize: new go.Size(200, NaN),
                    wrap: go.TextBlock.WrapFit,
                    editable: true,
                    textAlign: "center"
                  },
                  new go.Binding("text").makeTwoWay())
              ),
              // Port untuk koneksi
              makePort("T", go.Spot.Top, false, true),
              makePort("L", go.Spot.Left, true, true),
              makePort("R", go.Spot.Right, true, true),
              makePort("B", go.Spot.Bottom, true, false)
          ));
          myDiagram.nodeTemplateMap.add("Comment",
            $(go.Node, "Auto", nodeStyle(),
              $(go.Shape, "File",
                { fill: "#556B2F", stroke: null }),
              $(go.TextBlock,
                {
                  margin: 5,
                  maxSize: new go.Size(200, NaN),
                  wrap: go.TextBlock.WrapFit,
                  textAlign: "center",
                  editable: true,
                  font: "bold 12pt Helvetica, Arial, sans-serif",
                  stroke: 'white'
                },
                new go.Binding("text").makeTwoWay())
              // no ports, because no links are allowed to connect with a comment
          ));
          myDiagram.nodeTemplateMap.add("InputOutput",
            $(go.Node, "Spot", nodeStyle(),
              $(go.Panel, "Auto",
                $(go.Shape, "Parallelogram1",  // Bentuk parallelogram untuk Input/Output
                  { fill: "#FFA500", stroke: null }),  // Warna orange
                $(go.TextBlock,
                  {
                    font: "bold 11pt Helvetica, Arial, sans-serif",
                    stroke: lightText,
                    margin: 8,
                    maxSize: new go.Size(160, NaN),
                    wrap: go.TextBlock.WrapFit,
                    editable: true
                  },
                  new go.Binding("text").makeTwoWay())
              ),
              // Port untuk koneksi
              makePort("T", go.Spot.Top, false, true),
              makePort("L", go.Spot.Left, true, true),
              makePort("R", go.Spot.Right, true, true),
              makePort("B", go.Spot.Bottom, true, false)
          ));
          myDiagram.nodeTemplateMap.add("ManualOperation",
            $(go.Node, "Spot", nodeStyle(),
              $(go.Panel, "Auto",
                $(go.Shape, "ManualOperation",  // Menggunakan bentuk bawaan GoJS
                  { 
                    fill: "#9370DB", 
                    stroke: "#9370DB",
                    strokeWidth: 2,
                    minSize: new go.Size(120, 60)
                  }),
                $(go.TextBlock,
                  {
                    font: "bold 11pt Helvetica, Arial, sans-serif",
                    stroke: "white",
                    margin: 10,
                    maxSize: new go.Size(160, NaN),
                    wrap: go.TextBlock.WrapFit,
                    editable: true,
                    textAlign: "center"
                  },
                  new go.Binding("text").makeTwoWay())
              ),
              // Port untuk koneksi
              makePort("T", go.Spot.Top, false, true),
              makePort("L", go.Spot.Left, true, true),
              makePort("R", go.Spot.Right, true, true),
              makePort("B", go.Spot.Bottom, true, false)
          ));
          myDiagram.nodeTemplateMap.add("PredefinedProcess",
            $(go.Node, "Spot", nodeStyle(),
              $(go.Panel, "Auto",
                $(go.Shape, "Procedure",  
                  { 
                    fill: "#4682B4", 
                    stroke: "#87CEEB",  
                    strokeWidth: 2,
                    minSize: new go.Size(80, 50)
                  }),
                $(go.TextBlock,
                  {
                    font: "bold 10pt Helvetica, Arial, sans-serif",
                    stroke: "white",  // Teks putih untuk kontras
                    margin: 8,
                    maxSize: new go.Size(120, NaN),
                    wrap: go.TextBlock.WrapFit,
                    editable: true,
                    textAlign: "center"
                  },
                  new go.Binding("text").makeTwoWay())
              ),
              // Port untuk koneksi
              makePort("T", go.Spot.Top, false, true),
              makePort("L", go.Spot.Left, true, true),
              makePort("R", go.Spot.Right, true, true),
              makePort("B", go.Spot.Bottom, true, false)
          ));
          myDiagram.nodeTemplateMap.add("Display",
            $(go.Node, "Spot", nodeStyle(),
              $(go.Panel, "Auto",
                $(go.Shape,
                  { 
                    geometry: go.Geometry.parse("M20 0 L160 0 Q180 0 180 20 L180 60 Q180 80 160 80 L20 80 L0 40 Z", true),
                    fill: "#FF69B4",     
                    stroke: "#FF69B4",   
                    strokeWidth: 2
                  }),
                $(go.TextBlock,
                  {
                    font: "bold 11pt Helvetica, Arial, sans-serif",
                    stroke: "white",  // Teks putih untuk kontras
                    margin: 10,
                    maxSize: new go.Size(160, NaN),
                    wrap: go.TextBlock.WrapFit,
                    editable: true,
                    textAlign: "center"
                  },
                  new go.Binding("text").makeTwoWay())
              ),
              // Port untuk koneksi
              makePort("T", go.Spot.Top, false, true),
              makePort("L", go.Spot.Left, true, true),
              makePort("R", go.Spot.Right, true, true),
              makePort("B", go.Spot.Bottom, true, false)
          ));
          myDiagram.nodeTemplateMap.add("Preparation",
            $(go.Node, "Spot", nodeStyle(),
              $(go.Panel, "Auto",
                $(go.Shape, "Hexagon",  // Bentuk hexagon untuk preparation
                  { 
                    fill: "#8B4513",     // Saddle Brown - warna coklat
                    stroke: "#8B4513",   // Warna seragam
                    strokeWidth: 2,
                    minSize: new go.Size(100, 60)
                  }),
                $(go.TextBlock,
                  {
                    font: "bold 11pt Helvetica, Arial, sans-serif",
                    stroke: "white",  // Teks putih untuk kontras
                    margin: 10,
                    maxSize: new go.Size(140, NaN),
                    wrap: go.TextBlock.WrapFit,
                    editable: true,
                    textAlign: "center"
                  },
                  new go.Binding("text").makeTwoWay())
              ),
              makePort("T", go.Spot.Top, true, true),           
              makePort("TR", go.Spot.TopRight, true, true),     
              makePort("R", go.Spot.Right, true, true),         
              makePort("BR", go.Spot.BottomRight, true, true),  
              makePort("B", go.Spot.Bottom, true, true),        
              makePort("BL", go.Spot.BottomLeft, true, true),   
              makePort("L", go.Spot.Left, true, true),          
              makePort("TL", go.Spot.TopLeft, true, true)
          ));

          // replace the default Link template in the linkTemplateMap
          myDiagram.linkTemplate =
            $(go.Link,
                {
                    routing: go.Link.AvoidsNodes,
                    curve: go.Link.JumpOver,
                    corner: 5, 
                    toShortLength: 4,
                    relinkableFrom: true,
                    relinkableTo: true,
                    reshapable: true,
                    resegmentable: true,
                    // PENTING: Binding yang eksplisit untuk port
                    fromPortId: "",
                    toPortId: "",
                    mouseEnter: function(e, link) { 
                        link.findObject("HIGHLIGHT").stroke = "rgba(30,144,255,0.2)"; 
                    },
                    mouseLeave: function(e, link) { 
                        link.findObject("HIGHLIGHT").stroke = "transparent"; 
                    }
                },
                // Binding eksplisit untuk mempertahankan port connections
                new go.Binding("fromPortId", "fromPort"),
                new go.Binding("toPortId", "toPort"),
                new go.Binding("points").makeTwoWay(),
                $(go.Shape,  // highlight shape
                    { isPanelMain: true, strokeWidth: 8, stroke: "transparent", name: "HIGHLIGHT" }),
                $(go.Shape,  // link path shape
                    { isPanelMain: true, stroke: "gray", strokeWidth: 2 }),
                $(go.Shape,  // arrowhead
                    { toArrow: "standard", stroke: null, fill: "gray"}),
                $(go.Panel, "Auto",  // link label
                    { visible: false, name: "LABEL", segmentIndex: 2, segmentFraction: 0.5},
                    new go.Binding("visible", "visible").makeTwoWay(),
                    $(go.Shape, "RoundedRectangle",
                        { fill: "#F8F8F8", stroke: null }),
                    $(go.TextBlock, "Yes",
                        {
                            textAlign: "center",
                            font: "10pt helvetica, arial, sans-serif",
                            stroke: "#333333"
                        },
                        new go.Binding("text").makeTwoWay())
                )
            );
          // Make link labels visible if coming out of a "conditional" node.
          // This listener is called by the "LinkDrawn" and "LinkRelinked" DiagramEvents.
          function showLinkLabel(e) {
            var label = e.subject.findObject("LABEL");
            if (label !== null) {
                var fromNode = e.subject.fromNode;
                
                // Hanya tampilkan label untuk node Decision (Diamond)
                if (fromNode.data.figure === "Diamond") {
                    label.visible = true;
                    
                    // Hitung jumlah link yang keluar dari node ini
                    var outgoingLinks = [];
                    fromNode.findLinksOutOf().each(function(link) {
                        outgoingLinks.push(link);
                    });
                    
                    // Tentukan teks berdasarkan urutan link
                    var linkIndex = outgoingLinks.indexOf(e.subject);
                    var labelText = "";
                    
                    if (linkIndex === 0) {
                        labelText = "Yes";
                    } else if (linkIndex === 1) {
                        labelText = "No";
                    } else {
                        labelText = "Option " + (linkIndex + 1);
                    }
                    
                    // Update teks label
                    myDiagram.model.setDataProperty(e.subject.data, "text", labelText);
                } else {
                    label.visible = false;
                }
            }
          }
          // temporary links used by LinkingTool and RelinkingTool are also orthogonal:
        //   myDiagram.toolManager.linkingTool.temporaryLink.routing = go.Link.Orthogonal;
        //   myDiagram.toolManager.relinkingTool.temporaryLink.routing = go.Link.Orthogonal;
          myDiagram.toolManager.linkingTool = new go.LinkingTool();
          myDiagram.toolManager.linkingTool.portGravity = 1; // Mengurangi "magnet effect"
          myDiagram.toolManager.linkingTool.archetypeLinkData = { routing: go.Link.Orthogonal };

            myDiagram.toolManager.linkingTool.insertLink = function(fromnode, fromport, tonode, toport) {
                var newlink = go.LinkingTool.prototype.insertLink.call(this, fromnode, fromport, tonode, toport);
                
                // Pastikan link menggunakan port yang benar-benar dipilih user
                if (newlink !== null) {
                    myDiagram.model.setFromKeyForLinkData(newlink.data, fromnode.data.key);
                    myDiagram.model.setToKeyForLinkData(newlink.data, tonode.data.key);
                    myDiagram.model.setDataProperty(newlink.data, "fromPort", fromport.portId);
                    myDiagram.model.setDataProperty(newlink.data, "toPort", toport.portId);
                }
                
                return newlink;
            };

          load();  // load an initial diagram from some JSON text
          // initialize the Palette that is on the left side of the page
          const paletteNodeDataArray = [];

          if (allowedComponents.includes('Terminator')) {
              paletteNodeDataArray.push({ category: "Terminator", text: "Start/End"});
          }
          if (allowedComponents.includes('Process')) {
              paletteNodeDataArray.push({ category: "Process", text: "Process" });
          }
          if (allowedComponents.includes('Decision')) {
              paletteNodeDataArray.push({ category: "Decision", text: "Decision", figure: "Diamond" });
          }
          if (allowedComponents.includes('InputOutput')) {
              paletteNodeDataArray.push({ category: "InputOutput", text: "Input/Output" });
          }
          if (allowedComponents.includes('ManualOperation')) {
              paletteNodeDataArray.push({ category: "ManualOperation", text: "Manual Process" });
          }
          if (allowedComponents.includes('Comment')) {
              paletteNodeDataArray.push({ category: "Comment", text: "Comment" });
          }
          if (allowedComponents.includes('PredefinedProcess')) {
              paletteNodeDataArray.push({ category: "PredefinedProcess", text: "Predefined Process" });
          }
          if (allowedComponents.includes('Display')) {
              paletteNodeDataArray.push({ category: "Display", text: "Display" });
          }
          if (allowedComponents.includes('Preparation')) {
              paletteNodeDataArray.push({ category: "Preparation", text: "Preparation" });
          }
          if (allowedComponents.includes('OnPageReference')) {
              paletteNodeDataArray.push({ category: "OnPageReference", text: "" });
          }
          if (allowedComponents.includes('OffPageReference')) {
              paletteNodeDataArray.push({ category: "OffPageReference", text: "" });
          }

          myPalette =
            $(go.Palette, "myPaletteDiv",  // must name or refer to the DIV HTML element
              {
                "animationManager.duration": 800, // slightly longer than default (600ms) animation
                nodeTemplateMap: myDiagram.nodeTemplateMap,  // share the templates used by myDiagram
                // model: new go.GraphLinksModel([  // specify the contents of the Palette
                //   { category: "OnPageReference", text: "" },      // On-Page Reference
                //   { category: "OffPageReference", text: "" },
                //   { category: "Terminator", text: "Start/End"},
                //   { text: "Process" },
                //   { text: "Decision", figure: "Diamond" },
                //   { category: "InputOutput", text: "Input/Output" },
                //   { category: "ManualOperation", text: "Manual Process" },
                //   { category: "Comment", text: "Comment" },
                //   { category: "PredefinedProcess", text: "Predefined Process" },
                //   { category: "Display", text: "Display" },
                //   { category: "Preparation", text: "Preparation" }, 
                // ])
                model: new go.GraphLinksModel(paletteNodeDataArray)
              });

            setupAutoSave();

          // Inisialisasi progress dan load soal pertama
          setTimeout(() => {
              initializeProgress();
              loadProgressForQuestion('1');
              updateNavigationButtons();
          }, 100);
      }
      
        function showPorts(node, show) {
            var diagram = node.diagram;
            if (!diagram || diagram.isReadOnly || !diagram.allowLink) return;

            node.ports.each(function(port) {
                if (show) {
                    // Tampilkan port dengan visual yang lebih jelas
                    port.stroke = "white";
                    port.strokeWidth = 3;
                    port.fill = "rgba(255, 255, 255, 0.3)";
                } else {
                    port.stroke = null;
                    port.strokeWidth = 1;
                    port.fill = "transparent";
                }
            });
        }
      
      function save() {
          document.getElementById("mySavedModel").value = myDiagram.model.toJson();
          myDiagram.isModified = false;
      }
      
      function load() {
          myDiagram.model = go.Model.fromJson(document.getElementById("mySavedModel").value);
      }
      
      function makeSVG() {
          var svg = myDiagram.makeSvg({
            scale: 0.5
          });
          svg.style.border = "1px solid black";
          obj = document.getElementById("SVGArea");
          obj.appendChild(svg);
          if (obj.children.length > 0) {
            obj.replaceChild(svg, obj.children[0]);
          }
      }
      
      function downloadSVG() {
          var svg = myDiagram.makeSvg({
            scale: 1.0
          });
          var svgBlob = new Blob([svg.outerHTML], { type: "image/svg+xml" });
          var svgUrl = URL.createObjectURL(svgBlob);
          var link = document.createElement("a");
          link.href = svgUrl;
          link.download = "flowchart.svg";
          link.click();
      }

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

      function showTimeoutMessage(message) {
          const notification = document.createElement('div');
          notification.className = 'fixed top-4 right-4 bg-yellow-400 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full';
          notification.innerHTML = `
              <div class="flex items-center">
                  <i class="bi bi-clock-fill mr-2"></i>
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

        function showConfirmation(callback) {
            Swal.fire({
                title: 'Perhatian',
                text: 'Pengerjaan anda tidak akan bisa dirubah setelah di submit.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Lanjutkan',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    callback();
                }
            });
        }   

        function generateFlowchartImageForQuestion(questionId) {
            if (!myDiagram) return null;
            
            const currentModelJson = myDiagram.model.toJson();
            
            try {
                if (flowchartProgress[questionId] && flowchartProgress[questionId].nodeDataArray.length > 0) {
                    const progress = cleanFlowchartData(flowchartProgress[questionId]);
                    
                    // Load model untuk soal ini
                    myDiagram.model = new go.GraphLinksModel(progress.nodeDataArray, progress.linkDataArray);
                    
                    // PERBAIKAN: Tunggu diagram ter-render
                    myDiagram.requestUpdate();
                    
                    // PERBAIKAN: Generate image dengan parameter yang lebih spesifik
                    const imageData = myDiagram.makeImageData({
                        scale: 1.0,
                        background: "white",
                        type: "image/png",
                    });
                    
                    // Restore model asli
                    myDiagram.model = go.Model.fromJson(currentModelJson);
                    
                    // PERBAIKAN: Validasi hasil image
                    if (imageData && imageData.startsWith('data:image/png;base64,')) {
                        console.log('Image generated successfully for question:', questionId);
                        return imageData;
                    } else {
                        console.warn('Invalid image data generated for question:', questionId);
                        return null;
                    }
                }
            } catch (error) {
                console.error('Error generating image for question', questionId, error);
                try {
                    myDiagram.model = go.Model.fromJson(currentModelJson);
                } catch (restoreError) {
                    console.error('Error restoring model:', restoreError);
                }
            }
            
            return null;
        }

        function saveFlowchartToDatabase() {
            saveCurrentProgress();

            const isAutoSave = !event || !event.target;

            const allAnswers = [];
            const allImages = [];
            
            @php $questionIndex = 1; @endphp
            @foreach ($questionTask as $data)
            try {
                const questionData = flowchartProgress['{{ $questionIndex }}'] || { nodeDataArray: [], linkDataArray: [] };
                const cleanedData = cleanFlowchartData(questionData);
                
                allAnswers.push({
                    question_id: {{ $data->id }},
                    flowchart_data: JSON.stringify(cleanedData) // Gunakan data yang sudah dibersihkan
                });

                // Generate image hanya jika ada data
                let imageData = null;
                if (cleanedData.nodeDataArray.length > 0) {
                    imageData = generateFlowchartImageForQuestion('{{ $questionIndex }}');
                }
                
                allImages.push({
                    question_id: {{ $data->id }},
                    flowchart_image: imageData
                });
            } catch (e) {
                console.error('Error processing question {{ $questionIndex }}:', e);
                // Fallback dengan data kosong
                allAnswers.push({
                    question_id: {{ $data->id }},
                    flowchart_data: JSON.stringify({ nodeDataArray: [], linkDataArray: [] })
                });
                allImages.push({
                    question_id: {{ $data->id }},
                    flowchart_image: null
                });
            }
                @php $questionIndex++; @endphp
            @endforeach
            
            // if (allAnswers.length === 0) {
            //     showErrorMessage('Tidak ada jawaban untuk disimpan!');
            //     return;
            // }

            // let flowchartImage = null;
            // if (myDiagram && myDiagram.model.nodeDataArray.length > 0) {
            //     try {
            //         flowchartImage = myDiagram.makeImageData({
            //             scale: 1,
            //             background: "white",
            //             type: "image/png",
            //             details: 1.0
            //         });
            //     } catch (error) {
            //         console.error('Error generating flowchart image:', error);
            //     }
            // }
            
            // Update button state
            let saveButton, originalText;
            if (!isAutoSave && event.target) {
                saveButton = event.target;
                originalText = saveButton.innerHTML;
                saveButton.innerHTML = 'Menyimpan...';
                saveButton.disabled = true;
            }

            let totalDuration = {{$sessionTask->taskSession->duration}} * 60; 
            let remainingTime = localStorage.getItem('timer_{{$sessionTask->id}}_{{$studentId}}');
            let duration = totalDuration - remainingTime;  
            
            // Siapkan data untuk dikirim
            const requestData = {
                question_id: allAnswers,
                duration: duration,
                student_id: @json($studentId),
                flowchart_data: JSON.stringify(allAnswers[0]),
                flowchart_images: allImages ?? null
            };

            // Kirim ke server
            fetch('{{ route("store-flowchart", $sessionTask->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(requestData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessMessage('Flowchart berhasil disimpan! Mengalihkan...');
                    
                    setTimeout(() => {
                        localStorage.removeItem('timer_{{ $sessionTask->id }}_{{ $studentId }}');
                        localStorage.removeItem('startTime_{{ $sessionTask->id }}_{{ $studentId }}');

                        const redirectUrl = '{{ route("summary", ["idTask" => $encryptedId, "from" => "draw-flowchart"]) }}';

                        window.location.href = redirectUrl;
                    }, 1000);
                } else {
                    showErrorMessage('Gagal menyimpan: ' + (data.message || 'Terjadi kesalahan'));
                    if (!isAutoSave && saveButton) {
                        saveButton.innerHTML = originalText;
                        saveButton.disabled = false;
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorMessage('Terjadi kesalahan saat menyimpan flowchart: ' + error.message);
                if (!isAutoSave && saveButton) {
                    saveButton.innerHTML = originalText;
                    saveButton.disabled = false;
                }
            });
        }


      $(function(){
          $('#sample').trigger('onload');
      });
    </script>
@endsection