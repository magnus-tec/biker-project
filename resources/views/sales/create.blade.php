<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{-- Registro de Socios --}}
        </h2>
    </x-slot>
    <div class="container mx-auto p-2 text-sm">
        <div class="grid grid-cols-3 gap-6">
            <!-- Formulario de Cliente -->
            <div class="col-span-2 bg-white p-6 rounded-lg shadow">
                <h2 class="text-lg font-bold mb-4">Cliente</h2>
                <input type="text" id="dni_personal" placeholder="Ingrese Documento"
                    class="w-full p-2 border rounded mb-2">
                <input type="text" placeholder="Nombre del cliente" id="nombres_apellidos"
                    class="w-full p-2 border rounded mb-2">
                <input type="text" placeholder="Direccion del cliente" id="direccion"
                    class="w-full p-2 border rounded mb-2">
                <select name="region" id="regions_id" class="w-3/12 p-2 border rounded">
                    <option value="todos">Seleccione un Departamento</option>
                    @foreach ($regions as $region)
                        <option value="{{ $region->id }}">{{ $region->name }}</option>
                    @endforeach
                </select>
                <select name="" id="provinces_id" class="w-3/12 p-2 border rounded" disabled>
                    <option value="todos">Seleccione una opción</option>
                </select>
                <select name="" id="districts_id" class="w-3/12 p-2 border rounded" disabled>
                    <option value="todos">Seleccione una opción</option>
                </select>
                <!-- Botón que abre el modal -->
                <button class="bg-yellow-400 p-2 rounded w-3/12" id="buscarProductos">Consultar Productos</button>
                <div class="relative">
                    <label for="service" class="block font-medium text-gray-700">Servicio</label>
                    <input type="text" id="service" name="service" value="{{ old('service', 'TALLER') }}"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm" autocomplete="off">

                    <!-- Dropdown de Sugerencias -->
                    <div id="serviceDropdown"
                        class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden">
                        <ul id="serviceSuggestions" class="max-h-40 overflow-y-auto"></ul>
                    </div>
                </div>

                <div class="mt-3">
                    <label for="service_price" class="block font-medium text-gray-700">Precio del Servicio</label>
                    <input type="number" id="service_price" name="service_price" value="{{ old('service_price', 60) }}"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                </div>

                <button type="button" id="addService" class="bg-blue-500 text-white px-4 py-2 mt-3 rounded-md">Agregar
                    Servicio</button>
                <div id="modalMecanicos"
                    class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center">
                    <div class="bg-white p-6 rounded-lg shadow-md w-1/3">
                        <h3 class="text-xl font-semibold mb-4">Mecánicos Disponibles</h3>
                        <div id="listaMecanicosModal"></div>
                        <button onclick="closeModal('modalMecanicos')"
                            class="mt-4 px-4 py-2 bg-red-500 text-white rounded-lg">Cerrar</button>
                    </div>
                </div>
                <div>
                    <div class="flex mt-2">
                        <input name="datos_mecanico" id="datos_mecanico" type="text"
                            class="block w-6/12  border border-gray-300 rounded-md shadow-sm">
                        <input name="mechanics_id" id="mechanics_id" type="hidden"
                            class="block w-full  border border-gray-300 rounded-md shadow-sm">
                        <button onclick="eliminarMecanico()" type="button"
                            class="px-4 py-2 bg-red-500 text-white rounded-lg mr-11">X</button>
                        <button onclick="mostrarModal()" type="button"
                            class="px-4 py-2 bg-green-500 text-white rounded-lg  whitespace-nowrap">Seleccionar
                            Mecánico</button>

                    </div>
                </div>
                <!-- Modal -->
                <div id="buscarProductosModal"
                    class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 p-4 hidden">
                    <div class="bg-white p-6 rounded-lg shadow-lg max-w-4xl w-full">
                        <h3 class="text-lg font-bold mb-4">Productos</h3>

                        <!-- Campo de búsqueda dentro del modal -->
                        <div class="mb-4 flex items-center ">
                            <div class="w-8/12">
                                <input type="text" placeholder="Buscar por nombre del producto..."
                                    class="w-full p-2 border rounded" id="searchProduct">
                            </div>
                            <div>
                                <button class="bg-blue-500 text-white px-4 py-2  rounded-md rounded-l-none mr-5"
                                    id="btnBuscarProduct">Buscar</button>
                            </div>
                            <div class="w-3/12">
                                <label for="almacen" class="block font-medium text-gray-700"></label>
                                <select id="almacen" name="almacen"
                                    class="block w-full p-2 border border-gray-300 rounded-md shadow-sm">
                                    <option value="todos">Todos</option>
                                    @foreach ($warehouses as $warehouse => $almacen)
                                        <option value="{{ $almacen->id }}">{{ $almacen->name }}</option>
                                    @endforeach
                                </select>

                            </div>

                        </div>

                        <!-- Tabla con los productos -->
                        <div class="overflow-x-auto overflow-y-auto h-80">
                            <table class="min-w-full table-auto text-xs">
                                <thead class="bg-gray-200 sticky top-0">
                                    <tr>
                                        <th class="px-2 py-1 border">Código</th>
                                        <th class="px-2 py-1 border">Descripción</th>
                                        <th class="px-2 py-1 border">Ubicación</th>
                                        <th class="px-2 py-1 border">Stock Actual</th>
                                        <th class="px-2 py-1 border">Stock Mínimo</th>
                                        <th class="px-2 py-1 border">Cantidad</th>
                                        <th class="px-2 py-1 border">Seleccionar Precio</th>
                                        <th class="px-2 py-1 border">Subtotal</th>
                                        <th class="px-2 py-1 border">Agregar</th>
                                    </tr>
                                </thead>
                                <tbody id="productTable">
                                    <!-- Productos generados dinámicamente -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Botón de cerrar modal -->
                        <button class="mt-4 bg-red-500 text-white px-4 py-2 rounded"
                            onclick="closeModal('buscarProductosModal')">Cerrar</button>
                    </div>
                </div>
            </div>
            <!-- Detalle del Pedido -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-lg font-bold mb-4">Documento</h2>
                <div>
                    <label class="font-bold">Empresa </label>
                    <!-- Se agrega id para capturar el valor -->
                    <select id="companies_id" class="w-full p-2 border rounded">
                        <option value="">Seleccione</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->razon_social }} - {{ $company->ruc }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="font-bold">Tipo pago</label>
                    <!-- Se agrega id para capturar el valor -->
                    <select id="paymentType" class="w-full p-2 border rounded">
                        <option value="">Seleccione</option>
                        @foreach ($paymentsType as $payment)
                            <option value="{{ $payment->id }}">{{ $payment->name }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Campos adicionales ocultos -->
                <div id="creditFields" class="mt-3 hidden">
                    <label for="nro_dias">Número de días:</label>
                    <input type="number" id="nro_dias" class="w-full p-2 border rounded" min="1">

                    <label for="fecha_vencimiento" class="mt-2">Fecha de vencimiento:</label>
                    <input type="date" id="fecha_vencimiento" class="w-full p-2 border rounded">
                </div>
                <div class="mt-3" id="paymentMethodContainer1">
                    <label class="font-bold">Metodo pago</label>
                    <!-- Se agrega id para capturar el valor -->
                    <select id="paymentMethod1" class="w-full p-2 border rounded">
                        <option value="">Seleccione</option>
                        @foreach ($paymentsMethod as $payment)
                            <option value="{{ $payment->id }}">{{ $payment->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mt-2" id="paymentMethodContainer2">
                    <input type="checkbox" id="togglePaymentFields" class="mr-2">
                    <label for="togglePaymentFields">Agregar método de pago y monto</label>
                </div>
                <div id="paymentFieldsContainer" class="mt-2 hidden">
                    <div>
                        <label class="font-bold">Método de pago</label>
                        <select id="paymentMethod2" class="w-full p-2 border rounded">
                            <option value="">Seleccione</option>
                            @foreach ($paymentsMethod as $payment)
                                <option value="{{ $payment->id }}">{{ $payment->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mt-2">
                        <label class="font-bold">Monto a pagar</label>
                        <input type="number" id="paymentAmount2" class="w-full p-2 border rounded"
                            placeholder="Ingrese el monto">
                    </div>
                </div>
                <div>
                    <label class="font-bold">Tipo de documento</label>
                    <!-- Se agrega id para capturar el valor -->
                    <select id="documentType" class="w-full p-2 border rounded">
                        <option value="">Seleccione</option>
                        @foreach ($documentTypes as $documentType)
                            <option value="{{ $documentType->id }}">{{ $documentType->name }}</option>
                        @endforeach
                    </select>
                </div>
                <label>Fecha</label>
                <!-- Se agrega id para la fecha -->
                <input type="date" id="orderDate" value="{{ date('Y-m-d') }}"
                    class="w-full p-2 border rounded mb-4">
                <label>Moneda</label>
                <!-- Si la moneda es fija, también se puede capturar -->
                <input type="text" id="orderCurrency" value="SOLES" class="w-full p-2 border rounded mb-4">
                <!-- Subtotal -->
                <div class="bg-gray-200 text-gray-800 p-1 rounded text-center text-sm font-bold mb-2">
                    Subtotal: <span id="subtotalAmount">S/ 0.00</span>
                </div>
                <!-- IGV (18%) -->
                <div class="bg-gray-200 text-gray-800 p-1 rounded text-center text-sm font-bold mb-2">
                    IGV (18%): <span id="igvAmount">S/ 0.00</span>
                </div>

                <div class="bg-indigo-500 text-white p-1 rounded text-center text-sm font-bold" id="totalAmount">
                    S/ 0.00
                </div>
                <div class="mt-4">
                    <!-- Botón para guardar la orden -->
                    <button class="bg-blue-500 text-white p-2 rounded" onclick="saveSales()">Guardar</button>
                </div>
            </div>
        </div>

        <!-- Tabla de Productos (Detalle del Pedido) -->
        <div class="mt-6 bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-bold mb-4">Producto</h2>
            <div class="mb-4 flex items-center justify-end ">
                <div class="w-5/12">
                    <input type="text" placeholder="Buscar por nombre del producto..."
                        class="w-full p-2 border rounded" id="searchProductList">
                </div>
            </div>

            <table class="w-full border-collapse border border-gray-300" id="orderTable">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border p-2">Item</th>
                        <th class="border p-2">Producto</th>
                        <th class="border p-2">Cantidad</th>
                        <th class="border p-2">P. Unit.</th>
                        <th class="border p-2">T. Precio</th>
                        <th class="border p-2">Parcial</th>
                        <th class="border p-2">Acciones</th>
                    </tr>
                </thead>
                <tbody id="orderTableBody">
                    <tr id="emptyRow">
                        <td class="border p-2 text-center" colspan="7">No hay productos agregados</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Tabla para listar servicios -->
        <div class="mt-5">
            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border border-gray-300 px-4 py-2">Servicio</th>
                        <th class="border border-gray-300 px-4 py-2">Precio</th>
                        <th class="border border-gray-300 px-4 py-2">Acciones</th>
                    </tr>
                </thead>
                <tbody id="serviceList">
                    <!-- Aquí se agregarán los servicios -->
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>

<script>
    let services = [];
    let quotationItems = [];
    const searchInput = document.getElementById("searchProduct");
    let orderCount = 0; // para numerar los ítems
    const totalAmountEl = document.getElementById("totalAmount");
    const orderTableBody = document.getElementById("orderTableBody");
    let payments = [];
    // credito y contado
    document.getElementById("paymentType").addEventListener("change", function() {
        let selectedValue = this.value;
        let creditFields = document.getElementById("creditFields");
        let daysInput = document.getElementById("nro_dias");
        let dueDateInput = document.getElementById("fecha_vencimiento");
        let paymentFieldsContainer = document.getElementById("paymentFieldsContainer");
        let paymentMethodContainer1 = document.getElementById("paymentMethodContainer1");
        let paymentMethodContainer2 = document.getElementById("paymentMethodContainer2");
        if (selectedValue === "2") {
            // Si es crédito, mostrar campos de número de días y fecha de vencimiento
            creditFields.classList.remove("hidden");
            paymentFieldsContainer.classList.add("hidden"); // Ocultar método de pago
            paymentMethodContainer1.classList.add("hidden");
            paymentMethodContainer2.classList.add("hidden");
            // Calcular fecha de vencimiento al cambiar el número de días
            daysInput.addEventListener("input", function() {
                let days = parseInt(this.value, 10);
                if (!isNaN(days) && days > 0) {
                    let today = new Date();
                    today.setDate(today.getDate() + days);
                    dueDateInput.value = today.toISOString().split("T")[0]; // Formato YYYY-MM-DD
                } else {
                    dueDateInput.value = ""; // Vaciar si el valor no es válido
                }
            });
            const container = document.getElementById('paymentFieldsContainer');

            payments = [];

            container.style.display = 'none';
            document.getElementById("togglePaymentFields").checked = false;
            document.getElementById("paymentMethod2").value = "";
            document.getElementById("paymentAmount2").value = "";
            document.getElementById("paymentMethod1").value = "";
            document.getElementById("paymentAmount1").value = "";

        } else if (selectedValue === "1") {
            // Si es contado, mostrar el método de pago y ocultar crédito
            creditFields.classList.add("hidden");
            paymentMethodContainer1.classList.remove("hidden");
            paymentMethodContainer2.classList.remove("hidden");
            let togglePaymentFields = document.getElementById("togglePaymentFields").checked;
            if (togglePaymentFields) {
                paymentFieldsContainer.classList.remove("hidden");
                document.getElementById('paymentFieldsContainer').style.display = 'block';

            }
        } else {
            // Si no ha seleccionado nada, ocultar ambos
            creditFields.classList.add("hidden");
        }

        // Reiniciar valores si cambia de opción
        if (selectedValue !== "2") {
            daysInput.value = "";
            dueDateInput.value = "";
        }
    });
    //agregando buscador 
    document.getElementById('searchProductList').addEventListener('input', function() {
        let searchProductList = document.getElementById('searchProductList').value;
        let filteredItems = quotationItems.filter(item =>
            item.description.toLowerCase().includes(searchProductList.toLowerCase())
        );
        console.log(searchProductList, "searchProductList")
        console.log(filteredItems, "filteredItems")
        console.log(quotationItems, "quotationItems")
        mostrarProductos(filteredItems);
    })

    function mostrarProductos(items) {
        let productListContainer = document.getElementById('orderTableBody');
        productListContainer.innerHTML = ''; // Limpia la tabla

        if (items.length === 0) {
            productListContainer.innerHTML = `
            <tr id="emptyRow">
                <td colspan="7" class="text-center p-2">No hay productos disponibles</td>
            </tr>
        `;
            return;
        }

        items.forEach(product => {
            addProductTo(product); // Usa la misma función para crear filas
        });
    }
    //metodo d epago 
    document.getElementById('togglePaymentFields').addEventListener('change', function() {
        const container = document.getElementById('paymentFieldsContainer');
        container.style.display = this.checked ? 'block' : 'none';
    });

    // metodos de pago 
    function salePaymentMethods() {
        payments = [];
        let paymentMethod1 = document.getElementById('paymentMethod1').value;
        const totalAmountDiv = document.getElementById('totalAmount');
        let text = totalAmountDiv.textContent.trim();
        let paymentAmount1 = parseFloat(text.replace('S/', '').trim()) || 0;
        let paymentAmount2 = 0;
        if (document.getElementById('togglePaymentFields').checked) {
            let paymentMethod2 = document.getElementById('paymentMethod2').value;
            paymentAmount2 = parseFloat(document.getElementById('paymentAmount2').value) || 0;

            if (paymentMethod2 && paymentAmount2 > 0) {
                payments.push({
                    payment_method_id: paymentMethod2,
                    amount: paymentAmount2,
                    order: 2
                });
            }
        } else {
            document.getElementById('paymentMethod2').value = '';
            document.getElementById('paymentAmount2').value = '';
        }

        if (paymentMethod1) {
            payments.push({
                payment_method_id: paymentMethod1,
                amount: paymentAmount1 - paymentAmount2,
                order: 1
            });
        }
    }
    // agregar mecanico
    function seleccionarMecanico(id, datos) {
        document.getElementById('mechanics_id').value = id;
        document.getElementById('datos_mecanico').value = datos;
    }

    function eliminarMecanico() {
        document.getElementById('mechanics_id').value = '';
        document.getElementById('datos_mecanico').value = '';
    }

    function mostrarModal() {
        document.getElementById('modalMecanicos').classList.remove('hidden');
        fetch("{{ route('mecanicosDisponibles') }}")
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
    // BUSCADOR DEPARTAMENTO PROVINCIA DISTRITO
    document.getElementById('regions_id').addEventListener('change', function() {
        const regionId = this.value;
        if (regionId !== 'Seleccione un Departamento') {
            fetchProvinces(regionId);
        } else {
            clearSelect('provinces_id');
            clearSelect('districts_id');
        }
    });
    document.getElementById('provinces_id').addEventListener('change', function() {
        const provinceId = this.value;
        if (provinceId !== 'todos') {
            fetchDistricts(provinceId);
        } else {
            clearSelect('districts_id');
        }
    });

    function fetchProvinces(regionId) {
        fetch(`/api/provinces/${regionId}`)
            .then(response => response.json())
            .then(data => {
                const provinceSelect = document.getElementById('provinces_id');
                provinceSelect.removeAttribute(
                    'disabled');
                clearSelect('districts_id');
                updateSelectOptions('provinces_id', data.provinces);
                console.log('data.provinces', data.provinces);
            })
            .catch(error => console.error('Error fetching provinces:', error));
    }

    function updateSelectOptions(selectId, options) {
        const select = document.getElementById(selectId);
        select.innerHTML = '<option value="todos">Seleccione una opción</option>';
        options.forEach(option => {
            const opt = document.createElement('option');
            opt.value = option.id;
            opt.textContent = option.name;
            select.appendChild(opt);
        });
    }

    function clearSelect(selectId) {
        const select = document.getElementById(selectId);
        select.innerHTML = '<option value="todos">Seleccione una opción</option>';
    }

    function fetchDistricts(provinceId) {
        fetch(`/api/districts/${provinceId}`)
            .then(response => response.json())
            .then(data => {
                const districtSelect = document.getElementById('districts_id');
                districtSelect.removeAttribute(
                    'disabled');
                updateSelectOptions('districts_id', data.districts);
            })
            .catch(error => console.error('Error fetching districts:', error));
    }

    /// AGREGANDO SERVICIOS
    document.getElementById("addService").addEventListener("click", function() {
        let serviceName = document.getElementById("service").value.trim();
        let servicePrice = document.getElementById("service_price").value.trim();

        if (serviceName === "" || servicePrice === "") {
            alert("Por favor, complete todos los campos.");
            return;
        }

        let newService = {
            id: Date.now(), // Genera un ID único basado en el tiempo
            name: serviceName,
            price: servicePrice
        };

        services.push(newService); // Agregar al array
        updateTable(); // Refrescar la tabla
        updateInformationCalculos();

        // Limpiar inputs
        document.getElementById("service").value = "";
        document.getElementById("service_price").value = "";
    });
    // modal
    function openModal(modalId, callback) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove("hidden");
            if (callback) callback();
        }
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add("hidden");
        }
    }


    function fetchProducts() {
        const almacen = document.getElementById("almacen").value;
        const search = searchInput.value;
        // Realizar la solicitud a la API
        fetch('/api/product?almacen=' + almacen + '&search=' + search)
            .then(res => res.json())
            .then(data => {
                let allProducts = data
                    .filter(product => !quotationItems.some(item => item.item_id === product.id))
                    .map(product => ({
                        ...product,
                        selectedQuantity: 1,
                        selectedPrice: ""
                    }));
                products = [...allProducts];
                console.log(products)
                renderProducts(products);
            })
            .catch(error => console.error('Error:', error));
    }
    // productos
    document.getElementById("buscarProductos").addEventListener("click", () => {
        openModal("buscarProductosModal", () => {
            fetchProducts();
        });
    });
    // // Renderiza la lista de productos en el modal
    function renderProducts(productList) {
        productTable.innerHTML = "";
        productList.forEach(product => {
            const row = document.createElement("tr");
            row.innerHTML = `
                    <td class="px-2 py-1 border">${product.code_sku}</td>
                    <td class="px-2 py-1 border">${product.description}</td>
                    <td class="px-2 py-1 border">${product.location}</td>
                    <td class="px-2 py-1 border">${product.stock.quantity}</td>
                    <td class="px-2 py-1 border">${product.stock.minimum_stock}</td>
                    <td class="px-2 py-1 border">
                        <input type="number" class="p-2 border rounded data-quantity-id-${product.id} value="1" min="1" max="${product.stock.quantity}" data-product-id="${product.id}">
                    </td>
                    <td class="px-2 py-1 border">
                        <select class="p-2 border rounded data-price-id-${product.id}" data-product-id="${product.id}">
                            <option value="">Seleccionar precio</option>
                            ${product.prices.map(price => `<option value="${price.price}" data-price-id="${price.id}">${price.type} - ${price.price}</option>`).join('')}
                        </select>
                    </td>
                    <td class="px-2 py-1 border subtotal-cell" id="subtotal-${product.id}">0</td>
                    <td class="px-2 py-1 border">
                        <button class="bg-blue-500 text-white px-3 py-1 rounded" data-product-id="${product.id}"  onclick="agregarProducto(${product.id})">Agregar</button>
                    </td>
                `;
            productTable.appendChild(row);
            addSubtotalEvents(row, product.id);

        });
    }
    // calcular subtotal de productos en el modal y tabla
    function addSubtotalEvents(row, productId) {
        const quantity = row.querySelector(`.data-quantity-id-${productId}`);
        const priceSelect = row.querySelector(`.data-price-id-${productId}`);

        quantity.addEventListener("input", () => updateModalSubtotal(productId, quantity, priceSelect));
        priceSelect.addEventListener("change", () => updateModalSubtotal(productId, quantity, priceSelect));
    }

    function updateModalSubtotal(productId, quantityInput, priceSelect) {
        const quantity = parseInt(quantityInput.value) || 0;
        const price = parseFloat(priceSelect.value) || 0;
        const subtotal = quantity * price;
        const subtotalElement = document.getElementById(`subtotal-${productId}`);

        if (subtotalElement) {
            subtotalElement.textContent = subtotal.toFixed(2);
        }
    }

    //agregar productos
    function agregarProducto(productId) {
        const quantity = document.querySelector(`.data-quantity-id-${productId}`).value;
        const priceSelect = document.querySelector(`.data-price-id-${productId}`);
        const selectOptionPriceId = priceSelect.options[priceSelect.selectedIndex];
        const selectedPriceId = selectOptionPriceId.getAttribute("data-price-id");
        const response = products.find(product => product.id == productId);
        if (response) {
            const product = {
                item_id: productId,
                description: response.description,
                priceId: selectedPriceId,
                unit_price: priceSelect.value,
                prices: response.prices,
                quantity: quantity,
                maximum_stock: response.stock.quantity,
            }
            const productCopy = {
                ...product
            };
            delete productCopy.prices;
            quotationItems.push(productCopy);
            addProductTo(product);
            updateInformationCalculos();

        }
        products = products.filter(product => product.id != productId)
        const row = priceSelect.closest("tr");
        console.log(quotationItems);
        row.style.display = "none";
    }

    function addProductTo(product) {
        const emptyRow = document.getElementById("emptyRow");
        if (emptyRow) {
            emptyRow.remove();
        }
        orderCount++;
        const orderRow = document.createElement("tr");
        orderRow.setAttribute("data-product-id", product.item_id);
        orderRow.innerHTML = `
            <td class="border p-2 text-center">${orderCount}</td>
            <td class="border p-2">${product.description}</td>
            <td class="border p-2">
                <input type="number" class="p-2 border rounded data-quantity-value-${product.item_id}" onchange="updatePriceAndTotal(${product.item_id})"
                       value="${product.quantity}" 
                       max="${product.maximum_stock}"
                       min="1"
                       style="width: 60px;">
            </td>
            <td class="border p-2">
                <select class="p-2 border rounded data-price-select-${product.item_id}" 
                        style="width: 120px;" onchange="updatePriceAndTotal(${product.item_id})">
                    <option value="">Seleccionar precio</option>
                    ${product.prices.map(precio => `
                        <option value="${precio.price}" 
                                data-price-id="${precio.id}" 
                                ${precio.id == product.priceId ? 'selected' : ''}>
                            ${precio.type} - ${precio.price}
                        </option>`).join('')}
                </select>
            </td>
            <td class="border p-2 data-price-value-${product.item_id}" style="text-align: right;">${product.unit_price}</td>
            <td class="border p-2 data-total-value-${product.item_id}" style="text-align: right;">${product.unit_price * product.quantity}</td>
            <td class="border p-2 text-center">
                <button class="bg-red-500 text-white px-2 py-1 rounded eliminar-btn" 
                       onclick="deleteProduct(${product.item_id})">
                    Eliminar
                </button>
            </td>
        `;
        orderTableBody.appendChild(orderRow);
    }
    // calcular subtotal igv y total
    function updateInformationCalculos() {
        let totalAmount = 0;
        let igvAmount = 0;
        let subtotalAmount = 0;
        quotationItems.forEach(item => {
            totalAmount += item.quantity * item.unit_price;
        })
        services.forEach(item => {
            totalAmount += parseFloat(item.price);
        })
        igvAmount = totalAmount * 0.18;
        subtotalAmount = totalAmount - igvAmount;
        document.getElementById("subtotalAmount").textContent = "S/ " + subtotalAmount.toFixed(2);
        document.getElementById("igvAmount").textContent = "S/ " + igvAmount.toFixed(2);
        document.getElementById("totalAmount").textContent = "S/ " + totalAmount.toFixed(2);
    }


    document.getElementById("btnBuscarProduct").addEventListener("click", () => {
        fetchProducts();
    })

    function updatePriceAndTotal(productId) {
        const quantityInput = document.querySelector(`.data-quantity-value-${productId}`);
        const priceSelect = document.querySelector(`.data-price-select-${productId}`);
        const priceValueCell = document.querySelector(`.data-price-value-${productId}`);
        const totalValueCell = document.querySelector(`.data-total-value-${productId}`);
        const quantity = parseFloat(quantityInput.value) || 0;
        const selectedOption = priceSelect.options[priceSelect.selectedIndex];
        const price = parseFloat(selectedOption.value) || 0;
        // precio y total en la tabla
        priceValueCell.textContent = price.toFixed(2);
        totalValueCell.textContent = (price * quantity).toFixed(2);

        quotationItems.forEach(item => {
            if (item.item_id == productId) {
                item.quantity = quantity;
                item.unit_price = price;
                item.priceId = parseFloat(selectedOption.dataset.priceId);
            }
        })
        updateInformationCalculos();

    }
    // eliminar producto
    function deleteProduct(productId) {
        quotationItems = quotationItems.filter(product => product.item_id != productId);
        const row = document.querySelector(`tr[data-product-id="${productId}"]`);
        if (row) {
            row.remove();
        }
        updateInformationCalculos();
    }


    // // 🔹 Función para construir el objeto de orden
    function buildOrderData() {
        return {
            ...getCustomerData(),
            products: quotationItems,
            services: services
        };
    }
    // // Extraer los datos del cliente
    function getCustomerData() {
        return {
            customer_dni: document.getElementById("dni_personal").value.trim(),
            customer_names_surnames: document.getElementById("nombres_apellidos").value.trim(),
            customer_address: document.getElementById("direccion").value.trim(),
            districts_id: document.getElementById("districts_id").value,
            mechanics_id: document.getElementById("mechanics_id").value,
            payments_id: document.getElementById("paymentType").value,
            order_date: document.getElementById("orderDate").value,
            currency: document.getElementById("orderCurrency").value,
            document_type_id: document.getElementById("documentType").value,
            companies_id: document.getElementById("companies_id").value,
            nro_dias: document.getElementById("nro_dias").value,
            fecha_vencimiento: document.getElementById("fecha_vencimiento").value,
            igv: parseAmount("igvAmount"),
            total: parseAmount("totalAmount")
        };
    }
    // // Convierte valores monetarios a números
    function parseAmount(elementId) {
        return parseFloat(document.getElementById(elementId).textContent.replace("S/ ", "")) || 0;
    }
    // guardar cotizacion 
    async function saveSales() {
        try {
            const orderData = buildOrderData();
            salePaymentMethods()

            const response = await fetch('{{ route('sales.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    ...orderData,
                    payments
                })
            });

            if (!response.ok) throw new Error("Error en la petición");
            const data = await response.json();
            alert("La Venta se ha guardado correctamente.");
        } catch (error) {
            console.error("Error al guardar la orden:", error);
            alert("Error al guardar la orden.");
        }
    }




    // Función para actualizar la tabla
    function updateTable() {
        let tableBody = document.getElementById("serviceList");
        tableBody.innerHTML = ""; // Limpiar tabla antes de actualizar

        services.forEach(service => {
            let row = document.createElement("tr");
            row.innerHTML = `
                    <td class="border border-gray-300 px-4 py-2">${service.name}</td>
                    <td class="border border-gray-300 px-4 py-2">${service.price}</td>
                    <td class="border border-gray-300 px-4 py-2">
                        <button class="bg-red-500 text-white px-2 py-1 rounded-md" onclick="deleteService(${service.id})">Eliminar</button>
                    </td>
                `;
            tableBody.appendChild(row);
        });
    }

    // Función para eliminar un servicio
    function deleteService(id) {
        services = services.filter(service => service.id !== id); // Elimina del array
        updateTable(); // Refrescar la tabla
        updateInformationCalculos();
    }
    // BUSQUEDA DE SERVICIOS
    document.getElementById("service").addEventListener("input", function() {
        const inputValue = this.value.trim();
        const suggestionsList = document.getElementById("serviceSuggestions");
        const dropdown = document.getElementById("serviceDropdown");

        if (inputValue === "") {
            suggestionsList.innerHTML = "";
            dropdown.classList.add("hidden");
            return;
        }

        fetch(`/api/services?query=${inputValue}`)
            .then(response => response.json())
            .then(data => {
                suggestionsList.innerHTML = "";

                if (data.length > 0) {
                    data.forEach(service => {
                        const item = document.createElement("li");
                        item.textContent = `${service.name} - S/. ${service.default_price}`;
                        item.classList.add("cursor-pointer", "p-2", "hover:bg-gray-100");

                        item.addEventListener("click", function() {
                            document.getElementById("service").value = service.name;
                            document.getElementById("service_price").value = service
                                .default_price;
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
    // api dni
    const inputDni = document.getElementById('dni_personal');
    const token =
        'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6InN5c3RlbWNyYWZ0LnBlQGdtYWlsLmNvbSJ9.yuNS5hRaC0hCwymX_PjXRoSZJWLNNBeOdlLRSUGlHGA';
    // Función para buscar DNI
    function buscarDNI(dni) {
        if (dni.length === 8) {
            fetch(`https://dniruc.apisperu.com/api/v1/dni/${dni}?token=${token}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la solicitud');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log(data);
                    document.getElementById('nombres_apellidos').value = data.apellidoPaterno + ' ' +
                        data.apellidoMaterno + ' ' + data.nombres;
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('No se pudo encontrar el DNI');
                });
        }
    }

    // Evento cuando el usuario escribe en el campo DNI
    inputDni.addEventListener('input', () => {
        buscarDNI(inputDni.value);
    });
</script>
