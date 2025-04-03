<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        </h2>
    </x-slot>
    <div class="max-w-7xl  mx-auto px-4 py-12">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Registro de Cotizaciones</h2>
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
            <a href="{{ route('quotations.create') }}"
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
                            Item
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
                            Mecanico
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
                <tbody class="bg-white divide-y divide-gray-200" id="tbodyQuotations">
                </tbody>
            </table>
        </div>
        <!-- Mostrar los enlaces de paginación -->
        {{-- @if ($registros instanceof \Illuminate\Pagination\LengthAwarePaginator && $registros->count() > 0)
            {{ $registros->links() }}
        @endif --}}
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
                <p><strong>Vendedor:</strong> <span id="ventaVendedor"></span></p>
                <p><strong>Fecha:</strong> <span id="ventaFecha"></span></p>
            </div>

            <!-- Tabla de Productos y Servicios -->
            <div class="mt-4">
                <h3 class="text-md font-semibold text-gray-700">Detalles de la Cotizacion</h3>
                <div class="overflow-x-auto">
                    <table class="w-full  text-left text-gray-700 border border-gray-300 mt-2">
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        async function venderQuotation(quotationId) {

            try {
                const response = await fetch(`{{ route('quotations.vender', ':id') }}`.replace(':id', quotationId), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                if (!response.ok) {
                    throw new Error('Error al intentar vender la cotización');

                }
                Swal.fire({
                    icon: 'success',
                    title: 'Venta Realizada',
                    text: response.success,
                    showConfirmButton: false,
                    timer: 2000
                })
                finAllQuotations();
            } catch (error) {
                console.error('Error:', error);
            }
        }

        document.getElementById('formBuscarPorFecha').addEventListener('submit', function(event) {
            event.preventDefault();
            finAllQuotations();
        })

        function finAllQuotations() {
            let desde = document.getElementById('fecha_desde').value;
            let hasta = document.getElementById('fecha_hasta').value;
            fetch(
                    `{{ route('quotations.filtroPorfecha') }}?fecha_desde=${encodeURIComponent(desde)}&fecha_hasta=${encodeURIComponent(hasta)}`
                )
                .then(response => response.json())
                .then(data => {
                    let tbody = document.getElementById('tbodyQuotations');
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
                            ${sale.mechanic == null ? 'Sin mecanico' : sale.mechanic.name} 
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
                            <button class="text-white px-2 py-1 rounded"
                                onclick="verDetalles(${sale.id})"><i class="bi bi-eye-fill text-blue-500"></i></button>
                                <button class=" px-2 py-1 rounded"
                                onclick="editQuotation(${sale.id})"  ${sale.status_sale == '0' ? '' : 'disabled'}><i class="bi bi-pencil-square  ${sale.status_sale == '0' ? 'text-yellow-500' : 'text-blue-500'}"></i></button>
                            <button class=" px-2 py-1 rounded "
                        onclick="deleteQuotation(${sale.id})"><i class="bi bi-trash3-fill text-red-500"></i></button>
                        <button class=" px-2 py-1 rounded "
                        onclick="generarPDF(${sale.id})"><i class="bi bi-filetype-pdf text-red-500"></i></button>
                        <button onclick="venderQuotation(${sale.id})" class="px-2 py-1 rounded "${sale.status_sale == '0' ? '' : 'disabled'}>
                            ${sale.status_sale == '0' ? '<i class="bi bi-cart-check text-yellow-500"></i>' : '<i class="bi bi-cart-x text-blue-500"></i>'} 
                        </button>
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
        async function verDetalles(quotationId) {
            try {
                let url = `{{ route('quotations.detallesQuotation', ':id') }}`.replace(':id', quotationId);
                let response = await fetch(url);
                let data = await response.json(); // Recibe los datos en JSON

                // Insertar datos generales de la venta
                // document.getElementById("ventaId").textContent = data.sale.id;
                document.getElementById("ventaCliente").textContent = data.quotation.customer_names_surnames;
                document.getElementById("ventaVendedor").textContent = data.quotation.user_register.name;
                document.getElementById("ventaDni").textContent = data.quotation.customer_dni;
                document.getElementById("ventaFecha").textContent = data.quotation.fecha_registro;
                // document.getElementById("ventaTotal").textContent = parseFloat(data.sale.total_price).toFixed(2);
                document.getElementById('ventaSubTotal').value = parseFloat(data.quotation.total_price - data.quotation
                        .igv)
                    .toFixed(2);
                document.getElementById('ventaIGV').value = data.quotation.igv;
                document.getElementById('ventaTotal').value = parseFloat(data.quotation.total_price).toFixed(2);

                // Limpiar la tabla antes de agregar nuevos datos
                let listaDetalles = document.getElementById("listaDetalles");
                listaDetalles.innerHTML = "";

                // Recorrer los ítems de la venta y agregarlos a la tabla
                data.quotation.quotation_items.forEach(item => {
                    let fila = document.createElement("tr");
                    fila.innerHTML = `
                <td class="py-2 px-3">${item.item_type.includes("Product") ? "Producto" : "Servicio"}</td>
                <td class="py-2 px-3">${item.item.description || item.item.name}</td>
                <td class="py-2 px-3 text-center">${item.quantity == null ? "1" : item.quantity}</td>
                <td class="py-2 px-3 text-center">S/ ${parseFloat(item.unit_price).toFixed(2)}</td>
                <td class="py-2 px-3 text-center">S/ ${(item.quantity * parseFloat(item.unit_price)).toFixed(2) == 0 ? parseFloat(item.unit_price).toFixed(2) : (item.quantity * parseFloat(item.unit_price)).toFixed(2)}</td>
            `;
                    listaDetalles.appendChild(fila);
                });

                // Mostrar el modal
                document.getElementById("detalleModal").classList.remove("hidden");
            } catch (error) {
                console.error("Error obteniendo los detalles:", error);
            }
        }
        async function editQuotation(quotationId) {
            let url = `{{ route('quotations.edit', ':id') }}`.replace(':id', quotationId);
            window.location.href = url;
        }
        async function deleteQuotation(quotationId) {

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
                let url = `{{ route('quotations.destroy', ':id') }}`.replace(':id', quotationId);
                let response = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                if (response.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Cotizacion Eliminada',
                        text: 'La cotizacion se ha eliminado correctamente',
                        showConfirmButton: false,
                        timer: 2000
                    })
                    // La venta se elimino correctamente
                    finAllQuotations();
                } else {
                    console.error("Error al eliminar la venta");
                }
            } catch (error) {
                console.error("Error al eliminar la venta:", error);
            }
        }
        async function generarPDF(quotationId) {
            try {
                let url = `{{ route('quotations.pdf', ':id') }}`.replace(':id', quotationId);
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
                finAllQuotations();
            }
            //fin calculo
        });
    </script>

</x-app-layout>
