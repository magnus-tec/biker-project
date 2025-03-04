<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{-- Registro de Socios --}}
        </h2>
    </x-slot>
    <div class="w-3/4 mx-auto py-8" id="descripDoc" role="tabpanel" aria-labelledby="descripDoc-tab">
        <form class="p-6 bg-white rounded-lg shadow-md" id="formService">
            @csrf
            <h5 class="text-lg font-semibold text-gray-800 mb-4">Detalle del problema</h5>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="num_doc" class="block text-sm font-medium text-gray-700">N° motor</label>
                    <div class="flex mt-2">
                        <input name="nro_motor" id="nro_motor" type="text" placeholder="Ingrese numero de motor"
                            class="block w-full  border border-gray-300 rounded-md shadow-sm">
                        <button id="buscarDrive" class="ml-2 py-2 px-4 bg-yellow-500 text-white rounded-md"
                            type="button" onclick="searchDrive()">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label for="datos_driver" class="block text-sm font-medium text-gray-700">Nombres y
                        apellidos</label>
                    <div class="flex mt-2">
                        <input name="datos_driver" id="datos_driver" type="text" placeholder="Ingrese Documento"
                            class="block w-full  border border-gray-300 rounded-md shadow-sm">

                    </div>
                </div>
                <div>
                    <div class=" mt-2">
                        <input name="drive_id" id="drive_id" type="hidden"
                            class="block w-full  border border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>

                <div class="flex flex-col mt-2 col-span-2" id="listaVehiculos">

                </div>
                <input name="car_id" id="car_id" type="hidden"
                    class="block w-full  border border-gray-300 rounded-md shadow-sm">
            </div>


            <div id="modalMecanicos"
                class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center">
                <div class="bg-white p-6 rounded-lg shadow-md w-1/3">
                    <h3 class="text-xl font-semibold mb-4">Mecánicos Disponibles</h3>
                    <div id="listaMecanicosModal"></div>
                    <button onclick="cerrarModal()"
                        class="mt-4 px-4 py-2 bg-red-500 text-white rounded-lg">Cerrar</button>
                </div>
            </div>
            <div>
                <div class="flex mt-2">
                    <input name="datos_mecanico" id="datos_mecanico" type="text"
                        class="block w-full  border border-gray-300 rounded-md shadow-sm">
                    <input name="mechanics_id" id="mechanics_id" type="hidden"
                        class="block w-full  border border-gray-300 rounded-md shadow-sm">
                    <button onclick="mostrarModal()" type="button"
                        class="px-4 py-2 bg-green-500 text-white rounded-lg  whitespace-nowrap">Seleccionar
                        Mecánico</button>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label for="detalle" class="block text-sm font-medium text-gray-700">Detalle</label>
                    <textarea name="detalle" id="detalle" cols="50" rows="5"
                        class="block w-full p-4 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                        placeholder="Escribe aquí..."></textarea>
                </div>
            </div>

            <div class="flex justify-center space-x-4 mt-6">
                <button id="registrar"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition"
                    onclick="guardarServicio()">
                    Registrar
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
<script>
    let contenedorListadoCar = '';

    function mostrarModal() {
        document.getElementById('modalMecanicos').classList.remove('hidden');
        fetch('{{ route('obtener.MecanicosDisponibles') }}')
            .then(response => response.json())
            .then(data => {
                let contenedor = document.getElementById('listaMecanicosModal');
                contenedor.innerHTML = '';

                data.forEach(mecanico => {
                    let row = `
                    <div class="flex justify-between items-center p-2 border-b">
                        <span>${mecanico.name} ${mecanico.apellidos} </span>
                        <button onclick="seleccionarMecanico(${mecanico.id}, '${mecanico.name} ${mecanico.apellidos}'); cerrarModal()" 
                            class="px-3 py-1 bg-blue-500 text-white rounded-lg" type="button">
                            Asignar
                        </button>
                    </div>
                `;
                    contenedor.innerHTML += row;
                });
            });
    }

    function cerrarModal() {
        document.getElementById('modalMecanicos').classList.add('hidden');
    }

    function seleccionarMecanico(id, datos) {
        document.getElementById('mechanics_id').value = id;
        document.getElementById('datos_mecanico').value = datos;
    }

    function searchDrive() {
        let nro_motor = document.getElementById('nro_motor').value;

        fetch(`{{ route('buscar.Vehiculo') }}?nro_motor=${encodeURIComponent(nro_motor)}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error('Error en la respuesta del servidor', err);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.error,
                        showConfirmButton: false,
                        timer: 2000
                    });
                    document.getElementById('drive_id').value = '';
                    document.getElementById('datos_driver').value = '';
                    document.getElementById('car_id').value = '';
                    contenedorListadoCar.innerHTML = '';
                    return;
                }

                if (data.drive.nombres === null || data.drive.apellido_paterno === null || data.drive
                    .apellido_materno === null) {
                    document.getElementById('datos_driver').value =
                        'Este numero de motor no tiene nombres y apellidos';
                    document.getElementById('drive_id').value = data.drive.id;
                } else {
                    document.getElementById('drive_id').value = data.drive.id;
                    document.getElementById('datos_driver').value = data.drive.nombres + ' ' + data.drive
                        .apellido_paterno + ' ' + data.drive.apellido_materno;
                }

                contenedorListadoCar = document.getElementById('listaVehiculos');
                contenedorListadoCar.innerHTML = '';
                data.car.forEach(car => {
                    let row = `
                        <div class="grid grid-cols-7 gap-1 border-b items-center p-2 border">
                            <span class="text-left">Placa: ${car.placa ? car.placa : 'Sin placa'}</span>
                            <span class="text-left">Marca: ${car.marca ? car.marca : 'Sin marca'}</span>
                            <span class="text-left">Modelo: ${car.modelo ? car.modelo : 'Sin modelo'}</span>
                            <span class="text-left">Color: ${car.color ? car.color : 'Sin color'}</span>
                            <span class="text-left">Año: ${car.anio ? car.anio : 'Sin año'}</span>
                            <button onclick="seleccionarVehiculo(this, ${car.id}); cerrarModal()" 
                                class="px-3 py-1 bg-blue-500 text-white rounded-lg" type="button">
                                Asignar
                            </button>
                        </div>
                    `;
                    contenedorListadoCar.innerHTML += row;
                });
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }
    let selectedVehicleButton = null;

    function seleccionarVehiculo(btn, id) {
        if (selectedVehicleButton && selectedVehicleButton !== btn) {
            selectedVehicleButton.classList.remove("bg-green-500");
            selectedVehicleButton.classList.add("bg-blue-500");
            selectedVehicleButton.innerText = "Asignar";
        }
        btn.classList.remove("bg-blue-500");
        btn.classList.add("bg-green-500");
        btn.innerText = "Asignado";
        selectedVehicleButton = btn;
        document.getElementById('car_id').value = id;
    }

    let form = document.getElementById('formService');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(form);

        fetch('{{ route('services.store') }}', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        let errorMessages = '';
                        if (err.errors) {
                            for (let field in err.errors) {
                                errorMessages += `${field}: ${err.errors[field].join(', ')}\n`;
                            }
                        } else if (err.error) {
                            errorMessages = err.error;
                        } else if (err.errorPago) {
                            errorMessages = err.errorPago;
                        }

                        if (errorMessages) {
                            Swal.fire({
                                title: 'Errores de Validación',
                                text: errorMessages,
                                icon: 'error',
                                confirmButtonText: 'Aceptar'
                            });
                        }

                        throw new Error('Error en la respuesta del servidor');
                    });
                }

                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });
</script>
