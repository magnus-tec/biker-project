<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight"></h2>
    </x-slot>

    <div class="max-w-max mx-auto px-4 py-12 text-xs">
        <!-- Encabezado y búsqueda -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Compras</h2>
            <div class="flex items-center">
                <div class="flex ">
                    <label for="">Desde: </label>
                    <input type="date" name="fecha_desde" id="fecha_desde"
                    class="border border-gray-300 rounded-lg py-2 px-4 mr-2">
                <label for="">Hasta: </label>
                <input type="date" name="fecha_hasta" id="fecha_hasta"
                    class="border border-gray-300 rounded-lg py-2 px-4 mr-2">
                    <button id="btnBuscar"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 mr-5 rounded-lg">
                        Buscar
                    </button>
                </div>
            </div>
          
            @can('agregar-productos')
                <a href="{{ route('buys.create') }}"
                    class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-all duration-300" >
                    Agregar Compra
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
                            Fecha registro
                        </th>
                        <th class="px-3 py-1 text-left text-sx font-medium text-gray-500 uppercase tracking-wider">
                            Serie
                        </th>
                        <th class="px-3 py-1 text-left text-sx font-medium text-gray-500 uppercase tracking-wider">
                            Numero
                        </th>
                        <th class="px-3 py-1 text-left text-sx font-medium text-gray-500 uppercase tracking-wider ">
                            Razon Social
                        </th>
                        <th class="px-3 py-1 text-left text-sx font-medium text-gray-500 uppercase tracking-wider">
                            Detalles
                        </th>
                        <th class="px-3 py-1 text-left text-sx font-medium text-gray-500 uppercase tracking-wider">
                            PDF
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white  divide-gray-200 " id="tbody">
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
    <div id="openModalStock" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
    <div class="bg-white p-6 rounded shadow-lg w-96 relative">
        <h2 class="text-xl font-bold mb-4">Aumentar Stock</h2>

        <form action="{{ route('buy.addStock') }}" method="POST" id='formAgregarStock'>
            @csrf
            <div class="mb-4">
            <input 
                    type="hidden" 
                    name="producto_id" 
                    id="producto_id"  
                    >
                <label class="block text-gray-700 text-sm font-bold mb-2" for="cantidad">
                    Cantidad a Aumentar
                </label>
                <input 
                    type="number" 
                    name="quantity" 
                    id="quantity" 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                    >
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="cantidad">
                    Precio compra
                </label>
                <input 
                    type="number" 
                    name="price" 
                    id="price" 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                    >
            </div>
            <div class="flex justify-end">
                <button 
                    type="button" 
                    onclick="closeModalStock('openModalStock')" 
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                    Cancelar
                </button>

                <button 
                    type="submit" 
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Aumentar
                </button>
            </div>
        </form>

        <button onclick="closeModalStock('openModalStock')" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-xl">&times;</button>
    </div>
