<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        </h2>
    </x-slot>
    <div class="max-w-7xl  mx-auto px-4 py-12">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Registro de Ventas</h2>
            <form class="flex items-center text-xs" id="formBuscarPorFecha">
                <label for="">Desde: </label>
                <input type="date" name="fecha_desde" id="fecha_desde"
                    class="border border-gray-300 rounded-lg py-2 px-4 mr-2">
                <label for="">Hasta: </label>
                <input type="date" name="fecha_hasta" id="fecha_hasta"
                    class="border border-gray-300 rounded-lg py-2 px-4 mr-2">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">
                    Buscar
                </button>
            </form>
            <a href="{{ route('sales.create') }}"
                class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-all duration-300">
                Agregar
            </a>
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
                            Cliente
                        </th>

                        <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            DNI Cliente
                        </th>
                        <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Vendedor
                        </th>

                        <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            SubTotal
                        </th>
                        <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            IGV
                        </th>
                        <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total
                        </th>
                        <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Fecha
                        </th>
                        <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="tbodySales">
                </tbody>
            </table>
        </div>
        <!-- Mostrar los enlaces de paginación -->
        {{-- @if ($registros instanceof \Illuminate\Pagination\LengthAwarePaginator && $registros->count() > 0)
            {{ $registros->links() }}
        @endif --}}
    </div>
    <!-- Modal -->
    <div id="detalleModal" class="fixed inset-0 bg-black bg-opacity-40 hidden flex justify-center items-center p-4">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 relative">
            <!-- Botón de Cierre -->
            <button onclick="cerrarModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-900 text-2xl">
                &times;
            </button>

            <!-- Encabezado -->
            <h2 class="text-xl font-semibold text-gray-800 text-center">Detalles de la Venta</h2>

            <!-- Información General -->
            <div class="mt-4 space-y-2 text-gray-700 text-sm border-b pb-4">
                {{-- <p><strong>ID:</strong> <span id="ventaId"></span></p> --}}
                <p><strong>Cliente:</strong> <span id="ventaCliente"></span></p>
                <p><strong>DNI:</strong> <span id="ventaDni"></span></p>
                <p><strong>Vendedor:</strong> <span id="ventaVendedor"></span></p>
                <p><strong>Fecha:</strong> <span id="ventaFecha"></span></p>
            </div>

            <!-- Tabla de Productos y Servicios -->
            <div class="mt-4">
                <h3 class="text-md font-semibold text-gray-700">Detalles de la Compra</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-700 border border-gray-300 mt-2">
                        <thead class="bg-gray-100 border-b border-gray-300">
                            <tr>
                                <th class="py-2 px-3 border-r border-gray-300">Tipo</th>
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
            <div class="mt-4 text-right text-lg font-semibold">
                <p>SubTotal: S/. <input type="text" id="ventaSubTotal" class="border px-2 w-20 bg-gray-200" readonly>
                </p>
                <p>IGV: S/. <input type="number" id="ventaIGV" value="0" step="0.01" class="border px-2 w-20"
                        oninput="calcularSubtotal()"></p>
                <p>Total: S/. <input type="number" id="ventaTotal" value="0" step="0.01"
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







    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function finAllSales() {
            let desde = document.getElementById('fecha_desde').value;
            let hasta = document.getElementById('fecha_hasta').value;
            fetch(
                    `{{ route('sales.filtroPorfecha') }}?fecha_desde=${encodeURIComponent(desde)}&fecha_hasta=${encodeURIComponent(hasta)}`
                )
                .then(response => response.json())
                .then(data => {
                    let tbody = document.getElementById('tbodySales');
                    tbody.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(sale => {
                            let row = document.createElement('tr');
                            row.innerHTML = `
                        <td class="px-3 py-1 whitespace-nowrap text-sm text-gray-900">${sale.code}</td>
                        <td class="px-3 py-1 whitespace-nowrap text-sm text-gray-900">${sale.customer_names_surnames == null ? 'Sin cliente' : sale.customer_names_surnames}</td> 
                        <td class="px-3 py-1 whitespace-nowrap text-sm text-gray-900">${sale.customer_dni}</td>
                        <td class="px-3 py-1 whitespace-nowrap text-sm text-gray-900">
                            ${sale.user_register == null ? 'Sin vendedor' : sale.user_register.name} 
                        </td>
                        <td class="px-3 py-1 whitespace-nowrap text-sm text-gray-900">
                            ${( (Number(sale.total_price) || 0) - (Number(sale.igv) || 0) ).toFixed(2)}
                        </td>

                          <td class="px-3 py-1 whitespace-nowrap text-sm text-gray-900">
                            ${sale.igv}
                        </td>
                        <td class="px-3 py-1 whitespace-nowrap text-sm text-gray-900">
                            ${sale.total_price}
                        </td>
                       
                        <td class="px-3 py-1 whitespace-nowrap text-sm text-gray-900">${sale.fecha_registro}</td>
                         <td class="px-3 py-1 whitespace-nowrap text-sm text-gray-900">
                            <button class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-700"
                                onclick="verDetalles(${sale.id})">Ver Detalles</button>
                            <button class="bg-red-500 text-white px-2 py-1 rounded hover:bg-blue-700"
                        onclick="deleteSale(${sale.id})">Eliminar</button>
                        <button class="bg-red-500 text-white px-2 py-1 rounded hover:bg-blue-700"
                        onclick="generarPDF(${sale.id})">PDF</button>
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
        // Función para obtener los detalles de la venta
        async function verDetalles(saleId) {
            try {
                let url = `{{ route('sale.detallesVenta', ':id') }}`.replace(':id', saleId);
                let response = await fetch(url);
                let data = await response.json(); // Recibe los datos en JSON

                // Insertar datos generales de la venta
                // document.getElementById("ventaId").textContent = data.sale.id;
                document.getElementById("ventaCliente").textContent = data.sale.customer_names_surnames;
                document.getElementById("ventaVendedor").textContent = data.sale.user_register.name;
                document.getElementById("ventaDni").textContent = data.sale.customer_dni;
                document.getElementById("ventaFecha").textContent = data.sale.fecha_registro;
                // document.getElementById("ventaTotal").textContent = parseFloat(data.sale.total_price).toFixed(2);
                document.getElementById('ventaSubTotal').value = parseFloat(data.sale.total_price - data.sale.igv)
                    .toFixed(2);
                document.getElementById('ventaIGV').value = data.sale.igv;
                document.getElementById('ventaTotal').value = parseFloat(data.sale.total_price).toFixed(2);

                // Limpiar la tabla antes de agregar nuevos datos
                let listaDetalles = document.getElementById("listaDetalles");
                listaDetalles.innerHTML = "";

                // Recorrer los ítems de la venta y agregarlos a la tabla
                data.sale.sale_items.forEach(item => {
                    let fila = document.createElement("tr");
                    fila.innerHTML = `
                <td class="py-2 px-3">${item.item_type.includes("Product") ? "Producto" : "Servicio"}</td>
                <td class="py-2 px-3">${item.item.description || item.item.name}</td>
                <td class="py-2 px-3 text-center">${item.quantity}</td>
                <td class="py-2 px-3 text-center">S/.${parseFloat(item.unit_price).toFixed(2)}</td>
                <td class="py-2 px-3 text-center">S/.${(item.quantity * parseFloat(item.unit_price)).toFixed(2)}</td>
            `;
                    listaDetalles.appendChild(fila);
                });

                // Mostrar el modal
                document.getElementById("detalleModal").classList.remove("hidden");
            } catch (error) {
                console.error("Error obteniendo los detalles:", error);
            }
        }
        async function deleteSale(saleId) {

            const result = await Swal.fire({
                title: '¿Estás seguro?',
                text: "No podrás revertir esta acción",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            });
            if (!result.isConfirmed) {
                return;
            }

            try {
                let url = `{{ route('sales.destroy', ':id') }}`.replace(':id', saleId);
                let response = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                if (response.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Venta Eliminada',
                        text: 'La venta se ha eliminado correctamente',
                        showConfirmButton: false,
                        timer: 2000
                    })
                    // La venta se elimino correctamente
                    finAllSales();
                } else {
                    console.error("Error al eliminar la venta");
                }
            } catch (error) {
                console.error("Error al eliminar la venta:", error);
            }
        }
        async function generarPDF(saleId) {
            try {
                let url = `{{ route('sales.pdf', ':id') }}`.replace(':id', saleId);
                window.open(url, '_blank');
            } catch (error) {
                console.error("Error al generar el PDF:", error);
            }
        }

        function cerrarModal() {
            document.getElementById("detalleModal").classList.add("hidden");
        }
        // fin de detalles
        document.addEventListener('DOMContentLoaded', () => {
            window.authUserId = @json(auth()->user()->id);
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
                finAllSales();
            }
            //fin calculo
        });
    </script>

</x-app-layout>
