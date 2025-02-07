<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{-- Registro de Socios --}}
        </h2>
    </x-slot>
    <div class=" w-3/4 mx-auto py-8">
        <form id="formCustomer">
            @csrf
            <h5 class="text-lg font-semibold mb-4">Datos del Conductor</h5>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
                <div>
                    <label for="tipo_doc" class="block text-sm font-medium text-gray-700">Tipo Documento</label>
                    <select name="tipo_doc" id="tipo_doc"
                        class="form-select block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                        <option value="notDoc">Seleccione un Documento</option>
                        <option value="DNI">DNI</option>
                        <option value="Pasaporte">Pasaporte</option>
                        <option value="Carnet">Carnet de Extranjería</option>
                    </select>
                </div>
                <div>
                    <label for="num_doc" class="block text-sm font-medium text-gray-700">N° Documento</label>
                    <div class="flex mt-2">
                        <input name="num_doc" type="text" placeholder="Ingrese Documento" id="num_doc"
                            class="block w-full p-2 border border-gray-300 rounded-md shadow-sm">
                        <button id="buscarDni" class="ml-2 py-2 px-4 bg-yellow-500 text-white rounded-md"
                            type="button">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label for="nacionalidad" class="block text-sm font-medium text-gray-700">Nacionalidad</label>
                    <input type="text" name="nacionalidad" id="nacionalidad"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm" required>
                </div>
                <div class="col-span-1 lg:col-span-1 row-span-4">
                    <label for="photo" class="block text-sm font-medium text-gray-700">Foto</label>
                    <div class="mt-2">
                        <input type="file" id="photo" name="photo"
                            class="block w-full text-sm text-gray-500 file:py-2 file:px-4 file:border file:rounded-lg file:bg-gray-100 hover:file:bg-gray-200" />
                        <div
                            class="mt-2 flex items-center justify-center p-4 border-2 border-dashed border-gray-300 rounded-lg">
                            <span id="photo-placeholder" class="text-gray-500">Sube una foto</span>
                        </div>
                    </div>
                </div>
                <div>
                    <label for="nombres" class="block text-sm font-medium text-gray-700">Nombres</label>
                    <input type="text" placeholder="Nombre" name="nombres" id="nombres"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="apellido_paterno" class="block text-sm font-medium text-gray-700">Apellido
                        Paterno</label>
                    <input type="text" placeholder="Apellido Paterno" name="apellido_paterno" id="apellido_paterno"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="apellido_materno" class="block text-sm font-medium text-gray-700">Apellido
                        Materno</label>
                    <input type="text" placeholder="Apellido Materno" name="apellido_materno" id="apellido_materno"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                </div>

                <div>
                    <label for="nro_licencia" class="block text-sm font-medium text-gray-700">Nº de Licencia</label>
                    <input type="text" name="nro_licencia"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="licencia_categoria" class="block text-sm font-medium text-gray-700">Lic.
                        Categoría</label>
                    <select name="licencia_categoria"
                        class="form-select block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                        <option value="">Seleccionar</option>
                        <option value="AI">AI</option>
                        <option value="AIIA">AIIA</option>
                        <option value="AIIB">AIIB</option>
                        <option value="AIIIA">AIIIA</option>
                        <option value="AIIIB">AIIIB</option>
                        <option value="AIIIC">AIIIC</option>
                    </select>
                </div>
                <div>
                    <label for="nro_unidad" class="block text-sm font-medium text-gray-700">Número de Unidad</label>
                    <input type="text" name="nro_unidad"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="fecha_nacimiento" class="block text-sm font-medium text-gray-700">Fecha de
                        Nacimiento</label>
                    <input type="date" name="fecha_nacimiento"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="telefono" class="block text-sm font-medium text-gray-700">Nº de Telefono</label>
                    <input type="text" name="telefono"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="correo" class="block text-sm font-medium text-gray-700">Correo</label>
                    <input type="text" name="correo"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
            </div>
            <div class="my-6 shadow-lg p-5 rounded-lg border-r-gray-500 " style="border: 2px solid rgb(215,215,215);">
                <h5 class="text-lg font-semibold mb-4">Dirección</h5>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label for="departamento" class="block text-sm font-medium text-gray-700">Departamento</label>
                        <input type="text" name="departamento"
                            class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label for="provincia" class="block text-sm font-medium text-gray-700">Provincia</label>
                        <input type="text" name="provincia"
                            class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label for="distrito" class="block text-sm font-medium text-gray-700">Distrito</label>
                        <input type="text" name="distrito"
                            class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label for="direccion_domicilio"
                            class="block text-sm font-medium text-gray-700">Av./Cal./Pj./Urb./Mz./Lt./Otros</label>
                        <input type="text" name="direccion_domicilio"
                            class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>
            </div>

            <div class="my-6 shadow-lg p-5 rounded-lg border-r-gray-500 " style="border: 2px solid rgb(215,215,215);">
                <h5 class="text-lg font-semibold mb-4">Contacto de Emergencia</h5>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label for="nombre_contacto" class="block text-sm font-medium text-gray-700">Nombres</label>
                        <input type="text" name="nombre_contacto"
                            class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label for="telefono_contacto" class="block text-sm font-medium text-gray-700">N°
                            Telefono</label>
                        <input type="text" name="telefono_contacto"
                            class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label for="parentesco_contacto"
                            class="block text-sm font-medium text-gray-700">Parentesco</label>
                        <input type="text" name="parentesco_contacto"
                            class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                    </div>
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
    let form = document.getElementById('formCustomer');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(form);

        fetch('{{ route('drives.store') }}', {
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
    document.addEventListener('DOMContentLoaded', () => {
        // api dni
        const Inputnum_doc = document.getElementById('num_doc');
        const token =
            'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6InN5c3RlbWNyYWZ0LnBlQGdtYWlsLmNvbSJ9.yuNS5hRaC0hCwymX_PjXRoSZJWLNNBeOdlLRSUGlHGA';

        Inputnum_doc.addEventListener('input', () => {
            const num_doc = Inputnum_doc.value;
            if (num_doc.length === 8) {
                fetch(`https://dniruc.apisperu.com/api/v1/dni/${num_doc}?token=${token}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error en la solicitud');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success === false) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'No se pudo encontrar el DNI',
                                showConfirmButton: false,
                                timer: 2000
                            });
                            document.getElementById('apellido_paterno').value = '';
                            document.getElementById('apellido_materno').value = '';
                            document.getElementById('nombres').value = '';
                        } else {
                            document.getElementById('apellido_paterno').value = data
                                .apellidoPaterno || '';
                            document.getElementById('apellido_materno').value = data
                                .apellidoMaterno || '';
                            document.getElementById('nombres').value = data.nombres || '';
                        }

                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Hubo un problema con la solicitud',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    });
            } else {
                document.getElementById('apellido_paterno').value = '';
                document.getElementById('apellido_materno').value = '';
                document.getElementById('nombres').value = '';
            }
        });
    })
</script>
