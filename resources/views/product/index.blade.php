<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        </h2>
    </x-slot>

    <div class="max-w-7xl  mx-auto px-4 py-12">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Lista de Productos</h2>
            @can('agregar-productos')
                <a href="{{ route('products.create') }}"
                    class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-all duration-300">
                    Agregar
                </a>
            @endcan
        </div>

        <!-- Mensajes de éxito o error -->
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- Tabla de registros -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-5">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Codigo
                        </th>
                        <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Descripcion
                        </th>
                        <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Model
                        </th>
                        <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Location
                        </th>
                        <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tipo Almacen
                        </th>
                        <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Marca
                        </th>
                        <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Unidad MEDIDA
                        </th>

                        <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($products as $product)
                        <tr>
                            <td class="px-3 py-1 whitespace-nowrap text-sm text-gray-900">
                                {{ $product->code }}
                            </td>
                            <td class="px-3 py-1 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $product->description }}
                            </td>

                            <td class="px-3 py-1 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $product->model }}
                            </td>
                            <td class="px-3 py-1 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $product->location }}
                            </td>
                            <td class="px-3 py-1 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $product->warehouse->name }}
                            </td>
                            <td class="px-3 py-1 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $product->brand->name }}
                            </td>
                            <td class="px-3 py-1 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $product->unit->name }}
                            </td>
                            <td class="px-3 py-1 whitespace-nowrap text-sm font-medium text-gray-900">
                                <button type="button" id ="btn-{{ $product->id }}"
                                    class=" px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full shadow-md {{ $product->status == 0 ? 'bg-green-200 text-green-700' : 'text-red-700  bg-red-200' }}"
                                    onclick="confirmDelete({{ $product->id }}, '{{ $product->status == 0 ? '¿Está seguro de desactivar este registro?' : '¿Está seguro de activar este registro?' }}')">
                                    @if ($product->status == 1)
                                        Activado
                                    @else
                                        Deshabilitado
                                    @endif
                                </button>
                            </td>
                            <td class="px-3 py-1 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('products.edit', $product->id) }}"
                                    class="text-indigo-600 hover:text-indigo-900 mr-3">
                                    Editar
                                </a>

                                <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                    style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?');">
                                        Eliminar
                                    </button>
                                </form>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-3 py-1 text-center text-gray-500">
                                No hay registros disponibles
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Mostrar los enlaces de paginación -->
        {{-- @if ($registros instanceof \Illuminate\Pagination\LengthAwarePaginator && $registros->count() > 0)
            {{ $registros->links() }}
        @endif --}}
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


</x-app-layout>
