<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Registro de Productos
        </h2>
    </x-slot>

    <div class="w-3/4 mx-auto py-8 my-6 shadow-lg p-5 rounded-lg border border-gray-300">
        <form id="formProducts" enctype="multipart/form-data">
            @csrf
            <h5 class="text-lg font-semibold mb-4">Datos del Producto</h5>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
                    <input type="text" name="description" id="description" value="{{ $product->description }}"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700">Imagen (QR tamaño)</label>

                    @if ($product->bar_code)
                        <div class="mb-2">
                            <img src="{{ asset($product->bar_code) }}" alt="Producto" id="current-image"
                                class="w-16 h-16 object-contain border rounded-lg" name="bar_code_edit">
                            <img id="preview-image" class="w-16 h-16 object-contain border rounded-lg mt-2"
                                style="display: none;">

                            <button type="button" id="remove-image"
                                class="mt-2 px-3 py-1 bg-red-500 text-white rounded-md text-sm hover:bg-red-600">
                                Eliminar Imagen
                            </button>
                        </div>
                    @endif

                    <input type="file" name="bar_code_update" id="image" accept="image/*"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                </div>

                <input type="hidden" name="remove_image" id="remove_image" value="0">

                <div>
                    <label for="model" class="block text-sm font-medium text-gray-700">Modelo</label>
                    <input type="text" name="model" id="model" value="{{ $product->model }}"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="warehouse_id" class="block text-sm font-medium text-gray-700">Tipo de Almacén</label>
                    <select name="warehouse_id" id="warehouse_id"
                        class="form-select block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                        <option value="">Seleccione una opción</option>
                        @foreach ($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}"
                                {{ $product->warehouse_id == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="unit_id" class="block text-sm font-medium text-gray-700">Unidad de Medida</label>
                    <select name="unit_id" id="unit_id"
                        class="form-select block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                        <option value="">Seleccione una medida</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}" {{ $product->unit_id == $unit->id ? 'selected' : '' }}>
                                {{ $unit->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700">Cantidad</label>
                    <input type="number" name="quantity" id="quantity"
                        value="{{ isset($productStock->quantity) ? $productStock->quantity : '' }}"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="minimum_stock" class="block text-sm font-medium text-gray-700">Stock Mínimo</label>
                    <input type="number" name="minimum_stock" id="minimum_stock"
                        value="{{ isset($productStock->minimum_stock) ? $productStock->minimum_stock : '' }}"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="brand_id" class="block text-sm font-medium text-gray-700">Marca</label>
                    <select name="brand_id" id="brand_id"
                        class="form-select block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                        <option value="">Seleccione una medida</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}"
                                {{ $product->brand_id == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700">Ubicación</label>
                    <input type="text" name="location" id="location" value="{{ $product->location }}"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
            </div>

            <h5 class="text-lg font-semibold mb-4">Precios</h5>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
                @foreach ($product->prices as $price)
                    <div>
                        <label for="prices[{{ $price->type }}]" class="block text-sm font-medium text-gray-700">
                            Precio {{ ucfirst($price->type) }}
                        </label>
                        <input type="number" name="prices[{{ $price->type }}]" id="prices_{{ $price->type }}"
                            value="{{ $price->price }}"
                            class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                    </div>
                @endforeach
            </div>

            <div class="flex justify-center space-x-4 mt-6">
                <button id="registrar"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition"
                    type="submit">
                    Actualizar
                </button>
            </div>
        </form>
    </div>
</x-app-layout>

<script>
    document.getElementById('image').addEventListener('change', function(event) {
        let file = event.target.files[0];
        let previewImage = document.getElementById('preview-image');

        if (file) {
            let reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewImage.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    document.getElementById('remove-image')?.addEventListener('click', function() {
        let previewImage = document.getElementById('preview-image');
        let currentImage = document.getElementById('current-image'); // Identifica la imagen existente
        let imageInput = document.getElementById('image');
        let removeImageInput = document.getElementById('remove_image');

        // Ocultar la imagen actual y permitir subir otra
        if (currentImage) {
            currentImage.style.display = 'none';
        }

        // Limpiar cualquier imagen previa cargada
        previewImage.style.display = 'none';

        // Restablecer el input de imagen
        imageInput.value = '';

        // Indicar que la imagen anterior no debe mostrarse más
        removeImageInput.value = '1';
    });


    document.getElementById('formProducts').addEventListener('submit', async function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        formData.append('_method', 'PUT');
        try {
            let response = await fetch('{{ route('products.update', $product) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content')
                },
                body: formData
            });

            let data = await response.json();

            if (!response.ok) {
                let errorMessages = '';
                if (data.errors) {
                    for (let field in data.errors) {
                        errorMessages += `${field}: ${data.errors[field].join(', ')}\n`;
                    }
                } else if (data.error) {
                    errorMessages = data.error;
                }

                Swal.fire({
                    title: 'Errores de Validación',
                    text: errorMessages,
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            } else if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => {
                    window.location.reload();
                });
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error',
                text: 'Ocurrió un problema al actualizar el conductor',
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
        }
    });
</script>
