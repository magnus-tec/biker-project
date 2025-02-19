<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Registro de Productos
        </h2>
    </x-slot>

    <div class="w-3/4 mx-auto py-8 my-6 shadow-lg p-5 rounded-lg border border-gray-300">
        <form id="formProducts">
            @csrf
            <h5 class="text-lg font-semibold mb-4">Datos del Producto</h5>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
                    <input type="text" name="description" id="description"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="model" class="block text-sm font-medium text-gray-700">Modelo</label>
                    <input type="text" name="model" id="model"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="warehouse_id" class="block text-sm font-medium text-gray-700">Tipo de Almacén</label>
                    <select name="warehouse_id" id="warehouse_id"
                        class="form-select block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                        <option value="">Seleccione una opción</option>
                        @foreach ($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="unit_id" class="block text-sm font-medium text-gray-700">Unidad de Medida</label>
                    <select name="unit_id" id="unit_id"
                        class="form-select block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                        <option value="">Seleccione una medida</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700">Cantidad</label>
                    <input type="number" name="quantity" id="quantity"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="minimum_stock" class="block text-sm font-medium text-gray-700">Stock Mínimo</label>
                    <input type="number" name="minimum_stock" id="minimum_stock"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="brand_id" class="block text-sm font-medium text-gray-700">Marca</label>
                    <select name="brand_id" id="brand_id"
                        class="form-select block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                        <option value="">Seleccione una medida</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700">Ubicación</label>
                    <input type="text" name="location" id="location"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
            </div>

            <h5 class="text-lg font-semibold mb-4">Precios</h5>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
                <div>
                    <label for="prices[buy]" class="block text-sm font-medium text-gray-700">Precio de Compra</label>
                    <input type="number" name="prices[buy]" id="prices_buy"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="prices[wholesale]" class="block text-sm font-medium text-gray-700">Precio
                        Mayorista</label>
                    <input type="number" name="prices[wholesale]" id="prices_wholesale"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="prices[sucursalA]" class="block text-sm font-medium text-gray-700">Precio Sucursal
                        A</label>
                    <input type="number" name="prices[sucursalA]" id="prices_sucursal_a"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="prices[sucursalB]" class="block text-sm font-medium text-gray-700">Precio Sucursal
                        B</label>
                    <input type="number" name="prices[sucursalB]" id="prices_sucursal_b"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
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
    document.getElementById('formProducts').addEventListener('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);

        fetch('{{ route('products.store') }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 2000
                    });
                    document.getElementById('formProducts').reset();
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'Hubo un problema al registrar el producto',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error del Servidor',
                    text: 'No se pudo procesar la solicitud.',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            });
    });
</script>
