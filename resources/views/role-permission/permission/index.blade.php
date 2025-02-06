<x-app-layout>
    <div class="container mx-auto mt-8 px-6">
        <!-- Botones de navegación -->
        <div class="mb-6">
            <a href="{{ url('roles') }}"
                class="bg-blue-600 text-white py-3 px-6 rounded-md hover:bg-blue-500 transition-all duration-300 mx-1">Perfiles</a>
            <a href="{{ url('permissions') }}"
                class="bg-green-600 text-white py-3 px-6 rounded-md hover:bg-teal-500 transition-all duration-300 mx-1">Permisos</a>
            <a href="{{ url('users') }}"
                class="bg-yellow-600 text-white py-3 px-6 rounded-md hover:bg-yellow-500 transition-all duration-300 mx-1">Ejecutivos</a>
        </div>

        <!-- Mensaje de estado -->
        @if (session('status'))
            <div class="bg-green-200 text-green-800 p-4 rounded-md mb-6">
                {{ session('status') }}
            </div>
        @endif

        <!-- Card de permisos -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="bg-gradient-to-r from-teal-600 to-teal-800 text-white p-6">
                <h4 class="text-2xl font-semibold">Permissions</h4>
                @can('create-permission')
                    <a href="{{ url('permissions/create') }}"
                        class="bg-indigo-600 text-white py-2 px-6 rounded-md hover:bg-indigo-500 transition-all duration-300 float-right">
                        Add Permission
                    </a>
                @endcan
            </div>
            <div class="p-6">
                <table class="min-w-full bg-white border border-gray-300 rounded-md shadow-md">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-1 text-left text-gray-600">Codigo</th>
                            <th class="p-1 text-left text-gray-600">Nombre</th>
                            <th class="p-1 text-left text-gray-600">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($permissions as $permission)
                            <tr class="border-t">
                                <td class="p-3 text-gray-800">{{ $permission->id }}</td>
                                <td class="p-3 text-gray-800">{{ $permission->name }}</td>
                                <td class="p-3">
                                    @can('update-permission')
                                        <a href="{{ url('permissions/' . $permission->id . '/edit') }}"
                                            class="bg-green-600 text-white py-1 px-2 rounded-md hover:bg-green-500 transition-all duration-300 mx-2">
                                            Editar
                                        </a>
                                    @endcan

                                    @can('delete-permission')
                                        <a href="{{ url('permissions/' . $permission->id . '/delete') }}"
                                            class="bg-red-600 text-white py-1 px-2 rounded-md hover:bg-red-500 transition-all duration-300 mx-2">
                                            Eliminar
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
