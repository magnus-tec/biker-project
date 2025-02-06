<x-app-layout>
    <div class="container mx-auto mt-5 px-6">
        <div class="flex space-x-4 mb-6">
            <a href="{{ url('roles') }}"
                class="bg-indigo-600 text-white py-3 px-6 rounded-md shadow-lg hover:bg-indigo-500 transition-all duration-300">Perfiles</a>
            <a href="{{ route('permissions.index') }}"
                class="bg-green-600 text-white py-3 px-6 rounded-md shadow-lg hover:bg-teal-500 transition-all duration-300">Permisos</a>
            <a href="{{ url('users') }}"
                class="bg-yellow-600 text-white py-3 px-6 rounded-md shadow-lg hover:bg-yellow-500 transition-all duration-300">Ejecutivos</a>
        </div>

        <div class="max-w-7xl mx-auto mt-8">
            <div class="bg-white shadow-2xl rounded-xl overflow-hidden">

                @if (session('status'))
                    <div class="bg-green-500 text-white p-4 rounded-md mb-6">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="bg-gradient-to-r from-gray-700 to-gray-800 text-white px-6 py-4">
                    <h4 class="text-2xl font-bold">Roles</h4>
                    @can('create-role')
                        <a href="{{ url('roles/create') }}"
                            class="bg-blue-600 text-white py-2 px-5 rounded-md hover:bg-blue-500 transition-all duration-300 float-right">
                            Agregar Perfil
                        </a>
                    @endcan
                </div>

                <div class="p-6">
                    <table class="min-w-full table-auto text-sm text-gray-600">
                        <thead class="bg-gray-100 text-gray-500 uppercase">
                            <tr>
                                <th class="px-6 py-3 text-left border-b">Codigo</th>
                                <th class="px-6 py-3 text-left border-b">Nombre</th>
                                <th class="px-6 py-3 text-left border-b" style="width: 40%;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $role)
                                <tr class="hover:bg-gray-50 transition duration-200">
                                    <td class="px-6 py-4 border-b">{{ $role->id }}</td>
                                    <td class="px-6 py-4 border-b">{{ $role->name }}</td>
                                    <td class="px-6 py-4 border-b">
                                        <a href="{{ url('roles/' . $role->id . '/give-permissions') }}"
                                            class="bg-yellow-500 text-white py-2 px-5 rounded-md hover:bg-yellow-400 transition-all duration-300">
                                            Editar Permisos
                                        </a>

                                        @can('update-role')
                                            <a href="{{ url('roles/' . $role->id . '/edit') }}"
                                                class="bg-green-600 text-white py-2 px-5 rounded-md hover:bg-green-500 transition-all duration-300 mx-2">
                                                Editar
                                            </a>
                                        @endcan

                                        @can('delete-role')
                                            <a href="{{ url('roles/' . $role->id . '/delete') }}"
                                                class="bg-red-600 text-white py-2 px-5 rounded-md hover:bg-red-500 transition-all duration-300">
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
    </div>
</x-app-layout>
