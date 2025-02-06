<x-app-layout>
    <div class="container mx-auto mt-8 px-6">
        <!-- Mensaje de errores -->
        @if ($errors->any())
            <div class="bg-yellow-200 text-yellow-800 p-4 rounded-md mb-6">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Card para editar el rol -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 text-white p-6">
                <h4 class="text-2xl font-semibold">Editar Perfil: {{ $role->name }}</h4>
                <a href="{{ url('roles') }}"
                    class="bg-red-600 text-white py-2 px-6 rounded-md hover:bg-red-500 transition-all duration-300 float-right">
                    Atras
                </a>
            </div>
            <div class="p-6">
                <form action="{{ url('roles/' . $role->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Nombre del rol -->
                    <div class="mb-6">
                        <label for="name" class="block text-lg font-medium text-gray-700">Nombe de Perfil</label>
                        <input type="text" name="name" value="{{ $role->name }}"
                            class="mt-2 w-full p-3 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-gray-900" />
                    </div>

                    <!-- Botón de actualización -->
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
