<div class="container mx-auto px-6 md:px-25">
    <div class="flex flex-col md:flex-row justify-between items-center">
        <p class="text-sm md:text-base">
            <span id="current-year"></span> | Fauzan Ramadhan Aisfa
        </p>
        <div class="mt-2 md:mt-0 text-sm">
            <span id="current-time"></span>
        </div>
    </div>
</div>

<script>
    document.getElementById('current-year').textContent = new Date().getFullYear();
    
    function updateClock() {
        const currentDate = new Date();
        const hours = String(currentDate.getHours()).padStart(2, '0');
        const minutes = String(currentDate.getMinutes()).padStart(2, '0');
        const seconds = String(currentDate.getSeconds()).padStart(2, '0');
        
        document.getElementById('current-time').textContent = `${hours}:${minutes}:${seconds}`;
    }

    setInterval(updateClock, 1000);

    updateClock();
</script>
