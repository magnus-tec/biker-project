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
                @can('filtrar-por-trabajador-servicios')
                    <select name="mechanic" id="mechanic" class="border border-gray-300 rounded-lg py-2 px-4 mr-2">
                        <option value="todos">Todos</option>
                        @foreach ($mechanics as $mechanic)
                            <option value="{{ $mechanic->id }}">{{ $mechanic->name }} {{ $mechanic->apellidos }}</option>
                        @endforeach
                    </select>
                @endcan
                @can('filtrar-por-estado-servicios')
                    <div>
                        <label for="">Estado: </label>
                        <select name="estado-filtro" id="estado-filtro" class="border border-gray-300 rounded-lg py-2 px-4">
                            <option value="">Todos</option>
                            <option value="0">Pendiente</option>
                            <option value="1">Completo</option>
                            <option value="2">En proceso</option>
                        </select>
                    </div>
                @endcan
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">
                    Buscar
                </button>


            </form>
            @can('agregar-servicios')
                <a href="{{ route('services.create') }}"
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
                            Nº Codigo
                        </th>
                        <th class="px-3 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nº Motor
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

    <!-- MODAL AGREGAR DETALLES -->
    <div id="modalAgregarDetalles" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded-lg shadow-lg w-96">
            <!-- Encabezado del modal -->
            <div class="flex justify-between items-center border-b px-4 py-2">
                <h5 class="text-lg font-semibold">Agregar Detalles</h5>
                <button class="close-modal text-gray-500 hover:text-gray-700"
                    onclick="toggleModal('modalAgregarDetalles', false)">&times;</button>
            </div>

            <!-- Contenido del modal -->
            <div class="p-4">
                <form id="formAgregarDetalles">
                    @csrf
                    <input type="hidden" id="serviceIdAgregar">

                    <!-- Selección de Estado -->
                    <div class="mb-3">
                        <label for="estado" class="block font-medium">Estado del Servicio</label>
                        <select id="estado" name="estado"
                            class="w-full border border-gray-300 rounded-lg py-2 px-4">
                            <option value="">Seleccione un estado</option>
                            <option value="0">Pendiente</option>
                            <option value="1">Completo</option>
                            <option value="2">En proceso</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="descripcionAgregar" class="block font-medium">Detalle del Servicio</label>
                        <textarea id="descripcionAgregar" class="w-full p-2 border rounded-lg"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg">
                        Guardar
                    </button>
                </form>
            </div>
        </div>
    </div>


    <!-- MODAL VER DETALLES -->
    <div id="modalVerDetalles" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded-lg shadow-lg w-96">
            <div class="flex justify-between items-center border-b px-4 py-2">
                <h5 class="text-lg font-semibold">Detalles del Servicio</h5>
                <button class="close-modal text-gray-500 hover:text-gray-700"
                    onclick="toggleModal('modalVerDetalles', false)">&times;</button>
            </div>
            <div class="p-4">
                <input type="hidden" id="serviceIdDetalles">
                <p><strong>Descripción:</strong></p>
                <p id="descripcionVer"></p>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        window.authUser = @json(auth()->user()->load('roles'));

        function toggleModal(id, show) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.toggle('hidden', !show);
            }
        }
        document.getElementById("formAgregarDetalles").addEventListener("submit", function(event) {
            event.preventDefault();
            enviarDetalles();
        });
        async function enviarDetalles() {
            const serviceId = document.getElementById("serviceIdAgregar").value;
            const estado = document.getElementById("estado").value || null;
            const descripcion = document.getElementById("descripcionAgregar").value;

            console.log('serviceId' + serviceId, 'estado:' + estado, 'descripcion:' + descripcion);

            const formData = {
                serviceId,
                estado,
                descripcion
            };

            try {
                const response = await fetch("{{ route('service.cambiarEstado') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (response.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Guardado',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 2000
                    })
                    toggleModal("modalAgregarDetalles", false);
                    finAllServices();
                } else {
                    alert("Error al guardar: " + data.message);
                }
            } catch (error) {
                console.error("Error:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error,
                    showConfirmButton: false,
                    timer: 2000
                })
            }
        }

        async function openModal(serviceId) {
            console.log('entro');

            //const serviceId = button.getAttribute('data-service-id');
            const description = 'desc';

            if (window.authUser?.roles?.some(role => role.name === 'mecanico')) {
                document.getElementById('serviceIdAgregar').value = serviceId;
                document.getElementById('modalAgregarDetalles').classList.remove(
                    'hidden');
            } else {
                try {
                    const response = await fetch(
                        `{{ route('service.verDetalles') }}?serviceId=${encodeURIComponent(serviceId)}`, {
                            method: "GET",
                            headers: {
                                "Content-Type": "application/json",
                            },
                        });

                    if (!response.ok) {
                        throw new Error("No se pudieron obtener los detalles del servicio.");
                    }

                    const data = await response.json();

                    // Mostrar los detalles en el modal
                    document.getElementById('serviceIdDetalles').value = serviceId;
                    document.getElementById('descripcionVer').textContent = data.service.detalle_servicio ||
                        'Sin descripción';
                    document.getElementById('modalVerDetalles').classList.remove('hidden');

                } catch (error) {
                    console.error("Error al obtener detalles:", error);
                    alert("Hubo un error al cargar los detalles del servicio.");
                }
            }
        }


        function finAllServices() {
            let desde = document.getElementById('fecha_desde').value;
            let hasta = document.getElementById('fecha_hasta').value;
            let mechanicElement = document.getElementById('mechanic');
            let mechanic = mechanicElement ? mechanicElement.value : '';
            let estadoElement = document.getElementById('estado-filtro');
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
                            ${servicio.drive.nro_motor}
                        </td>
                        <td class="px-3 py-1 whitespace-nowrap text-sm text-gray-900">${(servicio.car.placa)?servicio.car.placa:'Sin placa'}</td>
                        <td class="px-3 py-1 whitespace-nowrap text-sm text-gray-900">
                            ${servicio.user.name} ${servicio.user.apellidos}
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
                            <button type="button" onclick="openModal(${servicio.id})"
                                class="px-3 py-1 text-white font-semibold rounded open-modal
                                ${window.authUser?.roles?.some(role => role.name === 'mecanico') 
                                    ? 'bg-green-600 hover:bg-green-700' 
                                    : 'bg-blue-600 hover:bg-blue-700'}"
                                data-service-id="${servicio.id}"
                                data-description="${servicio.descripcion}">
                                ${window.authUser?.roles?.some(role => role.name === 'mecanico') 
                                    ? 'Agregar Detalles' 
                                    : 'Ver detalles'}
                            </button>
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
                finAllServices();
            }
            //fin calculo
        });
    </script>

</x-app-layout>
