<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{-- Registro de Socios --}}
        </h2>
    </x-slot>

    <div class="max-w-7xl  mx-auto px-4 py-12">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Registro de Servicios</h2>
            <form class="flex items-center text-xs" id="formBuscarPorFecha">
                <label for="">Desde: </label>
                <input type="date" name="fecha_desde" id="fecha_desde"
                    class="border border-gray-300 rounded-lg py-2 px-4 mr-2">
                <label for="">Hasta: </label>
                <input type="date" name="fecha_hasta" id="fecha_hasta"
                    class="border border-gray-300 rounded-lg py-2 px-4 mr-2">
                <select name="mechanic" id="mechanic" class="border border-gray-300 rounded-lg py-2 px-4 mr-2">
                    <option value="todos">Todos</option>
                    @foreach ($mechanics as $mechanic)
                        <option value="{{ $mechanic->id }}">{{ $mechanic->nombres }} {{ $mechanic->apellido_paterno }}
                            {{ $mechanic->apellido_materno }}</option>
                    @endforeach
                </select>
                <div>
                    <label for="">Estado: </label>
                    <select name="estado" id="estado" class="border border-gray-300 rounded-lg py-2 px-4">
                        <option value="">Todos</option>
                        <option value="0">Pendiente</option>
                        <option value="1">Completo</option>
                        <option value="2">En proceso</option>
                    </select>
                </div>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">
                    Buscar
                </button>


            </form>
            <a href="{{ route('services.create') }}"
                class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-all duration-300">
                Agregar
            </a>
            {{-- @endcan --}}
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
                            Nº Codigo
                        </th>
                        <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Conductor
                        </th>
                        <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Placa
                        </th>
                        <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Mecanico
                        </th>
                        <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Descripcion
                        </th>
                        <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Fecha
                        </th>
                        <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="tbodyServices">
                </tbody>
            </table>
        </div>
        <!-- Mostrar los enlaces de paginación -->
        @if ($servicios instanceof \Illuminate\Pagination\LengthAwarePaginator && $registros->count() > 0)
            {{ $servicios->links() }}
        @endif
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function finAllServices() {
            let desde = document.getElementById('fecha_desde').value;
            let hasta = document.getElementById('fecha_hasta').value;
            let mechanicElement = document.getElementById('mechanic');
            let mechanic = mechanicElement ? mechanicElement.value : '';
            let estadoElement = document.getElementById('estado');
            let estado = estadoElement ? estadoElement.value : '';
            fetch(
                    `{{ route('service.filtroPorfecha') }}?fecha_desde=${encodeURIComponent(desde)}&fecha_hasta=${encodeURIComponent(hasta)}&mechanic=${encodeURIComponent(mechanic)}&estado=${encodeURIComponent(estado)}`
                )
                .then(response => response.json())
                .then(data => {
                    let tbody = document.getElementById('tbodyServices');
                    tbody.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(servicio => {
                            let row = document.createElement('tr');
                            row.innerHTML = `
                        <td class="px-3 py-1 whitespace-nowrap text-sm text-gray-900">${servicio.codigo}</td>
                        <td class="px-3 py-1 whitespace-nowrap text-sm text-gray-900">
                            ${servicio.drive.nombres} ${servicio.drive.apellido_paterno} ${servicio.drive.apellido_materno}
                        </td>
                        <td class="px-3 py-1 whitespace-nowrap text-sm text-gray-900">${servicio.car.placa}</td>
                        <td class="px-3 py-1 whitespace-nowrap text-sm text-gray-900">
                            ${servicio.mechanic.nombres} ${servicio.mechanic.apellido_paterno} ${servicio.mechanic.apellido_materno}
                        </td>
                        <td class="px-3 py-1 whitespace-nowrap text-sm">
                            <button type="button" id="btn-${servicio.id}"
                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full shadow-md 
                                    ${servicio.status_service == 1 
                                        ? 'bg-green-200 text-green-700' 
                                        : (servicio.status_service == 2 
                                            ? 'bg-yellow-200 text-yellow-700' 
                                            : 'bg-red-200 text-red-700')}">
                                ${servicio.status_service == 1 ? 'Completo' : servicio.status_service == 2 ? 'En proceso' : 'Pendiente'}
                            </button>
                        </td>
                        <td class="px-3 py-1 whitespace-nowrap text-sm text-gray-900">${servicio.descripcion}</td>
                        <td class="px-3 py-1 whitespace-nowrap text-sm text-gray-900">${servicio.fecha_registro}</td>
                        <td class="px-3 py-1 whitespace-nowrap text-sm">
                            <a href="#" class="text-indigo-600 hover:text-indigo-900 mr-3">Ver detalles</a>
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
            finAllServices();
        });
        document.addEventListener('DOMContentLoaded', () => {
            fecha_desde = document.getElementById('fecha_desde');
            fecha_hasta = document.getElementById('fecha_hasta');
            let today = new Date().toISOString().split('T')[0];
            fecha_desde.value = today;
            fecha_hasta.value = today;
            if (fecha_desde && fecha_hasta) {
                finAllServices();
            }
        });
    </script>

</x-app-layout>
