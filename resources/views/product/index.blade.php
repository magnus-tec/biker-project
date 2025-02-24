<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight"></h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 py-12">
        <!-- Encabezado y búsqueda -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Lista de Productos</h2>
            <div class="mb-4">
                <div class="flex">
                    <!-- Campo de búsqueda y botón -->
                    <input type="text" id="buscar" name="buscar" placeholder="Buscar productos..."
                        value="{{ request('buscar') }}" class="border border-gray-300 rounded-lg py-2 px-4 mr-2">
                    <button id="btnBuscar"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">
                        Buscar
                    </button>
                </div>
            </div>
            <div class="flex items-center">
                <select id="exportFilter" name="exportFilter" class="border border-gray-300 rounded-lg py-2 px-4 mr-2">
                    <option value="productos">Exportar por Productos</option>
                    <option value="stock_minimo">Exportar por Stock Mínimo</option>
                    <option value="precio">Exportar por Precio</option>
                </select>
                <button id="btnExport"
                    class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 rounded-lg">
                    Exportar Excel
                </button>
            </div>

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
                            Código
                        </th>
                        <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Código de Barras
                        </th>
                        <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Descripción
                        </th>
                        <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Model
                        </th>
                        <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Location
                        </th>
                        <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tipo Almacén
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
                <tbody class="bg-white divide-y divide-gray-200" id="productsTableBody">
                    <!-- Aquí se cargarán las filas de la tabla -->
                    @forelse($products as $product)
                        <tr>
                            <td class="px-3 py-1 whitespace-nowrap text-sm text-gray-900">
                                {{ $product->code }}
                            </td>
                            <td class="px-3 py-1 whitespace-nowrap text-sm font-medium text-gray-900">
                                @if ($product->bar_code)
                                    <img src="{{ asset($product->bar_code) }}" alt="Producto"
                                        class="w-16 h-16 object-contain border rounded-lg cursor-pointer product-image">
                                @else
                                    <span class="text-gray-500">Sin imagen</span>
                                @endif
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
                                <button type="button" id="btn-{{ $product->id }}"
                                    class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full shadow-md {{ $product->status == 0 ? 'bg-green-200 text-green-700' : 'bg-red-200 text-red-700' }}"
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
                            <td colspan="10" class="px-3 py-1 text-center text-gray-500">
                                No hay registros disponibles
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Si utilizas paginación, puedes colocar los links aquí --}}
        {{-- {{ $products->links() }} --}}
    </div>
    <div id="imageModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-75 hidden z-50">
        <div class="relative">
            <button id="closeModal" class="absolute top-0 right-0 m-4 text-black text-3xl font-bold">&times;</button>
            <img id="modalImage" src="" alt="Imagen Ampliada" class="max-w-sm max-h-screen rounded-lg">
        </div>
    </div>
    <!-- Script usando Fetch para actualizar la tabla -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btnBuscar = document.getElementById('btnBuscar');
            const inputBuscar = document.getElementById('buscar');
            const tableBody = document.getElementById('productsTableBody');

            btnBuscar.addEventListener('click', (e) => {
                e.preventDefault();
                const buscarValue = inputBuscar.value;

                fetch(`{{ route('products.search') }}?buscar=${encodeURIComponent(buscarValue)}`)
                    .then(response => response.json())
                    .then(products => {
                        let rowsHtml = '';
                        console.log('products', products);
                        if (products.length > 0) {
                            products.forEach(product => {
                                rowsHtml += `
                                    <tr>
                                        <td class="px-3 py-1 whitespace-nowrap text-sm text-gray-900">${product.code}</td>
                                        <td class="px-3 py-1 whitespace-nowrap text-sm font-medium text-gray-900">
                                            ${product.bar_code 
                                                ? `<img src="{{ asset($product->bar_code) }}" alt="Producto" class="w-16 h-16 object-contain border rounded-lg cursor-pointer product-image">
                            ` 
                                                : '<span class="text-gray-500">Sin imagen</span>'}
                                        </td>
                                        <td class="px-3 py-1 whitespace-nowrap text-sm font-medium text-gray-900">${product.description ?? ''}</td>
                                        <td class="px-3 py-1 whitespace-nowrap text-sm font-medium text-gray-900">${product.model ?? ''}</td>
                                        <td class="px-3 py-1 whitespace-nowrap text-sm font-medium text-gray-900">${product.location ?? ''}</td>
                                        <td class="px-3 py-1 whitespace-nowrap text-sm font-medium text-gray-900">${product.warehouse?.name ?? ''}</td>
                                        <td class="px-3 py-1 whitespace-nowrap text-sm font-medium text-gray-900">${product.brand?.name ?? ''}</td>
                                        <td class="px-3 py-1 whitespace-nowrap text-sm font-medium text-gray-900">${product.unit?.name ?? ''}</td>
                                        <td class="px-3 py-1 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <button type="button" id="btn-${product.id}"
                                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full shadow-md ${product.status == 0 ? 'bg-green-200 text-green-700' : 'bg-red-200 text-red-700'}"
                                                onclick="confirmDelete(${product.id}, '${product.status == 0 ? '¿Está seguro de desactivar este registro?' : '¿Está seguro de activar este registro?'}')">
                                                ${product.status == 1 ? 'Activado' : 'Deshabilitado'}
                                            </button>
                                        </td>
                                        <td class="px-3 py-1 whitespace-nowrap text-sm font-medium">
                                            <a href="/products/${product.id}/edit" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</a>
                                            <form action="/products/${product.id}" method="POST" style="display: inline;">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" class="text-red-600 hover:text-red-900"
                                                    onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?');">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                `;
                            });
                        } else {
                            rowsHtml = `
                                <tr>
                                    <td colspan="10" class="px-3 py-1 text-center text-gray-500">No hay registros disponibles</td>
                                </tr>
                            `;
                        }

                        tableBody.innerHTML = rowsHtml;
                    })
                    .catch(error => console.error('Error en la búsqueda:', error));
            });
        });
        // Exportación: redirecciona a la ruta de exportación con el filtro seleccionado
        const btnExport = document.getElementById('btnExport');
        const exportFilter = document.getElementById('exportFilter');

        btnExport.addEventListener('click', (e) => {
            e.preventDefault();
            let filter = exportFilter.value;
            // Redirige a la ruta de exportación con el parámetro filter
            window.location.href = `{{ route('products.export') }}?filter=${encodeURIComponent(filter)}`;
        });
        // Función para abrir el modal con la imagen clickeada
        function openImageModal(src) {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            modalImage.src = src;
            modal.classList.remove('hidden');
        }
        document.querySelectorAll('.product-image').forEach(image => {
            image.addEventListener('click', () => {
                openImageModal(image.src);
            });
        });

        // Cerrar el modal al hacer click en el botón de cerrar
        document.getElementById('closeModal').addEventListener('click', () => {
            document.getElementById('imageModal').classList.add('hidden');
        });

        // Opcional: Cerrar el modal al hacer click fuera de la imagen
        document.getElementById('imageModal').addEventListener('click', (e) => {
            if (e.target.id === 'imageModal') {
                e.target.classList.add('hidden');
            }
        });
    </script>
</x-app-layout>
