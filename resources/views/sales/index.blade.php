<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        </h2>
    </x-slot>
    <div class="max-w-7xl  mx-auto px-4 py-12">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Registro de Ventas</h2>
            <form class="flex items-center text-xs" id="formBuscarPorFecha">
                <select id="document_type_id" class="border border-gray-300 rounded-lg py-2 px-4 mr-2">
                    <option value="">Todo los documentos</option>
                    @foreach ($documentTypes as $documentType)
                        <option value="{{ $documentType->id }}">
                            {{ $documentType->name }}</option>
                    @endforeach
                </select>

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
                        <th class="px-3 py-1 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nº
                        </th>
                        <th class="px-3 py-1 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Documento
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
    <div id="detalleModal" class="fixed inset-0 bg-black bg-opacity-30 hidden flex justify-center items-center p-4">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl p-5 border border-gray-300 relative">
            <!-- Botón de Cierre -->
            <button onclick="cerrarModal()"
                class="absolute top-3 right-3 text-gray-500 hover:text-gray-900 text-lg font-semibold transition">
                ✖
            </button>

            <!-- Encabezado -->
            <h2 class="text-sm font-bold text-center pb-3 uppercase tracking-wide text-gray-800 border-b">
                Detalles de la Venta
            </h2>

            <!-- Información General -->
            <div class="mt-3 p-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 text-xs space-y-1">
                <p><strong>Cliente:</strong> <span id="ventaCliente"></span></p>
                <p><strong>DNI:</strong> <span id="ventaDni"></span></p>
                <p><strong>Vendedor:</strong> <span id="ventaVendedor"></span></p>
                <p><strong>Fecha y hora:</strong> <span id="ventaFecha"></span></p>
            </div>
            <!-- Tabla de Productos y Servicios -->
            <div class="mt-4">
                <h3 class="text-xs font-semibold border-b pb-1 text-gray-700 uppercase">
                    Detalles de la Compra
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left border border-gray-300 mt-2 rounded-lg overflow-hidden">
                        <thead class="bg-gray-100 text-gray-800 uppercase text-xs">
                            <tr>
                                <th class="py-2 px-2 border-r border-gray-300">Tipo</th>
                                <th class="py-2 px-2 border-r border-gray-300">Descripción</th>
                                <th class="py-2 px-2 text-center border-r border-gray-300">Cantidad</th>
                                <th class="py-2 px-2 text-center border-r border-gray-300">Precio Unitario</th>
                                <th class="py-2 px-2 text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody id="listaDetalles" class="divide-y divide-gray-300">
                            <!-- Aquí se insertarán los productos y servicios -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Totales -->
            <div class="mt-4 flex justify-end">
                <div class="p-3 border border-gray-300 rounded-lg w-56 bg-gray-50 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="font-medium text-gray-700">SubTotal:</span>
                        <input type="text" id="ventaSubTotal"
                            class="border border-gray-400 px-2 py-1 w-20  rounded bg-white focus:outline-none" readonly>
                    </div>
                    <div class="flex justify-between items-center mt-1">
                        <span class="font-medium text-gray-700">IGV:</span>
                        <input type="number" id="ventaIGV" value="0" step="0.01"
                            class="border border-gray-400 px-2 py-1 w-20 rounded bg-white focus:outline-none"
                            oninput="calcularSubtotal()">
                    </div>
                    <div class="flex justify-between items-center text-sm font-semibold text-gray-900 mt-1">
                        <span>Total:</span>
                        <input type="number" id="ventaTotal" value="0" step="0.01"
                            class="border border-gray-500 px-2 py-1 w-20  rounded bg-white focus:outline-none"
                            oninput="calcularSubtotal()">
                    </div>
                </div>
            </div>
        </div>
    </div>








    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function finAllSales() {
            let desde = document.getElementById('fecha_desde').value;
            let hasta = document.getElementById('fecha_hasta').value;
            let document_type_id = document.getElementById('document_type_id').value;
            fetch(
                    `{{ route('sales.filtroPorfecha') }}?fecha_desde=${encodeURIComponent(desde)}&fecha_hasta=${encodeURIComponent(hasta)}&document_type_id=${encodeURIComponent(document_type_id)}`
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
                        <td class="px-3 py-1 whitespace-nowrap text-sm text-gray-900"><a href="javascript:void(0)" class="text-blue-600 hover:underline"onclick="generarPDF(${sale.id})">${sale.serie} - ${sale.number}</a></td>
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
                       
                        <td class="px-1 py-1 whitespace-nowrap text-sm text-gray-900 p">${sale.fecha_registro}</td>
                         <td class="px-1 py-1 whitespace-nowrap text-sm text-gray-900">
                            <button class="text-xl px-2 py-1 rounded"
                                onclick="verDetalles(${sale.id})"><i class="bi bi-eye-fill text-blue-500"></i></button>
                            <button class="text-xl px-2 py-1 rounded"
                        onclick="deleteSale(${sale.id})" ${sale.status_sunat == 1 ? 'disabled' : ''}><i class="bi bi-trash3-fill text-red-500"></i></button>
                        <button class="text-xl px-2 py-1 rounded" 
                        onclick="generarPDF(${sale.id})"><i class="bi bi-filetype-pdf text-red-500"></i></button>
                        <button class=" text-white px-2 py-1 rounded text-xl" ${sale.status_sunat == 1 ? 'disabled' : ''}
                        onclick="enviarSunat(${sale.id})" title="${sale.status_sunat == 1 ? 'Enviado a Sunat' : 'No enviado a Sunat'}">${sale.status_sunat == 1 ? '<i class="bi bi-send-check text-blue-500"></i>' : '<i class="bi bi-send-slash text-green-500"></i>'}</button>
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

        document.getElementById('formBuscarPorFecha').addEventListener('submit', function(event) {
            event.preventDefault();
            finAllSales();
        })
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

        // enviarSunat
        async function enviarSunat(saleId) {
            try {
                let url = `{{ route('sales.enviarSunat', ':id') }}`.replace(':id', saleId);
                let response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                let respuesta = await response.json();
                console.log(respuesta)

                if (response.ok) {
                    finAllSales();
                    Swal.fire({
                        icon: 'success',
                        title: 'Venta Enviada a la SUNAT',
                        text: 'La venta se ha enviado correctamente a la SUNAT',
                        showConfirmButton: false,
                        timer: 2000
                    })
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Al enviar la venta a la SUNAT',
                        showConfirmButton: false,
                        timer: 2000
                    })
                    console.error("Error al enviar la venta a la SUNAT");
                }
            } catch (error) {
                console.error("Error al enviar la venta a la SUNAT:", error);
            }
        }
    </script>

</x-app-layout>
