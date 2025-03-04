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
                {{-- @can('filtrar-por-trabajador-servicios')
                    <select name="mechanic" id="mechanic" class="border border-gray-300 rounded-lg py-2 px-4 mr-2">
                        <option value="todos">Todos</option>
                        @foreach ($mechanics as $mechanic)
                            <option value="{{ $mechanic->id }}">{{ $mechanic->name }} {{ $mechanic->apellidos }}</option>
                        @endforeach
                    </select>
                @endcan --}}
                {{-- @can('filtrar-por-estado-servicios')
                    <div>
                        <label for="">Estado: </label>
                        <select name="estado-filtro" id="estado-filtro" class="border border-gray-300 rounded-lg py-2 px-4">
                            <option value="">Todos</option>
                            <option value="0">Pendiente</option>
                            <option value="1">Completo</option>
                            <option value="2">En proceso</option>
                        </select>
                    </div>
                @endcan --}}
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
                            ${sale.total_price}
                        </td>
                        <td class="px-3 py-1 whitespace-nowrap text-sm text-gray-900">${sale.fecha_registro}</td>
                         <td class="px-3 py-1 whitespace-nowrap text-sm text-gray-900">
                            <button class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-700"
                                onclick="verDetalles(${sale.id})">Ver Detalles</button>
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
