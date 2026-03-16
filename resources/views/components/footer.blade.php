<footer class="mt-auto bg-white border-top py-3 shadow-sm">
    <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 text-muted small">
            <span>© {{ now()->year }} {{ config('app.name') }} v{{ config('app.version') }}.</span>
            <span class="badge text-bg-light border">Licence : {{ config('app.license') }}</span>

        </div>
    </div>
</footer>
