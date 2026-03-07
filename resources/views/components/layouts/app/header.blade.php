<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:header container class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <a href="{{ route('denuncias.index') }}" class="ms-2 me-5 flex items-center space-x-2 rtl:space-x-reverse lg:ms-0" wire:navigate>
                <x-app-logo />
            </a>

            {{-- Navbar principal (desktop) --}}
            <flux:navbar class="-mb-px max-lg:hidden">
                <flux:navbar.item icon="map" :href="route('denuncias.index')" :current="request()->routeIs('denuncias.index')" wire:navigate>
                    {{ __('Mapa') }}
                </flux:navbar.item>

                @if(auth()->user()->hasAnyRole(['admin', 'moderador']))
                    <flux:navbar.item icon="layout-grid" :href="route('admin.dashboard')" :current="request()->routeIs('admin.dashboard')" wire:navigate>
                        {{ __('Panel Admin') }}
                    </flux:navbar.item>
                @endif
            </flux:navbar>

            <flux:spacer />

            {{-- Desktop User Menu --}}
            <flux:dropdown position="top" align="end">
                <flux:profile
                    class="cursor-pointer"
                    :initials="auth()->user()->initials()"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>
                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('denuncias.index')" icon="map" wire:navigate>{{ __('Explorar Mapa') }}</flux:menu.item>
                        @if(auth()->user()->hasAnyRole(['admin', 'moderador']))
                            <flux:menu.item :href="route('admin.dashboard')" icon="layout-grid" wire:navigate>{{ __('Panel Admin') }}</flux:menu.item>
                        @endif
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Mi Perfil') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full" data-test="logout-button">
                            {{ __('Cerrar Sesión') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{-- Mobile Sidebar --}}
        <flux:sidebar stashable sticky class="lg:hidden border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('denuncias.index') }}" class="ms-1 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navlist variant="outline">
                <flux:navlist.group :heading="__('Navegación')">
                    <flux:navlist.item icon="map" :href="route('denuncias.index')" :current="request()->routeIs('denuncias.index')" wire:navigate>
                        {{ __('Explorar Mapa') }}
                    </flux:navlist.item>

                    @if(auth()->user()->hasAnyRole(['admin', 'moderador']))
                        <flux:navlist.item icon="layout-grid" :href="route('admin.dashboard')" :current="request()->routeIs('admin.dashboard')" wire:navigate>
                            {{ __('Panel Admin') }}
                        </flux:navlist.item>
                    @endif

                    <flux:navlist.item icon="cog" :href="route('profile.edit')" :current="request()->routeIs('profile.edit')" wire:navigate>
                        {{ __('Mi Perfil') }}
                    </flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist>

            <flux:spacer />

            <flux:navlist variant="outline">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <flux:navlist.item icon="arrow-right-start-on-rectangle" as="button" type="submit" class="w-full text-red-500">
                        {{ __('Cerrar Sesión') }}
                    </flux:navlist.item>
                </form>
            </flux:navlist>
        </flux:sidebar>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
