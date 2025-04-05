<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight"></h2>
    </x-slot>

    <div class="max-w-min mx-auto px-4 py-12 text-xs">
        <!-- Encabezado y búsqueda -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Lista de Productos</h2>
            <div class="flex items-center">
                <div class="flex ">
                    <!-- Campo de búsqueda y botón -->
                    <select name="almacen" id="almacen" class="border border-gray-300 rounded-lg py-2 px-4 mr-2">
                        <option value="todos">Todos</option>
                        @foreach ($warehouses as $warehouse => $almacen)
                            <option value="{{ $almacen->id }}">{{ $almacen->name }}</option>
                        @endforeach
                    </select>
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
                    Exportar
                </button>
            </div>
            <button id="btnOpenImportModal"
                class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 rounded-lg">
                Importar
            </button>

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
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-5 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 ">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-1 text-left text-sx font-medium text-gray-500 uppercase tracking-wider">
                            Código
                        </th>
                        <th class="px-3 py-1 text-left text-sx font-medium text-gray-500 uppercase tracking-wider">
                            Código Barras
                        </th>
                        <th class="px-3 py-1 text-left text-sx font-medium text-gray-500 uppercase tracking-wider">
                            Imagenes
                        </th>
                        <th class="px-3 py-1 text-left text-sx font-medium text-gray-500 uppercase tracking-wider ">
                            Descripción
                        </th>
                        <th class="px-3 py-1 text-left text-sx font-medium text-gray-500 uppercase tracking-wider">
                            Model
                        </th>
                        <th class="px-3 py-1 text-left text-sx font-medium text-gray-500 uppercase tracking-wider">
                            Location
                        </th>
                        <th class="px-3 py-1 text-left text-sx font-medium text-gray-500 uppercase tracking-wider">
                            Tipo Almacén
                        </th>
                        <th class="px-3 py-1 text-left text-sx font-medium text-gray-500 uppercase tracking-wider">
                            Marca
                        </th>
                        <th class="px-3 py-1 text-left text-sx font-medium text-gray-500 uppercase tracking-wider">
                            Unidad MEDIDA
                        </th>
                        <th class="px-3 py-1 text-left text-sx font-medium text-gray-500 uppercase tracking-wider">
                            Precio
                        </th>
                        {{-- <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Estado
                        </th> --}}
                        <th class="px-3 py-1 text-left text-sx font-medium text-gray-500 uppercase tracking-wider">

                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white  divide-gray-200 " id="productsTableBody">
                </tbody>
            </table>
        </div>
        {{-- Si utilizas paginación, puedes colocar los links aquí --}}
        {{-- {{ $products->links() }} --}}
    </div>
    <!-- Modal -->
    <div id="imagesModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center hidden z-50">
        <div class="bg-white p-4 rounded-lg w-11/12 md:w-3/4 lg:w-1/2 relative">
            <!-- Botón de cierre -->
            <button class="absolute top-2 right-2 text-gray-600 hover:text-gray-900 text-xl"
                onclick="closeModalImages()">
                &times; </button>
            <!-- Swiper Container -->
            <div class="swiper mySwiper">
                <div class="swiper-wrapper" id="swiperWrapper"></div>
                <!-- Botones de navegación -->
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>
    </div>
    <!-- Modal de Importación -->
    <div id="importModal" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg w-96">
            <h2 class="text-lg font-semibold mb-4">Importar Datos</h2>

            <!-- Enlace para descargar la plantilla -->
            <a href="{{ route('plantilla.descargar') }}" class="text-blue-500 hover:underline mb-4 block">
                📥 Descargar Plantilla
            </a>

            <!-- Formulario de subida de archivo -->
            <form id="importForm" enctype="multipart/form-data">
                <input type="file" id="importFile" name="file" required>
                <button type="submit"
                    class="mt-4 bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg">Importar</button>
            </form>


            <!-- Botón para cerrar -->
            <button id="btnCloseImportModal"
                class="mt-4 bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-lg w-full">
                Cancelar
            </button>
        </div>
    </div>
    <!-- Script usando Fetch para actualizar la tabla -->
    <script>
        function openModal(productId) {
            console.log('productId', productId);

            // Obtener imágenes con AJAX
            fetch(`/productos/${productId}/imagenes`)
                .then(response => response.json())
                .then(images => {
                    console.log('images', images);

                    let swiperWrapper = document.getElementById("swiperWrapper");

                    // Asegurar que images no esté vacío
                    if (!images || images.length === 0) {
                        console.error("No se encontraron imágenes para este producto.");
                        return;
                    }

                    // Limpiar el contenido previo
                    let html = "";

                    images.forEach(img => {
                        if (img.image_path) {
                            html += `
                    <div class="swiper-slide p-4 text-center">
                        <img src="${img.image_path}" class="w-auto h-auto object-cover rounded-lg">
                    </div>`;
                        } else {
                            console.warn("Imagen sin path válido:", img);
                        }
                    });

                    swiperWrapper.innerHTML = html;

                    // Mostrar el modal
                    document.getElementById("imagesModal").classList.remove("hidden");

                    // Inicializar Swiper después de asegurarse de que las imágenes están en el DOM
                    setTimeout(() => {
                        new Swiper(".mySwiper", {
                            loop: true,
                            navigation: {
                                nextEl: ".swiper-button-next",
                                prevEl: ".swiper-button-prev",
                            },
                        });
                    }, 100);
                })
                .catch(error => console.error("Error obteniendo imágenes:", error));
        }

        function closeModalImages() {
            document.getElementById("imagesModal").classList.add("hidden");
        }
        document.addEventListener('DOMContentLoaded', () => {
            fillAllProducts();

            // Inicializar Swiper
            new Swiper(".mySwiper", {
                loop: true,
                navigation: {
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev",
                },
                autoplay: {
                    delay: 3000,
                    disableOnInteraction: false
                },
                effect: "slide",
            });


            function fillAllProducts() {
                const btnBuscar = document.getElementById('btnBuscar');
                const inputBuscar = document.getElementById('buscar');
                const tableBody = document.getElementById('productsTableBody');
                const buscarValue = inputBuscar.value;
                const almacen = document.getElementById('almacen').value;
                fetch(
                        `{{ route('products.search') }}?buscar=${encodeURIComponent(buscarValue)}&almacen=${encodeURIComponent(almacen)}`
                    )
                    .then(response => response.json())
                    .then(products => {
                        let rowsHtml = '';
                        console.log('products', products);
                        if (products.length > 0) {
                            products.forEach(product => {
                                rowsHtml += `
                                    <tr>
                                        <td class="px-3 py-1 whitespace-nowrap text-sx text-gray-900">${product.code_sku}</td>
                                        <td class="px-3 py-1 whitespace-nowrap text-sx font-medium text-gray-900">
                                            ${product.code_bar ?? ''}
                                        </td>
                                         <td class="px-3 py-1 whitespace-nowrap text-sx text-gray-900">
                            ${product.images?.length > 0 
                                ? `<img src="${product.images[0].image_path}" alt="Producto"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            class="w-20 h-20 object-cover rounded-lg cursor-pointer"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            onclick="openModal(${product.id})">`
                                : '<span class="text-gray-400">No Image</span>'}
                        </td>
                                        <td class="px-3 py-1 whitespace-nowrap text-sx font-medium text-gray-900">${product.description ?? ''}</td>
                           

                                        <td class="px-3 w-[100px] text-xs font-medium text-gray-900">
                                            <div class="w-[100px] overflow-hidden text-ellipsis whitespace-nowrap">
                                                ${product.model ?? '-'}
                                            </div>
                                            </td>
                                        <td class="px-3 py-1 whitespace-nowrap text-sx font-medium text-gray-900">${product.location ?? ''}</td>
                                        <td class="px-3 py-1 whitespace-nowrap text-sx font-medium text-gray-900">${product.warehouse?.name ?? ''}</td>
                                        <td class="px-3 py-1 whitespace-nowrap text-sx font-medium text-gray-900">${product.brand?.name ?? ''}</td>
                                        <td class="px-3 py-1 whitespace-nowrap text-sx font-medium text-gray-900">${product.unit?.name ?? ''}</td>
                                        <td class="px-3 py-1 whitespace-nowrap text-sx font-medium text-gray-900">
                                            <select class="border border-gray-300 rounded px-2 py-1">
                                                ${product.prices?.map(price => `
                                                            <option value="${price.price}">
                                                                ${
                                                                    price.type === 'buy' ? 'Precio Compra' :
                                                                    price.type === 'sucursalA' ? 'Precio Sucursal A' :
                                                                    price.type === 'sucursalB' ? 'Precio Sucursal B' :
                                                                    price.type === 'wholesale' ? 'Precio Mayorista' :
                                                                    `Precio ${price.type}`
                                                                } - ${price.price}
                                                            </option>`
                                                ).join('') || '<option value="">No hay precios disponibles</option>'}
                                            </select>
                                        </td>
                                        <td class="px-3 py-1 whitespace-nowrap text-sx font-medium">
                                            <a href="/products/${product.id}/edit" class="text-indigo-600 hover:text-indigo-900 mr-3"><i class="bi bi-pencil-square"></i> </a>
                                            <form action="/products/${product.id}" method="POST" style="display: inline;">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" class="text-red-600 hover:text-red-900"
                                                    onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?');">
                                                    <i class="bi bi-trash-fill"></i>
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
            }
            btnBuscar.addEventListener('click', (e) => {
                e.preventDefault();
                fillAllProducts();

            });
            // fin de swiper

            // Importar productos
            const btnOpenImportModal = document.getElementById('btnOpenImportModal');
            const btnCloseImportModal = document.getElementById('btnCloseImportModal');
            const importModal = document.getElementById('importModal');
            const importForm = document.getElementById('importForm');
            const importFile = document.getElementById('importFile');
            btnOpenImportModal.addEventListener('click', () => {
                importModal.classList.remove('hidden');
            });
            btnCloseImportModal.addEventListener('click', () => {
                importModal.classList.add('hidden');
            });

            importForm.addEventListener('submit', function(e) {
                e.preventDefault();

                let formData = new FormData();
                formData.append('importFile', importFile.files[0]);

                fetch("{{ route('products.import') }}", {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            const errorMessages = data.message; // data.message es un array
                            const htmlMessage = errorMessages.join('<br>');

                            Swal.fire({
                                icon: 'error',
                                title: 'Importación Fallida',
                                html: htmlMessage,
                                width: '800px' // O un valor en %, por ejemplo: '80%'

                            });
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Importación Exitosa',
                                text: data.message,
                            })
                        }

                        importModal.classList.add('hidden');
                        fillAllProducts();
                    })
                    .catch(error => console.error('Error:', error));
            });
            //fin importar
        });
        // Exportación: redirecciona a la ruta de exportación con el filtro seleccionado
        const btnExport = document.getElementById('btnExport');
        const exportFilter = document.getElementById('exportFilter');

        btnExport.addEventListener('click', (e) => {
            e.preventDefault();
            let filter = exportFilter.value;
            window.location.href = `{{ route('products.export') }}?filter=${encodeURIComponent(filter)}`;
        });
    </script>
</x-app-layout>
