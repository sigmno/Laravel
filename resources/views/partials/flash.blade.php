@if (session('success'))
    <div class="flash success">{{ session('success') }}</div>
@endif

@if (session('error'))
    <div class="flash error">{{ session('error') }}</div>
@endif

@if (session('status'))
    <div class="flash success">{{ session('status') }}</div>
@endif
