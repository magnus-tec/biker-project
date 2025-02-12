<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{-- Registro de Socios --}}
        </h2>
    </x-slot>
    <div class="w-3/4 mx-auto py-8" id="vehiculo" role="tabpanel" aria-labelledby="vehiculo-tab">
        <form class="p-6 bg-white rounded-lg shadow-md" id="formCar">
            @csrf
            <h5 class="text-lg font-semibold text-gray-800 mb-4">Datos del Vehículo</h5>
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
                    <div class="flex mt-2">
                        <input name="drive_id" id="drive_id" type="hidden"
                            class="block w-full  border border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">

                <div>
                    <label for="n_placa" class="block text-sm font-medium text-gray-700">N° Placa</label>
                    <input type="text" name="n_placa"
                        class="block w-full p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="marca" class="block text-sm font-medium text-gray-700">Marca</label>
                    <input type="text" name="marca"
                        class="block w-full p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="modelo" class="block text-sm font-medium text-gray-700">Modelo</label>
                    <input type="text" name="modelo"
                        class="block w-full p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="anio" class="block text-sm font-medium text-gray-700">Año</label>
                    <input type="text" name="anio"
                        class="block w-full p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label for="color" class="block text-sm font-medium text-gray-700">Color</label>
                    <input type="text" name="color"
                        class="block w-full p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="tipo_condicion" class="block text-sm font-medium text-gray-700">Condición</label>
                    <select name="tipo_condicion" class="block w-full p-2 border border-gray-300 rounded-md shadow-sm">
                        <option value="">Seleccionar</option>
                        <option value="Propio">Propio</option>
                        <option value="Alquilado">Alquilado</option>
                    </select>
                </div>
                <div>
                    <label for="fecha_soat" class="block text-sm font-medium text-gray-700">Fecha Vencimiento
                        SOAT</label>
                    <input type="date" name="fecha_soat"
                        class="block w-full p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="nro_chasis" class="block text-sm font-medium text-gray-700">Numero de chasis</label>
                    <input type="text" name="nro_chasis"
                        class="block w-full p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="fecha_seguro" class="block text-sm font-medium text-gray-700">Fecha vencimiento Seguro
                        Vehicular</label>
                    <input type="date" name="fecha_seguro"
                        class="block w-full p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
            </div>
            <div class="flex justify-center space-x-4 mt-6">
                <button id="registrar"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition"
                    type="submit">
                    Registrar
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
<script>
    let formCar = document.getElementById('formCar');
    // let nro_motor = document.getElementById('nro_motor').value;

    function searchDrive() {
        let nro_motor = document.getElementById('nro_motor').value;

        fetch(`{{ route('buscar.Driver') }}?nro_motor=${encodeURIComponent(nro_motor)}`, {
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
                    return;
                } else {
                    if (data.drive.nombres === null || data.drive.apellido_paterno === null || data
                        .drive.apellido_materno === null) {
                        document.getElementById('datos_driver').value =
                            'Este numero de motor no tiene nombres y apellidos';
                        document.getElementById('drive_id').value = data.drive.id;

                        return;
                    }
                    document.getElementById('drive_id').value = data.drive.id;
                    document.getElementById('datos_driver').value = data.drive.nombres + ' ' + data.drive
                        .apellido_paterno + ' ' + data.drive.apellido_materno;
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }
    formCar.addEventListener('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(formCar);

        fetch('{{ route('cars.store') }}', {
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
