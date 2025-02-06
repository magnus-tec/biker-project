<x-app-layout>
    <div class="container mx-auto mt-8 px-6">
        <div class="row justify-center">
            <div class="col-md-8">
                <!-- Mensajes de error -->
                @if ($errors->any())
                    <div class="bg-yellow-100 text-yellow-700 p-4 rounded-md mb-6 shadow-md">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Card de edición -->
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white p-6">
                        <h4 class="text-2xl font-semibold">Editar Permiso</h4>
                        <a href="{{ url('permissions') }}"
                            class="bg-red-600 hover:bg-red-500 text-white py-2 px-4 rounded-md transition-all float-right">
                            Atras
                        </a>
                    </div>
                    <div class="p-6">
                        <form action="{{ url('permissions/' . $permission->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Campo Nombre del Permiso -->
                            <div class="mb-4">
                                <label for="name" class="block text-gray-700 font-medium">Nombre</label>
                                <input type="text" name="name" id="name" value="{{ $permission->name }}"
                                    class="w-full mt-2 p-3 border rounded-md focus:ring focus:ring-blue-300 focus:outline-none" />
                            </div>

                            <!-- Botón Actualizar -->
                            <div class="mt-6">
                                <button type="submit"
                                    class="w-full bg-blue-600 text-white py-3 px-4 rounded-md hover:bg-blue-500 transition-all">
                                    Actualizae
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
