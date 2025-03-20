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

                <!-- Botón que abre el modal -->
                <button class="bg-yellow-400 p-2 rounded" id="openModal">Consultar Productos</button>
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
                <div id="modal"
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
                        <button class="mt-4 bg-red-500 text-white px-4 py-2 rounded" id="closeModal">Cerrar</button>
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
                    <button id="saveOrder" class="bg-blue-500 text-white p-2 rounded">Guardar</button>
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
    const totalAmountEl = document.getElementById("totalAmount");
    let orderCount = 0; // para numerar los ítems
    let allProducts = []; // Todos los productos obtenidos de la API
    let products = []; // Productos disponibles en el modal
    const openModalBtn = document.getElementById("openModal");
    const closeModalBtn = document.getElementById("closeModal");
    // Variables del modal y productos
    const modal = document.getElementById("modal");
    const searchInput = document.getElementById("searchProduct");
    const btnBuscarProduct = document.getElementById("btnBuscarProduct");
    const productTable = document.getElementById("productTable");

    // Variables de la tabla de pedido
    const orderTableBody = document.getElementById("orderTableBody");

    // Variables de los datos de la orden
    const paymentTypeSelect = document.getElementById("paymentType");
    const orderDateInput = document.getElementById("orderDate");
    const orderCurrencyInput = document.getElementById("orderCurrency");
    const saveOrderBtn = document.getElementById("saveOrder");
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

        // Limpiar inputs
        document.getElementById("service").value = "";
        document.getElementById("service_price").value = "";
        updateTotalAmount();
    });

    function addProductToOrder(product) {
        // Eliminar fila "No hay productos agregados" si existe
        const emptyRow = document.getElementById("emptyRow");
        if (emptyRow) {
            emptyRow.remove();
        }
        orderCount++;
        const orderRow = document.createElement("tr");
        orderRow.setAttribute("data-product-id", product.id);
        orderRow.innerHTML = `
                <td class="border p-2 text-center">${orderCount}</td>
                <td class="border p-2">${product.description}</td>
                <td class="border p-2">
                    <input type="number" class="p-2 border rounded order-quantity" value="${product.selectedQuantity}" min="1" max="${product.stock.quantity}" style="width: 60px;">
                </td>
                <td class="border p-2">
                    <select class="p-2 border rounded order-price" data-product-id="${product.id}" style="width: 120px;">
                        <option value="">Seleccionar precio</option>
                        ${product.prices.map(price => `<option value="${price.price}" ${price.price == product.selectedPrice ? 'selected' : ''}>${price.type} - ${price.price}</option>`).join('')}
                    </select>
                </td>
                <td class="border p-2 order-total" style="text-align: right;">0.00</td>
                <td class="border p-2 order-subtotal" style="text-align: right;">0.00</td>
                <td class="border p-2 text-center">
                    <button class="bg-red-500 text-white px-2 py-1 rounded eliminar-btn" data-product-id="${product.id}">Eliminar</button>
                </td>
            `;
        orderTableBody.appendChild(orderRow);
        updateOrderRow(orderRow);
        updateTotalAmount();

        // Eventos para edición en la tabla de pedido
        const qtyInput = orderRow.querySelector(".order-quantity");
        const priceSelect = orderRow.querySelector(".order-price");
        qtyInput.addEventListener("input", () => {
            updateOrderRow(orderRow);
            updateTotalAmount();
        });
        priceSelect.addEventListener("change", () => {
            updateOrderRow(orderRow);
            updateTotalAmount();
        });

        // Evento para eliminar el producto de la orden
        const eliminarBtn = orderRow.querySelector(".eliminar-btn");
        eliminarBtn.addEventListener("click", () => {
            const prodId = eliminarBtn.getAttribute("data-product-id");
            // Eliminar la fila de la orden
            orderRow.remove();
            updateTotalAmount();
            // Reinsertar el producto eliminado en el listado del modal
            const productToRestore = allProducts.find(p => p.id == prodId);
            if (productToRestore) {
                if (!products.find(p => p.id == prodId)) {
                    products.push(productToRestore);
                    products.sort((a, b) => a.id - b.id);
                    renderProducts(products);
                }
            }
            // Si ya no hay filas en la orden, mostrar la fila vacía
            if (orderTableBody.querySelectorAll("tr[data-product-id]").length === 0) {
                orderCount = 0;
                orderTableBody.innerHTML = `<tr id="emptyRow">
                        <td class="border p-2 text-center" colspan="7">No hay productos agregados</td>
                    </tr>`;
            }
        });
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
        updateTotalAmount();
    }
    // FIN DE AGREGAR SERVICIO
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
    // Actualiza el total general de la orden
    function updateTotalAmount() {
        let subtotalTotal = 0;
        let total = 0;
        services.forEach(service => {
            subtotalTotal += parseFloat(service.price);
        });
        const rows = orderTableBody.querySelectorAll("tr[data-product-id]");
        rows.forEach(row => {
            const subtotal = parseFloat(row.querySelector(".order-subtotal").textContent) || 0;
            subtotalTotal += subtotal;
        });
        let baseSubtotal = subtotalTotal / 1.18;
        let igv = subtotalTotal - baseSubtotal;
        document.getElementById("subtotalAmount").textContent = "S/ " + baseSubtotal.toFixed(2);
        document.getElementById("igvAmount").textContent = "S/ " + igv.toFixed(2);
        totalAmountEl.textContent = "S/ " + subtotalTotal.toFixed(2);
    }
    // Actualiza el subtotal de un renglón en la tabla de pedido
    function updateOrderRow(row) {
        const qty = parseFloat(row.querySelector(".order-quantity").value) || 0;
        const price = parseFloat(row.querySelector(".order-price").value) || 0;
        const subtotal = qty * price;
        row.querySelector(".order-total").textContent = subtotal.toFixed(2);
        row.querySelector(".order-subtotal").textContent = subtotal.toFixed(2);
    }
    // FIN DE BUSQUEDA DE SERVICIOS
    // FIN DE GUARDANDO TODOS LOS SERVICIOS EN UN ARRAY
    // Abrir y cerrar modal
    openModalBtn.addEventListener("click", () => {
        modal.classList.remove("hidden");
        // Si no hay productos cargados aún, se consultan de la API
        if (allProducts.length === 0) {
            fetchProducts();
        } else {
            renderProducts(products);
        }
    });
    closeModalBtn.addEventListener("click", () => {
        modal.classList.add("hidden");
    });
    btnBuscarProduct.addEventListener("click", () => {
        fetchProducts();
    })

    function fetchProducts() {
        const almacen = document.getElementById("almacen").value;
        const search = searchInput.value;
        // Realizar la solicitud a la API
        fetch('/api/product?almacen=' + almacen + '&search=' + search)
            .then(res => res.json())
            .then(data => {
                allProducts = data.map(product => ({
                    ...product,
                    selectedQuantity: 1,
                    selectedPrice: ""
                }));
                // Inicialmente, todos los productos están disponibles en el modal
                products = [...allProducts];
                renderProducts(products);
            })
            .catch(error => console.error('Error:', error));
    }
    // Filtrar productos según el término de búsqueda
    function filterProducts(query) {
        const filtered = products.filter(product =>
            product.description.toLowerCase().includes(query.toLowerCase())
        );
        renderProducts(filtered);
    }
    // Renderiza la lista de productos en el modal
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
                        <input type="number" class="p-2 border rounded quantity-input" value="1" min="1" max="${product.stock.quantity}" data-product-id="${product.id}">
                    </td>
                    <td class="px-2 py-1 border">
                        <select class="p-2 border rounded price-select" data-product-id="${product.id}">
                            <option value="">Seleccionar precio</option>
                            ${product.prices.map(price => `<option value="${price.price}">${price.type} - ${price.price}</option>`).join('')}
                        </select>
                    </td>
                    <td class="px-2 py-1 border subtotal-cell" id="subtotal-${product.id}">0</td>
                    <td class="px-2 py-1 border">
                        <button class="bg-blue-500 text-white px-3 py-1 rounded agregar-btn" data-product-id="${product.id}">Agregar</button>
                    </td>
                `;
            productTable.appendChild(row);

            // Calcular subtotal en el modal
            const qtyInput = row.querySelector(".quantity-input");
            const priceSelect = row.querySelector(".price-select");
            qtyInput.addEventListener("input", () => updateModalSubtotal(product.id, qtyInput,
                priceSelect));
            priceSelect.addEventListener("change", () => updateModalSubtotal(product.id, qtyInput,
                priceSelect));
        });

        // Asignar evento "Agregar" a cada botón
        const agregarButtons = document.querySelectorAll(".agregar-btn");
        agregarButtons.forEach(btn => {
            btn.addEventListener("click", () => {
                const prodId = btn.getAttribute("data-product-id");
                const product = products.find(p => p.id == prodId);
                if (product) {
                    const row = btn.closest("tr");
                    const qtyInput = row.querySelector(".quantity-input");
                    const priceSelect = row.querySelector(".price-select");
                    const quantity = parseInt(qtyInput.value) || 1;
                    const price = parseFloat(priceSelect.value) || 0;
                    product.selectedQuantity = quantity;
                    product.selectedPrice = price;
                    addProductToOrder(product);

                    // Remover el producto agregado del listado para evitar duplicados
                    products = products.filter(p => p.id != prodId);
                    renderProducts(products);
                }
            });
        });
    }
    // Calcula y actualiza el subtotal en el modal para un producto
    function updateModalSubtotal(productId, qtyInput, priceSelect) {
        const quantity = parseInt(qtyInput.value) || 0;
        const price = parseFloat(priceSelect.value) || 0;
        const subtotal = quantity * price;
        const subtotalEl = document.getElementById(`subtotal-${productId}`);
        if (subtotalEl) {
            subtotalEl.textContent = subtotal.toFixed(2);
        }
    }
    // Guardar la orden: recopilar datos y enviar mediante fetch
    saveOrderBtn.addEventListener("click", () => {
        // Recopilar datos de la orden
        const customer_dni = document.getElementById("dni_personal").value;
        const customer_names_surnames = document.getElementById("nombres_apellidos").value;
        const paymentType = paymentTypeSelect.value;
        const orderDate = orderDateInput.value;
        const currency = orderCurrencyInput.value;
        const igv = parseFloat(document.getElementById("igvAmount").textContent.replace("S/ ", "")) || 0;
        const totalText = totalAmountEl.textContent.replace("S/ ", "");
        const total = parseFloat(totalText) || 0;
        const document_type_id = document.getElementById("documentType").value;

        // Recopilar los productos de la orden
        const orderRows = orderTableBody.querySelectorAll("tr[data-product-id]");
        let orderProducts = [];
        orderRows.forEach(row => {
            const productId = row.getAttribute("data-product-id");
            const quantity = parseFloat(row.querySelector(".order-quantity").value) || 0;
            const unitPrice = parseFloat(row.querySelector(".order-price").value) || 0;
            const subtotal = parseFloat(row.querySelector(".order-subtotal").textContent) ||
                0;
            orderProducts.push({
                product_id: productId,
                quantity,
                unit_price: unitPrice,
                subtotal
            });
        });

        // Construir el objeto a enviar
        const orderData = {
            customer_dni,
            customer_names_surnames,
            payment_type: paymentType,
            order_date: orderDate,
            currency,
            total,
            igv,
            document_type_id,
            products: orderProducts,
            services: services
        };

        // Enviar a "products.store" mediante fetch (POST)
        fetch('{{ route('sales.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(orderData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error("Error en la petición");
                }
                return response.json();
            })
            .then(data => {
                // Manejar la respuesta, por ejemplo, redireccionar o mostrar un mensaje
                console.log("Orden guardada:", data);
                alert("La orden se ha guardado correctamente.");
                location.reload();
            })
            .catch(error => {
                console.error("Error al guardar la orden:", error);
                alert("Error al guardar la orden.");
            });
    });

    document.addEventListener("DOMContentLoaded", () => {

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
