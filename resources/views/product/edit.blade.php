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
                    <label for="code_sku" class="block text-sm font-medium text-gray-700">Codigo</label>
                    <input type="text" name="code_sku" id="code_sku" value="{{ $product->code_sku }}"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="grid grid-cols-4 sm:grid-cols-4 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <h1 for="images" class="col-span-full  text-sm font-medium text-gray-700">Imágenes del
                        Producto</h1>
                    @foreach ($product->images as $image)
                        <div class="relative">
                            <img src="{{ asset($image->image_path) }}" alt="Imagen del producto"
                                class="w-20 h-20 object-cover border rounded-lg">
                            <button type="button"
                                class="absolute top-0 right-0 bg-red-500 text-white rounded-full px-2 py-1 text-xs btn-delete-image"
                                data-image-id="{{ $image->id }}">
                                X
                            </button>
                        </div>
                    @endforeach
                </div>
                <!-- Contenedor para previsualizar nuevas imágenes -->
                <div>
                    <label for="images" class="block text-sm font-medium text-gray-700">Imágenes del Producto</label>
                    <input type="file" name="new_images[]" id="new_images" accept="image/*" multiple
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                    <div id="preview-container" class="grid grid-cols-3 gap-4 mt-4"></div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
                    <input type="text" name="description" id="description" value="{{ $product->description }}"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="bar_code" class="block text-sm font-medium text-gray-700">Codigo de Barras</label>
                    <input type="text" name="code_bar" id="code_bar" value="{{ $product->code_bar }}"
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
                <div class="relative">
                    <label for="unit_name" class="block text-sm font-medium text-gray-700">Unidad de Medida</label>
                    <input type="text" id="unit_name" name="unit_name"
                        value="{{ old('unit_name', $product->unit->name) }}"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm" autocomplete="off">
                    <div id="unitDropdown"
                        class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden">
                        <ul id="unitSuggestions" class="max-h-40 overflow-y-auto"></ul>
                    </div>
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
                <div class="relative">
                    <label for="brand" class="block font-medium text-gray-700">Marca</label>
                    <input type="text" id="brand" name="brand"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm" autocomplete="off"
                        value="{{ old('brand', $product->brand->name) }}">

                    <!-- Dropdown de Sugerencias -->
                    <div id="brandDropdown"
                        class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden">
                        <ul id="brandSuggestions" class="max-h-40 overflow-y-auto"></ul>
                    </div>
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
                            @php
                                switch ($price->type) {
                                    case 'buy':
                                        $label = 'Precio Compra';
                                        break;
                                    case 'sucursalA':
                                        $label = 'Precio Sucursal A';
                                        break;
                                    case 'sucursalB':
                                        $label = 'Precio Sucursal B';
                                        break;
                                    case 'wholesale':
                                        $label = 'Precio Mayorista';
                                        break;
                                    default:
                                        $label = 'Precio ' . ucfirst($price->type);
                                        break;
                                }
                            @endphp
                            {{ $label }}
                        </label>
                        <input type="decimal" name="prices[{{ $price->type }}]" id="prices_{{ $price->type }}"
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
    //ELIMINAR IMAGENES
    let deletedImages = [];

    document.addEventListener("click", function(event) {
        if (event.target.classList.contains("btn-delete-image")) {
            let imageId = event.target.dataset.imageId;

            if (!deletedImages.includes(imageId)) {
                deletedImages.push(imageId);
            }

            let imageContainer = event.target.closest(".relative");
            if (imageContainer) {
                imageContainer.remove();
            }
        }
    });
    // FIN
    document.getElementById('remove-image')?.addEventListener('click', function() {
        let previewImage = document.getElementById('preview-image');
        let currentImage = document.getElementById('current-image');
        let imageInput = document.getElementById('image');
        let removeImageInput = document.getElementById('remove_image');
        if (currentImage) {
            currentImage.style.display = 'none';
        }
        previewImage.style.display = 'none';
        imageInput.value = '';
        removeImageInput.value = '1';
    });
    document.getElementById('new_images').addEventListener('change', function(event) {
        let previewContainer = document.getElementById('preview-container');
        previewContainer.innerHTML = '';

        for (let file of event.target.files) {
            let reader = new FileReader();
            reader.onload = function(e) {
                let img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'w-24 h-24 object-contain border rounded-lg';
                previewContainer.appendChild(img);
            };
            reader.readAsDataURL(file);
        }
    });

    document.querySelectorAll('.remove-image').forEach(button => {
        button.addEventListener('click', function() {
            let imageId = this.getAttribute('data-image-id');
            this.parentElement.style.display = 'none';

            let inputHidden = document.createElement('input');
            inputHidden.type = 'hidden';
            inputHidden.name = 'remove_images[]';
            inputHidden.value = imageId;
            document.getElementById('formProducts').appendChild(inputHidden);
        });
    });
    document.getElementById('formProducts').addEventListener('submit', async function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        formData.append('_method', 'PUT');
        formData.append('deleted_images', JSON.stringify(deletedImages));
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
    document.getElementById("unit_name").addEventListener("input", function() {
        console.log("Evento input detectado:", this.value); // Agrega esto

        const inputValue = this.value.trim();
        const suggestionsList = document.getElementById("unitSuggestions");
        const dropdown = document.getElementById("unitDropdown");
        if (inputValue === "") {
            suggestionsList.innerHTML = "";
            dropdown.classList.add("hidden");
            return;
        }
        fetch(`/units?query=${this.value}`)
            .then(response => response.json())
            .then(data => {
                suggestionsList.innerHTML = "";

                if (data.length > 0) {
                    data.forEach(unit => {
                        const item = document.createElement("li");
                        item.textContent = unit.name;
                        item.classList.add("cursor-pointer", "p-2", "hover:bg-gray-100");

                        item.addEventListener("click", function() {
                            document.getElementById("unit_name").value = unit.name;
                            dropdown.classList.add("hidden");
                        });

                        suggestionsList.appendChild(item);
                    });

                    dropdown.classList.remove("hidden");
                } else {
                    dropdown.classList.add("hidden");
                }
            });
    });
    // BUSQUEDA DE MARCA
    document.getElementById("brand").addEventListener("input", function() {
        const inputValue = this.value.trim();
        const suggestionsList = document.getElementById("brandSuggestions");
        const dropdown = document.getElementById("brandDropdown");
        if (inputValue === "") {
            suggestionsList.innerHTML = "";
            dropdown.classList.add("hidden");
            return;
        }
        fetch(`/api/brands?query=${this.value}`)
            .then(response => response.json())
            .then(data => {
                suggestionsList.innerHTML = "";

                if (data.length > 0) {
                    data.forEach(brand => {
                        const item = document.createElement("li");
                        item.textContent = brand.name;
                        item.classList.add("cursor-pointer", "p-2", "hover:bg-gray-100");

                        item.addEventListener("click", function() {
                            document.getElementById("brand").value = brand.name;
                            dropdown.classList.add("hidden");
                        });

                        suggestionsList.appendChild(item);
                    });

                    dropdown.classList.remove("hidden");
                } else {
                    dropdown.classList.add("hidden");

                }
            });
    });
    // Ocultar el dropdown cuando se hace clic fuera
    document.addEventListener("click", function(event) {
        const dropdown = document.getElementById("brandDropdown");
        if (!document.getElementById("brand").contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.classList.add("hidden");
        }
    });

    // FIN DE MARCA
</script>
