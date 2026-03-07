@props(['title', 'description'])

<div class="flex w-full flex-col mb-2">
    <h2 class="text-2xl lg:text-2xl font-black text-white lg:text-slate-900 tracking-tight">{{ $title }}</h2>
    <p class="text-sm text-sky-200/70 lg:text-slate-500 font-medium mt-1">{{ $description }}</p>
</div>
