<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            cotizacion insertar
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

                <!-- Botón que abre el modal -->
                <button class="bg-yellow-400 p-2 rounded" id="buscarProductos">Consultar
                    Productos</button>
                <div class="relative">
                    <label for="service" class="block font-medium text-gray-700">Servicio</label>
                    <input type="text" id="service" name="service"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm" autocomplete="off">

                    <!-- Dropdown de Sugerencias -->
                    <div id="serviceDropdown"
                        class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden">
                        <ul id="serviceSuggestions" class="max-h-40 overflow-y-auto"></ul>
                    </div>
                </div>

                <div class="mt-3">
                    <label for="service_price" class="block font-medium text-gray-700">Precio del Servicio</label>
                    <input type="number" id="service_price" name="service_price"
                        class="block w-full mt-2 p-2 border border-gray-300 rounded-md shadow-sm">
                </div>

                <button type="button" id="addService" class="bg-blue-500 text-white px-4 py-2 mt-3 rounded-md">Agregar
                    Servicio</button>

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
                    <label class="font-bold">RUC </label>
                    <!-- Se agrega id para capturar el valor -->
                    <select id="ruc" class="w-full p-2 border rounded">
                        <option value="">Seleccione</option>
                        <option value="1">SAGA FALABELLA S A -20100128056 </option>
                        <option value="2">TURISMO TITANIC S.A -20301040301 </option>
                        <option value="3">Biker S.A -20606806184 </option>
                    </select>
                </div>
                <div>
                    <label class="font-bold">Tipo pago</label>
                    <!-- Se agrega id para capturar el valor -->
                    <select id="paymentType" class="w-full p-2 border rounded">
                        <option value="">Seleccione</option>
                        @foreach ($payments as $payment)
                            <option value="{{ $payment->id }}">{{ $payment->name }}</option>
                        @endforeach
                    </select>
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
                <input type="date" id="orderDate" value="{{ date('Y-m-d') }}" class="w-full p-2 border rounded mb-4">
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
                    <button class="bg-blue-500 text-white p-2 rounded" onclick="saveQuotation()">Guardar</button>
                </div>
            </div>
        </div>

        <!-- Tabla de Productos (Detalle del Pedido) -->
        <div class="mt-6 bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-bold mb-4">Producto</h2>
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
    let orderCount = 0; // para numerar los ítems
    const searchInput = document.getElementById("searchProduct");
    let products = []; // Productos disponibles en el modal
    let quotationItems = [];
    let orderTableBody = document.getElementById("orderTableBody");


    //SERVICIOS
    //  AGREGANDO SERVICIOS
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

        // Limpiar inputs
        document.getElementById("service").value = "";
        document.getElementById("service_price").value = "";
        updateTotalAmount();
    });

    // Función para actualizar la tabla de servicios
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
        updateTotalAmount();
    }

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
    // FIN DE SERVICIOS


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
    document.getElementById("buscarProductos").addEventListener("click", () => {
        openModal("buscarProductosModal", () => {
            fetchProducts();
        });
    });

    document.getElementById("btnBuscarProduct").addEventListener("click", () => {
        fetchProducts();
    })

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

    // // Renderiza la lista de productos en el modal
    function renderProducts(productList) {
        productTable.innerHTML = "";
        productList.forEach(product => {
            const row = document.createElement("tr");
            row.innerHTML = `
                    <td class="px-2 py-1 border">${product.code}</td>
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
    }

    // guardar cotizacion 
    async function saveQuotation() {
        try {
            const orderData = buildOrderData();

            const response = await fetch('{{ route('quotations.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(orderData)
            });

            if (!response.ok) throw new Error("Error en la petición");

            const data = await response.json();
            console.log("Orden guardada:", data);
            alert("La cotización se ha guardado correctamente.");
        } catch (error) {
            console.error("Error al guardar la orden:", error);
            alert("Error al guardar la orden.");
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
            quotationItems.push(product);
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
        igvAmount = totalAmount * 0.18;
        subtotalAmount = totalAmount - igvAmount;
        document.getElementById("subtotalAmount").textContent = "S/ " + subtotalAmount.toFixed(2);
        document.getElementById("igvAmount").textContent = "S/ " + igvAmount.toFixed(2);
        document.getElementById("totalAmount").textContent = "S/ " + totalAmount.toFixed(2);
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
    // fin de eliminar


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
            payment_method_id: document.getElementById("paymentType").value,
            order_date: document.getElementById("orderDate").value,
            currency: document.getElementById("orderCurrency").value,
            document_type: document.getElementById("documentType").value,
            igv: parseAmount("igvAmount"),
            total: parseAmount("totalAmount")
        };
    }
    // // Convierte valores monetarios a números
    function parseAmount(elementId) {
        return parseFloat(document.getElementById(elementId).textContent.replace("S/ ", "")) || 0;
    }
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
