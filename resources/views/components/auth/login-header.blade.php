@props([
    'title' => 'Se connecter',
])

<div class="login-header">
    <div class="flex items-center flex-col gap-2 mb-6">
        <div class="w-32 h-32 rounded-2xl flex items-center justify-center">
            <img src="{{ asset('images/anef.png') }}" style="font-size: 1.5rem;"/>
        </div>
        <div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                {{ $title }}
            </h1>
        </div>
    </div>
</div>

