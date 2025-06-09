<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>FlowMatic</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
        @endif

        <style>
            html {
                scroll-behavior: smooth;
            }
            
            body {
                font-family: 'Fira Sans', sans-serif;
            }
            
            .fade-in-up {
                opacity: 0;
                transform: translateY(30px);
                animation: fadeInUp 1s ease-out forwards;
            }
            
            .fade-in-up-delay-1 {
                animation-delay: 0.2s;
            }
            
            .fade-in-up-delay-2 {
                animation-delay: 0.4s;
            }
            
            .fade-in-up-delay-3 {
                animation-delay: 0.6s;
            }
            
            .fade-in-up-delay-4 {
                animation-delay: 0.8s;
            }
            
            .fade-in-up-delay-5 {
                animation-delay: 1s;
            }
            
            @keyframes fadeInUp {
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .scroll-to-top {
                position: fixed;
                bottom: 20px;
                left: 20px;
                background: #0ea5e9;
                color: white;
                width: 50px;
                height: 50px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
                transition: all 0.3s ease;
                z-index: 1000;
                opacity: 0;
                visibility: hidden;
            }
            
            .scroll-to-top.show {
                opacity: 1;
                visibility: visible;
            }
            
            .scroll-to-top:hover {
                background: #0284c7;
                transform: translateY(-2px);
                box-shadow: 0 6px 16px rgba(14, 165, 233, 0.4);
            }

            /* Seamless gradient from nav to hero */
            .hero-nav-gradient {
                background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            }
        </style>
    </head>
    <body class="bg-white cursor-default">
        <div class="hero-nav-gradient">
            <!-- Navigation -->
            <nav>
                <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center py-3">
                        <div class="text-2xl font-bold text-white flex items-center">
                            <i class="bi bi-diagram-2 text-5xl"></i>
                            <span class="">FlowMatic</span>
                        </div>
                        
                        <div class="hidden md:flex items-center space-x-8">
                            <button class="w-full sm:w-auto px-7 py-2.5 bg-white text-sky-500 rounded-lg font-semibold text-lg hover:bg-sky-700 hover:text-white transition shadow-lg cursor-pointer" onclick="window.location.href='{{ route('login') }}'">
                                Masuk
                            </button>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Hero Section -->
            <section class="min-h-screen py-20">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="max-w-4xl mx-auto text-center">
                        <!-- Hook Statement -->
                        <h1 class="text-3xl md:text-5xl font-bold text-white mb-6 fade-in-up">
                            Flowchart: Langkah Pertama Menguasai Dasar-dasar Pemrograman
                        </h1>
                        
                        {{-- <p class="text-lg md:text-xl text-white/90 mb-4 fade-in-up fade-in-up-delay-1">
                            Dari pemula hingga mahir, pelajari teknik profesional membuat flowchart yang efektif dan mudah dipahami
                        </p> --}}
                        
                        <p class="text-base md:text-lg text-slate-300 mb-12 fade-in-up fade-in-up-delay-2">
                            ✨ Pahami konsep dasar untuk mempermudah proses pembelajaran!
                        </p>

                        <!-- Browser-like Container for Image -->
                        <div class="bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden fade-in-up fade-in-up-delay-3">
                            <!-- Browser Header -->
                            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 bg-red-400 rounded-full"></div>
                                    <div class="w-3 h-3 bg-yellow-400 rounded-full"></div>
                                    <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                                    <div class="ml-4 bg-white px-4 py-1 rounded text-sm text-gray-600 border">
                                        flowmatic.com
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Content Area for Image -->
                            <div class="h-64 md:h-96 bg-gray-50 border-2 border-dashed border-gray-200 flex items-center justify-center relative">
                                <div class="text-center">
                                    {{-- <div class="text-4xl md:text-6xl text-gray-300 mb-4">📊</div>
                                    <p class="text-gray-500 font-medium">Preview Course Content</p>
                                    <p class="text-gray-400 text-sm mt-2">Gambar akan ditampilkan di sini</p> --}}
                                </div>
                                
                                <img src="{{asset('img/bg-flowchart.jpg')}}" alt="Flowchart Course Preview" class="w-full h-full object-cover">
                                <!-- Placeholder for your image -->
                                <!-- Uncomment and add your image source when ready -->
                            </div>
                        </div>

                        <!-- CTA Buttons -->
                        <div class="mt-12 flex flex-col sm:flex-row gap-4 justify-center items-center fade-in-up fade-in-up-delay-4">
                            <button class="w-full sm:w-auto px-8 py-4 bg-white text-sky-500 rounded-lg font-semibold text-lg hover:bg-sky-700 hover:text-white transition shadow-lg cursor-pointer" onclick="window.location.href='{{ route('login') }}'">
                                🚀 Mulai Belajar Sekarang
                            </button>
                            
                            {{-- <button class="w-full sm:w-auto px-8 py-4 border-2 border-white text-white rounded-lg font-semibold text-lg hover:bg-white hover:text-sky-500 transition">
                                📖 Lihat Kurikulum
                            </button> --}}
                        </div>

                        <!-- Social Proof -->
                        {{-- <div class="mt-12 text-center fade-in-up fade-in-up-delay-5">
                            <p class="text-white/90 mb-4">Dipercaya oleh 1000+ profesional</p>
                            <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-8 text-white">
                                <div class="text-center">
                                    <div class="text-2xl font-bold">1000+</div>
                                    <div class="text-sm text-white/80">Siswa</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold">4.9/5</div>
                                    <div class="text-sm text-white/80">Rating</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold">30</div>
                                    <div class="text-sm text-white/80">Hari</div>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </section>
        </div>

        <!-- Features Preview -->
        <section id="features" class="bg-white py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="max-w-4xl mx-auto text-center">
                    <h2 class="text-3xl md:text-4xl font-bold text-sky-500 mb-12">
                        Mengapa FlowMatic?
                    </h2>
                    
                    <div class="grid md:grid-cols-3 gap-8">
                        <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                            <div class="text-4xl mb-4">⚡</div>
                            <h3 class="text-xl font-semibold text-sky-500 mb-4">Praktis & Cepat</h3>
                            <p class="text-gray-600">Langsung praktek dengan tools, tanpa teori yang membosankan</p>
                        </div>
                        
                        <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                            <div class="text-4xl mb-4">🎯</div>
                            <h3 class="text-xl font-semibold text-sky-500 mb-4">Logika Dasar</h3>
                            <p class="text-gray-600">Belajar dimulai dari awal terkait logika pemrograman</p>
                        </div>
                        
                        <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                            <div class="text-4xl mb-4">👨‍🏫</div>
                            <h3 class="text-xl font-semibold text-sky-500 mb-4">Auto Grading</h3>
                            <p class="text-gray-600">Sistem penilaian otomatis, untuk mengoptimalkan proses evaluasi</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Scroll to Top Button -->
        <div class="scroll-to-top" id="scrollToTop">
            <i class="bi bi-arrow-up text-xl"></i>
        </div>

        <script>
            // Scroll to top functionality
            const scrollToTopBtn = document.getElementById('scrollToTop');
            
            window.addEventListener('scroll', () => {
                if (window.pageYOffset > 300) {
                    scrollToTopBtn.classList.add('show');
                } else {
                    scrollToTopBtn.classList.remove('show');
                }
            });
            
            scrollToTopBtn.addEventListener('click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });

            // Intersection Observer for scroll animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Observe feature cards for scroll animation
            document.addEventListener('DOMContentLoaded', () => {
                const featureCards = document.querySelectorAll('#features .grid > div');
                featureCards.forEach((card, index) => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(30px)';
                    card.style.transition = `opacity 0.6s ease ${index * 0.2}s, transform 0.6s ease ${index * 0.2}s`;
                    observer.observe(card);
                });
            });
        </script>
    </body>
</html>
