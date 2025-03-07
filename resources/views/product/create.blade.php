<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Registro de Productos
        </h2>
    </x-slot>

    <div class="w-3/4 mx-auto py-8 my-6 shadow-lg p-5 rounded-lg border border-gray-300 text-xs">
        <form id="formProducts" enctype="multipart/form-data">
            @csrf
            <h5 class="text-lg font-semibold mb-4">Datos del Producto</h5>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-4  ">
                <div>
                    <label for="code_sku" class="block  font-medium text-gray-700 ">Codigo</label>
                    <input type="text" name="code_sku" id="code_sku"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="description" class="block  font-medium text-gray-700">Descripción</label>
                    <input type="text" name="description" id="description"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="code_bar" class="block  font-medium text-gray-700">Codigo de Barras</label>
                    <input type="text" name="code_bar" id="code_bar"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="images" class="block  font-medium text-gray-700">Imágenes del Producto</label>
                    <input type="file" name="images[]" id="images" accept="image/*" multiple
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                    <div id="previewImages" class="mt-2 flex flex-wrap gap-2"></div>
                </div>
                <div>
                    <label for="model" class="block  font-medium text-gray-700">Modelo</label>
                    <input type="text" name="model" id="model"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="warehouse_id" class="block  font-medium text-gray-700">Tipo de Almacén</label>
                    <select name="warehouse_id" id="warehouse_id"
                        class="form-select block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                        <option value="">Seleccione una opción</option>
                        @foreach ($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="relative">
                    <label for="unit_name" class="block text-sm font-medium text-gray-700">Unidad de Medida</label>
                    <input type="text" id="unit_name" name="unit_name"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm " autocomplete="off">
                    <div id="unitDropdown"
                        class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden">
                        <ul id="unitSuggestions" class="max-h-40 overflow-y-auto"></ul>
                    </div>

                </div>

                <div>
                    <label for="quantity" class="block font-medium text-gray-700">Cantidad</label>
                    <input type="number" name="quantity" id="quantity"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="minimum_stock" class="block  font-medium text-gray-700">Stock Mínimo</label>
                    <input type="number" name="minimum_stock" id="minimum_stock"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="relative">
                    <label for="brand" class="block font-medium text-gray-700">Marca</label>
                    <input type="text" id="brand" name="brand"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm" autocomplete="off">

                    <!-- Dropdown de Sugerencias -->
                    <div id="brandDropdown"
                        class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden">
                        <ul id="brandSuggestions" class="max-h-40 overflow-y-auto"></ul>
                    </div>
                </div>

                <div>
                    <label for="location" class="block  font-medium text-gray-700">Ubicación</label>
                    <input type="text" name="location" id="location"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
            </div>

            <h5 class="text-lg font-semibold mb-4">Precios</h5>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
                <div>
                    <label for="prices[buy]" class="block  font-medium text-gray-700">Precio de Compra</label>
                    <input type="decimal" name="prices[buy]" id="prices_buy"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="prices[wholesale]" class="block font-medium text-gray-700">Precio
                        Mayorista</label>
                    <input type="decimal" name="prices[wholesale]" id="prices_wholesale"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="prices[sucursalA]" class="block  font-medium text-gray-700">Precio Sucursal
                        A</label>
                    <input type="decimal" name="prices[sucursalA]" id="prices_sucursal_a"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="prices[sucursalB]" class="block  font-medium text-gray-700">Precio Sucursal
                        B</label>
                    <input type="decimal" name="prices[sucursalB]" id="prices_sucursal_b"
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
    // Previsualizar imágenes múltiples
    document.getElementById('images').addEventListener('change', function(e) {
        const previewContainer = document.getElementById('previewImages');
        previewContainer.innerHTML = ""; // Limpiar previsualizaciones previas
        const files = this.files;

        if (files.length > 0) {
            Array.from(files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = "Previsualización de imagen";
                    img.className =
                        "w-20 h-20 object-cover border rounded-lg"; // Ajusta según tus necesidades
                    previewContainer.appendChild(img);
                }
                reader.readAsDataURL(file);
            });
        }
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

    // BUSQUEDA DE UNIDAD MEDIDA
    document.getElementById("unit_name").addEventListener("input", function() {
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
    // FIN DE BUSQUEDA DE UNIDAD MEDIDA
    document.getElementById('formProducts').addEventListener('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);

        fetch('{{ route('products.store') }}', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('response', response);
                if (!response.ok) {
                    console.log('hola');
                    return response.json().then(err => {
                        let errorMessages = '';
                        console.log(err)
                        if (err.errors) {
                            console.log("1")
                            for (let field in err.errors) {
                                errorMessages += `${field}: ${err.errors[field].join(', ')}\n`;
                            }
                        } else if (err.error) {
                            console.log("2")
                            errorMessages = err.error;
                        } else if (err.errorPago) {
                            console.log("3")
                            errorMessages = err.errorPago;
                        }
                        console.log(errorMessages)

                        if (errorMessages) {
                            console.log("4")
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
                    document.getElementById('formProducts').reset();
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });
</script>
