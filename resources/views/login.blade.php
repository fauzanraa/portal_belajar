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

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
        @endif
    </head>
    <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col font-fira cursor-default">
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
        <div class="flex items-center justify-center w-full transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0">
            <main class="flex max-w-[335px] w-full flex-col-reverse lg:max-w-4xl lg:flex-row">
                <div class="text-[13px] leading-[20px] flex-1 p-6 pb-12 lg:p-20 bg-white dark:bg-[#161615] dark:text-[#EDEDEC] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-bl-lg rounded-br-lg lg:rounded-tl-lg lg:rounded-br-none">
                    <h1 class="text-2xl font-medium">Selamat <span class="text-sky-500">Datang</span></h1>
                    <p class="mb-6 text-gray-700 dark:text-white">Masuk dan lanjutkan eksplorasi anda.</p>
                    
                    <form method="POST" action="{{route('postLogin')}}" class="space-y-4">
                        @csrf
                        
                        <div>
                            <label for="username" class="block mb-1 font-medium">Username</label>
                            <input id="username" name="username" required autofocus class="w-full px-3 py-2 border rounded-sm border-gray-300 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 dark:text-black">
                        </div>

                        <div>
                            <label for="password" class="block mb-1 font-medium">Password</label>
                            <input id="password" type="password" name="password" required class="w-full px-3 py-2 border rounded-sm border-[#e3e3e0] bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 dark:text-black">
                        </div>
                        
                        <div>
                            <button type="submit" class="w-full px-5 py-2 mt-4 text-white bg-sky-500 rounded-sm hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 cursor-pointer">
                                Masuk
                            </button>
                        </div>
                        <div>
                            <button class="w-full px-5 py-2 text-black bg-white rounded-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 cursor-pointer dark:bg-[#161615] dark:text-white" onclick="window.location.href='/'">
                                Kembali
                            </button>
                        </div>
                    </form>
                </div>
                <div class="relative lg:-ml-px -mb-px lg:mb-0 rounded-t-lg lg:rounded-t-none lg:rounded-r-lg aspect-[335/376] lg:aspect-auto w-full lg:w-[438px] shrink-0 overflow-hidden">
                    <div class="w-full h-full flex items-center justify-center">
                        <!-- Replace this comment with your image tag once you have the image -->
                        <img src="{{asset('img/bg-login.jpg')}}" alt="Login Image" class="w-full h-full">
                    </div>
                    <div class="absolute inset-0 rounded-t-lg lg:rounded-t-none lg:rounded-r-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"></div>
                </div>
            </main>
        </div>
    </body>

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
    </script>
</html>