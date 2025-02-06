<x-app-layout>
    <div class="container mx-auto mt-8 px-6">
        <!-- Mensaje de éxito -->
        @if (session('status'))
            <div class="bg-green-200 text-green-800 p-4 rounded-md mb-6">
                {{ session('status') }}
            </div>
        @endif

        <!-- Card para los permisos del rol -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 text-white p-6">
                <h4 class="text-2xl font-semibold">Perfil: {{ $role->name }}</h4>
                <a href="{{ url('roles') }}"
                    class="bg-red-600 text-white py-2 px-6 rounded-md hover:bg-red-500 transition-all duration-300 float-right">
                    Atras
                </a>
            </div>
            <div class="p-6">
                <form action="{{ url('roles/' . $role->id . '/give-permissions') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Mensaje de error para los permisos -->
                    <div class="mb-4">
                        @error('permission')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Permisos -->
                    <div class="mb-6">
                        <label for="permissions" class="block text-lg font-medium text-gray-700">Permisos</label>

                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mt-4">
                            @foreach ($permissions as $permission)
                                <div class="flex items-center">
                                    <input type="checkbox" name="permission[]" value="{{ $permission->name }}"
                                        {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}
                                        class="mr-2 w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" />
                                    <label for="permission"
                                        class="text-sm text-gray-700">{{ $permission->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Botón de Actualización -->
                    <div class="mb-4">
                        <button type="submit"
                            class="w-full bg-indigo-600 text-white py-3 px-6 rounded-md hover:bg-indigo-500 transition-all duration-300">
                            Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
