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

                <!-- Card de creación -->
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white p-6">
                        <h4 class="text-2xl font-semibold">Registrar Trabajador</h4>
                        <a href="{{ url('users') }}"
                            class="bg-red-600 hover:bg-red-500 text-white py-2 px-4 rounded-md transition-all float-right">
                            Atras
                        </a>
                    </div>
                    <div class="p-6">
                        <form action="{{ url('users') }}" method="POST">
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="mb-4">
                                    <label for="name" class="block text-gray-700 font-medium">Nombres </label>
                                    <input type="text" name="name" id="name"
                                        class="w-full mt-2 p-3 border rounded-md focus:ring focus:ring-blue-300 focus:outline-none" />
                                </div>
                                <div class="mb-4">
                                    <label for="apellidos" class="block text-gray-700 font-medium">Apellidos </label>
                                    <input type="text" name="apellidos" id="apellidos"
                                        class="w-full mt-2 p-3 border rounded-md focus:ring focus:ring-blue-300 focus:outline-none" />
                                </div>
                                <div class="mb-4">
                                    <label for="apellidos" class="block text-gray-700 font-medium">DNI </label>
                                    <input type="text" name="dni" id="dni"
                                        class="w-full mt-2 p-3 border rounded-md focus:ring focus:ring-blue-300 focus:outline-none" />
                                </div>
                                <div class="mb-4">
                                    <label for="telefono" class="block text-gray-700 font-medium">Telefono </label>
                                    <input type="text" name="telefono" id="telefono"
                                        class="w-full mt-2 p-3 border rounded-md focus:ring focus:ring-blue-300 focus:outline-none" />
                                </div>
                                <div class="mb-4">
                                    <label for="direccion" class="block text-gray-700 font-medium">Direccion </label>
                                    <input type="text" name="direccion" id="direccion"
                                        class="w-full mt-2 p-3 border rounded-md focus:ring focus:ring-blue-300 focus:outline-none" />
                                </div>
                                <div class="mb-4">
                                    <label for="direccion" class="block text-gray-700 font-medium">Correo electronico
                                    </label>
                                    <input type="text" name="correo" id="correo"
                                        class="w-full mt-2 p-3 border rounded-md focus:ring focus:ring-blue-300 focus:outline-none" />
                                </div>

                                <!-- Campo Email -->
                                <div class="mb-4">
                                    <label for="email" class="block text-gray-700 font-medium"> Correo
                                        Usuario</label>
                                    <input type="text" name="email" id="email"
                                        class="w-full mt-2 p-3 border rounded-md focus:ring focus:ring-blue-300 focus:outline-none" />
                                </div>

                                <!-- Campo Contraseña -->
                                <div class="mb-4">
                                    <label for="password" class="block text-gray-700 font-medium">Contraseña</label>
                                    <input type="text" name="password" id="password"
                                        class="w-full mt-2 p-3 border rounded-md focus:ring focus:ring-blue-300 focus:outline-none" />
                                </div>

                                <!-- Campo Roles -->
                                <div class="mb-4">
                                    <label for="roles" class="block text-gray-700 font-medium">Perfiles</label>
                                    <select name="roles[]" id="roles"
                                        class="w-full mt-2 p-3 border rounded-md focus:ring focus:ring-blue-300 focus:outline-none"
                                        multiple>
                                        <option value="">Seleccione...</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role }}">{{ $role }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <!-- Botón Guardar -->
                            <div class="mt-6">
                                <button type="submit"
                                    class="w-full bg-green-600 text-white py-3 px-4 rounded-md hover:bg-green-500 transition-all">
                                    Guardar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
