<div
    x-data="{
        now: '',
        init() {
            this.tick();
            setInterval(() => this.tick(), 1000);
        },
        tick() {
            this.now = new Date().toLocaleString('en-US', {
                weekday: 'short',
                month: 'short',
                day: 'numeric',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        }
    }"
    x-init="init()"
    x-cloak
    style="color:white;font-size:0.8rem;display:flex;align-items:center;gap:6px;padding:0 16px;white-space:nowrap;font-weight:500;"
>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:15px;height:15px;flex-shrink:0;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
    </svg>
    <span x-text="now" style="min-width:220px;"></span>
</div>  <?php /**PATH /home/u476045238/domains/gvcfiguidancesc.com/public_html/resources/views/filament/topbar-clock.blade.php ENDPATH**/ ?>