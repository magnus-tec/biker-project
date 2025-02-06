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
        <!-- Card de usuarios -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="bg-gradient-to-r from-yellow-600 to-yellow-800 text-white p-6">
                <h4 class="text-2xl font-semibold">Ejecutivos</h4>
                @can('create-user')
                    <a href="{{ url('users/create') }}"
                        class="bg-indigo-600 text-white py-2 px-6 rounded-md hover:bg-indigo-500 transition-all duration-300 float-right">
                        Agregar
                    </a>
                @endcan
            </div>
            <div class="p-6">
                <table class="min-w-full bg-white border border-gray-300 rounded-md shadow-md">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2 text-left text-gray-600">Id</th>
                            <th class="p-2 text-left text-gray-600">Nombres y Apellidos</th>
                            <th class="p-2 text-left text-gray-600">Email</th>
                            <th class="p-2 text-left text-gray-600">Perfil</th>
                            <th class="p-2 text-left text-gray-600">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr class="border-t">
                                <td class="p-2 text-gray-800">{{ $user->id }}</td>
                                <td class="p-2 text-gray-800">{{ $user->name }}</td>
                                <td class="p-2 text-gray-800">{{ $user->email }}</td>
                                <td>
                                    @if ($user->status == 1)
                                        <span
                                            class="inline-block px-3 py-1 text-sm font-medium text-white bg-green-500 rounded-full">
                                            Activo
                                        </span>
                                    @else
                                        <span
                                            class="inline-block px-3 py-1 text-sm font-medium text-white bg-red-500 rounded-full">
                                            Inactivo
                                        </span>
                                    @endif
                                </td>

                                <td class="p-2">
                                    @if (!empty($user->getRoleNames()))
                                        @foreach ($user->getRoleNames() as $rolename)
                                            <label class="badge bg-primary mx-1">{{ $rolename }}</label>
                                        @endforeach
                                    @endif
                                </td>
                                <td class="p-1">
                                    @can('update-user')
                                        <a href="{{ url('users/' . $user->id . '/edit') }}"
                                            class="bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-500 transition-all duration-300 mx-2">
                                            Editar
                                        </a>
                                    @endcan

                                <td>
                                    @can('delete-user')
                                        <a href="{{ url('users/' . $user->id . '/delete') }}"
                                            class="{{ $user->status == 1 ? 'bg-red-600 hover:bg-red-500' : 'bg-green-600 hover:bg-green-500' }} text-white py-2 px-4 rounded-md transition-all duration-300 mx-2">
                                            {{ $user->status == 1 ? 'Desactivar' : 'Activar' }}
                                        </a>
                                    @endcan
                                </td>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