</div>
<!-- Modal -->
<div id="detalleModal"
class="fixed inset-0 bg-black bg-opacity-40 hidden flex justify-center items-center p-4 text-xs">
<div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 relative">
    <!-- Botón de Cierre -->
    <button onclick="cerrarModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-900 ">
        &times;
    </button>

    <!-- Encabezado -->
    <h2 class="text-xl font-semibold text-gray-800 text-center">Detalles de la Cotizacion</h2>

    <!-- Información General -->
    <div class="mt-4 space-y-2 text-gray-700  border-b pb-4">
        {{-- <p><strong>ID:</strong> <span id="ventaId"></span></p> --}}
        <p><strong>Cliente:</strong> <span id="ventaCliente"></span></p>
        <p><strong>DNI:</strong> <span id="ventaDni"></span></p>
        <p><strong>Comprador:</strong> <span id="comprador"></span></p>
        <p><strong>Fecha:</strong> <span id="ventaFecha"></span></p>
    </div>

    <!-- Tabla de Productos y Servicios -->
    <div class="mt-4">
        <h3 class="text-md font-semibold text-gray-700">Detalles de la Cotizacion</h3>
        <div class="overflow-x-auto">
            <table class="w-full  text-left text-gray-700 border border-gray-300 mt-2">
                <thead class="bg-gray-100 border-b border-gray-300">
                    <tr>
                        <th class="py-2 px-3 border-r border-gray-300">Descripción</th>
                        <th class="py-2 px-3 text-center border-r border-gray-300">Cantidad</th>
                        <th class="py-2 px-3 text-center border-r border-gray-300">Precio Unitario</th>
                        <th class="py-2 px-3 text-center">Total</th>
                    </tr>
                </thead>
                <tbody id="listaDetalles" class="divide-y divide-gray-300">
                    <!-- Aquí se insertarán los productos y servicios -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Total -->
    <div class="mt-4  font-semibold">
        <p>SubTotal: S/ <input type="text" id="ventaSubTotal" class="border px-2 w-20 bg-gray-200" readonly>
        </p>
        <p>IGV: S/ <input type="number" id="ventaIGV" value="0" step="0.01" class="border px-2 w-20"
                oninput="calcularSubtotal()"></p>
        <p>Total: S/ <input type="number" id="ventaTotal" value="0" step="0.01"
                class="border px-2 w-20" oninput="calcularSubtotal()"></p>
    </div>

    <!-- Botón Cerrar -->
    <div class="mt-6 text-center">
        <button onclick="cerrarModal()"
            class="bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-900 transition">
            Cerrar
        </button>
    </div>
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
        // formulario enviar stock
        const form = document.getElementById('formAgregarStock');

         if (form) {
            form.addEventListener('submit', async function(event) {
                event.preventDefault(); // Detiene el envío normal del formulario

                const productoId = document.getElementById('producto_id').value;
                const quantity = document.getElementById('quantity').value;
                const price = document.getElementById('price').value;

                const token = document.querySelector('input[name="_token"]').value;

                try {
                    const response = await fetch("{{ route('buy.addStock') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            producto_id: productoId,
                            quantity: quantity,
                            price: price
                        })
                    });

                    const data = await response.json();

                    if (response.ok) {
                        alert('✅ ' + data.message);
                        closeModalStock('openModalStock'); // Cerrar modal si todo bien
                        form.reset(); // Limpiar campos
                    } else {
                        alert('❌ Error: ' + data.message || 'Algo salió mal.');
                    }

                } catch (error) {
                    console.error('Error en el fetch:', error);
                    alert('❌ Ocurrió un error inesperado.');
                }
            });
        }
        async function generarPDF(buyId) {
            try {
                let url = `{{ route('buy.pdf', ':id') }}`.replace(':id', buyId);
                window.open(url, '_blank');
            } catch (error) {
                console.error("Error al generar el PDF:", error);
            }
        }
        function cerrarModal() {
            document.getElementById("detalleModal").classList.add("hidden");
        }
        // fin
        function closeModalImages() {
            document.getElementById("imagesModal").classList.add("hidden");
        }
        function openModalStock(modalId){
                const modal = document.getElementById(modalId);
                    if (modal) {
                        modal.classList.remove('hidden');
                    }
            }
            function closeModalStock(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
        }
    }
            function add(id)
            {
                document.getElementById("producto_id").value=id;
                openModalStock('openModalStock')
            }
            // Función para obtener los detalles de la compra
            async function verDetalles(buyId) {
            try {
                let url = `{{ route('buy.detallesBuy', ':id') }}`.replace(':id', buyId);
                let response = await fetch(url);
                let data = await response.json(); // Recibe los datos en JSON

                // Insertar datos generales de la venta
                // document.getElementById("ventaId").textContent = data.sale.id;
                document.getElementById("ventaCliente").textContent = data.buy.customer_names_surnames;
                document.getElementById("comprador").textContent = data.buy.user_register.name;
                document.getElementById("ventaDni").textContent = data.buy.customer_dni;
                document.getElementById("ventaFecha").textContent = data.buy.fecha_registro;
                // document.getElementById("ventaTotal").textContent = parseFloat(data.sale.total_price).toFixed(2);
                document.getElementById('ventaSubTotal').value = parseFloat(data.buy.total_price - data.buy.igv)
                    .toFixed(2);
                document.getElementById('ventaIGV').value = data.buy.igv;
                document.getElementById('ventaTotal').value = parseFloat(data.buy.total_price).toFixed(2);

                // Limpiar la tabla antes de agregar nuevos datos
                let listaDetalles = document.getElementById("listaDetalles");
                listaDetalles.innerHTML = "";

                // Recorrer los ítems de la venta y agregarlos a la tabla
                data.buy.buy_items.forEach(item => {
                    let fila = document.createElement("tr");
                    fila.innerHTML = `
                <td class="py-2 px-3">${item.product.description || item.item.name}</td>
                <td class="py-2 px-3 text-center">${item.quantity == null ? "1" : item.quantity}</td>
                <td class="py-2 px-3 text-center">S/ ${parseFloat(item.price).toFixed(2)}</td>
                <td class="py-2 px-3 text-center">S/ ${(item.quantity * parseFloat(item.price)).toFixed(2) == 0 ? parseFloat(item.price).toFixed(2) : (item.quantity * parseFloat(item.price)).toFixed(2)}</td>
            `;
                    listaDetalles.appendChild(fila);
                });

                // Mostrar el modal
                document.getElementById("detalleModal").classList.remove("hidden");
            } catch (error) {
                console.error("Error obteniendo los detalles:", error);
            }
        }
        document.addEventListener('DOMContentLoaded', () => {
             // calculamos la fecha actual
             let fecha_desde = document.getElementById('fecha_desde');
            let fecha_hasta = document.getElementById('fecha_hasta');

            let today = new Date();
            let year = today.getFullYear();
            let month = String(today.getMonth() + 1).padStart(2, '0'); // Meses van de 0 a 11, por eso se suma 1
            let day = String(today.getDate()).padStart(2, '0');

            let formattedDate = `${year}-${month}-${day}`;

            fecha_desde.value = formattedDate;
            fecha_hasta.value = formattedDate;

            if (fecha_desde && fecha_hasta) {
                findAll();
            }
            //fin calculo
            findAll();
            function findAll() {
                let desde = document.getElementById('fecha_desde').value;
                let hasta = document.getElementById('fecha_hasta').value;
            fetch(
                    `{{ route('buy.search') }}?fecha_desde=${encodeURIComponent(desde)}&fecha_hasta=${encodeURIComponent(hasta)}}}`
                )
                .then(response => response.json())
                .then(data => {
                    let tbody = document.getElementById('tbody');
                    tbody.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(buy => {
                            let row = document.createElement('tr');
                            row.innerHTML = `
                        <td class="px-3 py-1 whitespace-nowrap text-sm text-gray-900">${buy.fecha_registro}</td>
                        <td class="px-3 py-1 whitespace-nowrap text-sm text-gray-900">
                            ${buy.serie}
                        </td>
                        <td class="px-3 py-1 whitespace-nowrap text-sm text-gray-900">${buy.number}</td>
                        <td class="px-3 py-1 whitespace-nowrap text-sm text-gray-900">
                            ${buy.customer_names_surnames}
                        </td>
                        <td class="px-3 py-1 whitespace-nowrap text-sm text-gray-900"></td>
                        <td class="px-3 py-1 whitespace-nowrap text-sm text-gray-900"></td>
                        <td class="px-3 py-1 whitespace-nowrap text-sm text-gray-900">
                            <button class="text-white px-2 py-1 rounded"
                                onclick="verDetalles(${buy.id})"><i class="bi bi-eye-fill text-blue-500"></i></button>
                            <button class=" px-2 py-1 rounded "
                            onclick="generarPDF(${buy.id})"><i class="bi bi-filetype-pdf text-red-500"></i></button>
                        </td>
                    `;
                            tbody.appendChild(row);
                        });
                    } else {
                        tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="px-3 py-1 text-center text-gray-500">No hay registros disponibles</td>
                    </tr>
                `;
                    }
                })
            }
            btnBuscar.addEventListener('click', (e) => {
                e.preventDefault();
                findAll();

            });

           
        });
    </script>
</x-app-layout>
