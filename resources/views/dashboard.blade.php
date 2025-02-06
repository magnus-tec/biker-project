<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight ">
            {{-- {{ __('Dashboard') }} --}}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}

                    {{-- Verificación de rol --}}
                    @if (auth()->user()->hasRole('admin'))
                        <div class="mt-4">
                            <h3 class="text-xl font-semibold">Admin Content</h3>
                            <p>Bienvenido, admin. Aquí puedes gestionar roles, permisos y usuarios.</p>
                        </div>
                    @elseif(auth()->user()->hasRole('user'))
                        <div class="mt-4">
                            <h3 class="text-xl font-semibold">User Content</h3>
                            <p>Bienvenido, usuario. Aquí puedes ver tu perfil y estado de cuenta.</p>
                        </div>
                    @else
                        <div class="mt-4">
                            <h3 class="text-xl font-semibold">Restricted Access</h3>
                            <p>No tienes los permisos necesarios para acceder a esta sección.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
