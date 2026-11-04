@php
    $toasts = [];
    if (session('success')) {
        $toasts[] = ['type' => 'success', 'title' => 'Berhasil', 'msgs' => [session('success')]];
    }
    if (session('error')) {
        $toasts[] = ['type' => 'error', 'title' => 'Gagal', 'msgs' => [session('error')]];
    }
    if ($errors->any()) {
        $toasts[] = ['type' => 'error', 'title' => 'Gagal', 'msgs' => array_slice($errors->all(), 0, 3)];
    }
@endphp

@if ($toasts)
<div class="arah-toasts" role="status" aria-live="polite">
    @foreach ($toasts as $t)
        <div class="arah-toast {{ $t['type'] }}">
            <span class="arah-toast-ico">
                @if ($t['type'] === 'success')
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>
                @else
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7.5v5M12 16.5h.01"/></svg>
                @endif
            </span>
            <div class="arah-toast-body">
                <b>{{ $t['title'] }}</b>
                @foreach ($t['msgs'] as $m)
                    <p>{{ $m }}</p>
                @endforeach
            </div>
            <button type="button" class="arah-toast-x" aria-label="Tutup">&times;</button>
        </div>
    @endforeach
</div>

<style>
    .arah-toasts { position: fixed; top: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px; width: min(360px, calc(100vw - 24px)); font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; }
    .arah-toast { display: flex; align-items: flex-start; gap: 12px; padding: 14px 12px 14px 14px; border-radius: 14px; background: #fff; color: #1e293b; border-left: 5px solid; box-shadow: 0 14px 34px rgba(8, 20, 70, .28); animation: toast-in .35s ease both; transition: opacity .3s, transform .3s; }
    .arah-toast.success { border-color: #10b981; }
    .arah-toast.error { border-color: #ef4444; }
    .arah-toast.hide { opacity: 0; transform: translateX(24px); }
    .arah-toast-ico { width: 32px; height: 32px; flex-shrink: 0; display: grid; place-items: center; border-radius: 10px; }
    .arah-toast.success .arah-toast-ico { background: #d1fae5; color: #047857; }
    .arah-toast.error .arah-toast-ico { background: #fee2e2; color: #b91c1c; }
    .arah-toast-body { flex: 1; font-size: 12.5px; line-height: 1.5; }
    .arah-toast-body b { display: block; margin-bottom: 2px; font-size: 13px; color: #0f172a; }
    .arah-toast-body p { margin: 0; }
    .arah-toast-x { border: 0; background: none; padding: 0 4px; color: #94a3b8; font-size: 20px; line-height: 1; cursor: pointer; }
    .arah-toast-x:hover { color: #475569; }
    @keyframes toast-in { from { opacity: 0; transform: translateX(24px); } to { opacity: 1; transform: none; } }
</style>

<script>
    document.querySelectorAll('.arah-toast').forEach(function (t) {
        function close() { t.classList.add('hide'); setTimeout(function () { t.remove(); }, 300); }
        t.querySelector('.arah-toast-x').addEventListener('click', close);
        setTimeout(close, t.classList.contains('error') ? 7000 : 5000);
    });
</script>
@endif