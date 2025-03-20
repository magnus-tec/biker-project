<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            cotizacion EDTAR
        </h2>
    </x-slot>
    <div class="container mx-auto p-2 text-sm">
        <div class="grid grid-cols-3 gap-6">
            <!-- Formulario de Cliente -->
            <div class="col-span-2 bg-white p-6 rounded-lg shadow">
                <input type="hidden" id="quotationId" value="{{ $quotation->id }}">
                <h2 class="text-lg font-bold mb-4">Cliente</h2>
                <input type="text" id="dni_personal" placeholder="Ingrese Documento"
                    class="w-full p-2 border rounded mb-2">
                <input type="text" placeholder="Nombre del cliente" id="nombres_apellidos"
                    class="w-full p-2 border rounded mb-2">

                <!-- Botón que abre el modal -->
                <button class="bg-yellow-400 p-2 rounded" id="buscarProductos">Consultar Productos</button>
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
                            <option value="{{ $documentType->id }}">
                                {{ $documentType->name }}</option>
                        @endforeach
                    </select>
                </div>
                <label>Fecha</label>
                <!-- Se agrega id para la fecha -->
                <input type="date" id="orderDate" class="w-full p-2 border rounded mb-4">
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
                    <button id="save" class="bg-blue-500 text-white p-2 rounded">Guardar</button>
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
    let productTable = document.getElementById("productTable");
    // let orderTable = document.getElementById("orderTable");
    let orderCount = 0; // para numerar los ítems
    let allProducts = []; // Todos los productos obtenidos de la API
    const searchInput = document.getElementById("searchProduct");
    let products = []; // Productos disponibles en el modal
    let quotationItems = [];

    let orderTableBody = document.getElementById("orderTableBody");

    document.addEventListener("DOMContentLoaded", async function() {
        let quotationId = document.getElementById("quotationId").value;

        try {
            let response = await fetch(`{{ route('quotations.detallesQuotation', ':id') }}`.replace(':id',
                quotationId));
            if (!response.ok) throw new Error("Error al obtener la cotización");

            let responseQuotation = await response.json();
            console.log("log", responseQuotation)

            // Llenar los campos con los datos de la cotización
            document.getElementById("nombres_apellidos").value = responseQuotation.quotation
                .customer_names_surnames;
            document.getElementById("dni_personal").value = responseQuotation.quotation.customer_dni;
            console.log('responseQuotation', responseQuotation);
            document.getElementById("orderDate").value = responseQuotation.quotation.fecha_registro.split(
                " ")[0];
            document.getElementById("documentType").value = responseQuotation.quotation.document_type_id;
            // document.getElementById("paymentType").value = responseQuotation.quotation.document_type_id; GUARDAR ESTE DATO

            quotationItems = responseQuotation.quotation.quotation_items
                .filter(item => item.item_type === "App\\Models\\Product")
                .map(item => ({
                    item_id: item.item_id,
                    description: item.item.description,
                    priceId: item.product_prices_id,
                    unit_price: item.unit_price,
                    prices: item.prices,
                    quantity: item.quantity,
                }));
            addProductToForm();

            console.log('responseQuotation', responseQuotation);
            updateTotalAmount();
        } catch (error) {
            console.error("Error:", error);
        }
    });
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
    //  Guardar la cotizacion
    document.getElementById("save").addEventListener("click", async () => {
        try {
            let quotationId = document.getElementById("quotationId").value;
            const orderData = buildOrderData();

            const response = await fetch('{{ route('quotations.update', ':id') }}'.replace(':id',
                quotationId), {
                method: 'PUT',
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
    });

    // 🔹 Función para construir el objeto de orden
    function buildOrderData() {
        return {
            ...getCustomerData(),
            products: quotationItems,
            // services: services
        };
    }

    // Extraer los datos del cliente
    function getCustomerData() {
        return {
            customer_dni: document.getElementById("dni_personal").value.trim(),
            customer_names_surnames: document.getElementById("nombres_apellidos").value.trim(),
            payment_type: document.getElementById("paymentType").value,
            order_date: document.getElementById("orderDate").value,
            currency: document.getElementById("orderCurrency").value,
            document_type: document.getElementById("documentType").value,
            igv: parseAmount("igvAmount"),
            total: parseAmount("totalAmount")
        };
    }

    function parseAmount(elementId) {
        return parseFloat(document.getElementById(elementId).textContent.replace("S/ ", "")) || 0;
    }
    //  Extraer los productos de la tabla
    // function getOrderProducts() {
    // const selectElement = document.querySelector('.order-price');
    // const selectedOption = selectElement.options[selectElement.selectedIndex];
    // const selectedPriceId = selectedOption.getAttribute('data-price-id');
    // console.log(selectedPriceId);

    // return Array.from(document.querySelectorAll("#orderTableBody tr[data-product-id]")).map(row => ({
    //     product_id: row.getAttribute("data-product-id"),
    //     quantity: parseFloat(row.querySelector(".order-quantity").value) || 0,
    //     unit_price: parseFloat(row.querySelector(".order-price").value) || 0,
    //     subtotal: parseFloat(row.querySelector(".order-subtotal").textContent) || 0,
    //     priceId: selectedPriceId
    // }));
    // }

    function agregarProducto(btn) {
        const productoId = btn.getAttribute("data-product-id");
        const product = products.find(product => product.id == productoId);
        if (product) {
            const row = btn.closest("tr");
            const qtyInput = row.querySelector(".quantity-input");
            const priceSelect = row.querySelector(".price-select");
            const quantity = parseInt(qtyInput.value) || 1;
            const price = parseFloat(priceSelect.value) || 0;
            const selectedOption = priceSelect.options[priceSelect.selectedIndex];
            const priceId = selectedOption.getAttribute("data-price-id");
            const idPrice = row.getAttribute("data-product-id");
            product.selectedQuantity = quantity;
            product.selectedPrice = price;

            console.log("prodasc", product)

            //products = products.filter(product => product.id != productoId);
            // renderProducts(products);
            let oTherProduct = {
                item_id: product.id,
                description: product.description,
                priceId: priceId,
                unit_price: price,
                /*price: {
                    price: item.unit_price,
                    product_prices_id: item.product_prices_id
                },*/
                prices: product.prices,
                quantity: quantity,
            };
            quotationItems.push(oTherProduct);
            //addProductToOrder(oTherProduct);
            addProductToOrderEdited(quotationItems);
            row.remove();
        }
    }

    function addProductToOrder(product) {
        // Eliminar fila "No hay productos agregados" si existe
        const emptyRow = document.getElementById("emptyRow");
        if (emptyRow) {
            emptyRow.remove();
        }
        orderCount++;
        console.log(product);
        const orderRow = document.createElement("tr");
        orderRow.setAttribute("data-product-id", product.item_id);
        orderRow.innerHTML = `
            <td class="border p-2 text-center">${orderCount}</td>
            <td class="border p-2">${product.description}</td>
            <td class="border p-2">
                <input type="number" class="p-2 border rounded order-quantity" 
                       value="${product.quantity}" 
                       min="${product.minimum_stock}" 
                       max="${product.maximum_stock}" 
                       style="width: 60px;">
            </td>
            <td class="border p-2">
                <select class="p-2 border rounded order-price" 
                        data-product-id="${product.item_id}" 
                        style="width: 120px;">
                    <option value="">Seleccionar precio</option>
                    ${product.prices.map(precio => `
                        <option value="${precio.price}" 
                                data-price-id="${precio.id}" 
                                ${precio.id == product.priceId ? 'selected' : ''}>
                            ${precio.type} - ${precio.price}
                        </option>`).join('')}
                </select>
            </td>
            <td class="border p-2 order-total" style="text-align: right;">0.00</td>
            <td class="border p-2 order-subtotal" style="text-align: right;">0.00</td>
            <td class="border p-2 text-center">
                <button class="bg-red-500 text-white px-2 py-1 rounded eliminar-btn" 
                        data-product-id="${product.item_id}">
                    Eliminar
                </button>
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
            console.log(prodId)
            quotationItems = quotationItems.filter(item => item.item_id != prodId);
            // Eliminar la fila de la orden
            orderRow.remove();
            updateTotalAmount();
            // Reinsertar el producto eliminado en el listado del modal
            // const productToRestore = allProducts.find(p => p.id == prodId);
            // if (productToRestore) {
            //     if (!products.find(product => product.id == prodId)) {
            //         products.push(productToRestore);
            //         products.sort((a, b) => a.id - b.id);
            //         renderProducts(products);
            //     }
            // }
            // Si ya no hay filas en la orden, mostrar la fila vacía
            if (orderTableBody.querySelectorAll("tr[data-product-id]").length === 0) {
                orderCount = 0;
                orderTableBody.innerHTML = `<tr id="emptyRow">
                        <td class="border p-2 text-center" colspan="7">No hay productos agregados</td>
                    </tr>`;
            }
        });


    }
    // Función para agregar productos al formulario de edición
    function addProductToForm(quotationProducts) {

        console.log(quotationItems, "quotationItems");
        let orderTableBody = document.getElementById("orderTableBody");

        // Limpiar la tabla antes de agregar nuevos productos
        orderTableBody.innerHTML = "";

        // Filtrar solo los productos
        /*const products = quotationItems
            .filter(item => item.item_type === "App\\Models\\Product")
            .map(item => ({
                id: item.id,
                item_id: item.item_id,
                description: item.item.description,
                minimum_stock: item.stock.minimum_stock,
                maximum_stock: item.stock.quantity,
                prices: item.prices,
                priceId: item.product_prices_id,
                unit_price: item.unit_price,
        selectedQuantity: 1,
            selectedPrice: "",
            quantity: item.quantity,


    }));*/

        // Llamar a la función para renderizar productos
        addProductToOrderEdited(quotationItems);
    }

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
            /*if (allProducts.length === 0) {
                fetchProducts();
            } else {
                renderProducts(products);
            }*/
        });
    });

    function fetchProducts() {
        const almacen = document.getElementById("almacen").value;
        const search = searchInput.value;
        // Realizar la solicitud a la API
        fetch('/api/product?almacen=' + almacen + '&search=' + search)
            .then(res => res.json())
            .then(data => {
                allProducts = data
                    .filter(product => !quotationItems.some(item => item.item_id === product.id))
                    .map(product => ({
                        ...product,
                        selectedQuantity: 1,
                        selectedPrice: ""
                    }));
                // Inicialmente, todos los productos están disponibles en el modal
                products = [...allProducts];
                console.log(products)
                renderProducts(products);
            })
            .catch(error => console.error('Error:', error));
    }

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
                            ${product.prices.map(price => `<option value="${price.price}" data-price-id="${price.id}">${price.type} - ${price.price}</option>`).join('')}
                        </select>
                    </td>
                    <td class="px-2 py-1 border subtotal-cell" id="subtotal-${product.id}">0</td>
                    <td class="px-2 py-1 border">
                        <button class="bg-blue-500 text-white px-3 py-1 rounded" data-product-id="${product.id}"  onclick="agregarProducto(this)">Agregar</button>
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


    }

    function updateOrderRow(row) {
        const qty = parseFloat(row.querySelector(".order-quantity").value) || 0;
        const price = parseFloat(row.querySelector(".order-price").value) || 0;
        const subtotal = qty * price;
        row.querySelector(".order-total").textContent = subtotal.toFixed(2);
        row.querySelector(".order-subtotal").textContent = subtotal.toFixed(2);
    }

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

    function updateCalculos(item_id) {
        if (item_id !== null) {
            quotationItems.forEach(item => {
                console.log("foreach", item)
                if (item.item_id == item_id) {
                    const itemPrice = document.getElementById(`price-product-${item_id}`);
                    const selectedOption = itemPrice.options[itemPrice
                        .selectedIndex]; // Obtiene el <option> seleccionado
                    const priceId = selectedOption.dataset.priceId;
                    item.quantity = parseFloat(document.getElementById(`quantity-product-${item_id}`).value);
                    item.unit_price = parseFloat(itemPrice.value);
                    item.priceId = parseFloat(priceId);
                }
            })
        }
        updateInformationCalculos();

    }

    function addProductToOrderEdited(products) {
        console.log('Productos recibidos:', products);

        // Eliminar fila "No hay productos agregados" si existe
        const emptyRow = document.getElementById("emptyRow");
        if (emptyRow) {
            emptyRow.remove();
        }

        orderTableBody.innerHTML = "";

        quotationItems.forEach(product => { //el array para cada producto
            orderCount++;
            const orderRow = document.createElement("tr");
            orderRow.setAttribute("data-product-id", product.item_id);

            orderRow.innerHTML = `
            <td class="border p-2 text-center">${orderCount}</td>
            <td class="border p-2">${product.description}</td>
            <td class="border p-2">
                <input type="number" class="p-2 border rounded order-quantity" id="quantity-product-${product.item_id}" 
                       value="${product.quantity}" 
                       min="${product.minimum_stock}" 
                       max="${product.maximum_stock}" 
                       style="width: 60px;">
            </td>
            <td class="border p-2">
                <select class="p-2 border rounded order-price" id="price-product-${product.item_id}"
                        data-product-id="${product.item_id}" 
                        style="width: 120px;">
                    <option value="">Seleccionar precio</option>
                    ${product.prices.map(precio => `
                        <option value="${precio.price}" 
                                data-price-id="${precio.id}" 
                                ${precio.id == product.priceId ? 'selected' : ''}>
                            ${precio.type} - ${precio.price}
                        </option>`).join('')}
                </select>
            </td>
            <td class="border p-2 order-total" style="text-align: right;">0.00</td>
            <td class="border p-2 order-subtotal" style="text-align: right;">0.00</td>
            <td class="border p-2 text-center">
                <button class="bg-red-500 text-white px-2 py-1 rounded eliminar-btn" 
                        data-product-id="${product.item_id}">
                    Eliminar
                </button>
            </td>
        `;

            orderTableBody.appendChild(orderRow);
            updateOrderRow(orderRow);
            //updateTotalAmount();
            updateInformationCalculos();

            // Eventos para edición en la tabla de pedido
            const qtyInput = orderRow.querySelector(".order-quantity");
            const priceSelect = orderRow.querySelector(".order-price");
            qtyInput.addEventListener("input", () => {
                updateOrderRow(orderRow);
                const inputId = event.target.id.replace('quantity-product-', '');
                console.log(inputId);
                updateCalculos(inputId);
                //updateTotalAmount();
            });
            priceSelect.addEventListener("change", () => {
                updateOrderRow(orderRow);
                //updateTotalAmount();
                const productId = priceSelect.dataset.productId;
                updateCalculos(productId);
            });
            // Evento para eliminar el producto de la orden
            const eliminarBtn = orderRow.querySelector(".eliminar-btn");
            eliminarBtn.addEventListener("click", () => {
                const prodId = eliminarBtn.getAttribute("data-product-id");
                console.log("prodID", prodId)
                quotationItems = quotationItems.filter(item => item.item_id != prodId);
                updateInformationCalculos();
                // Eliminar la fila de la orden
                orderRow.remove();
                //updateTotalAmount();
                // Reinsertar el producto eliminado en el listado del modal
                // const productToRestore = allProducts.find(p => p.id == prodId);
                // if (productToRestore) {
                //     if (!products.find(product => product.id == prodId)) {
                //         products.push(productToRestore);
                //         products.sort((a, b) => a.id - b.id);
                //         renderProducts(products);
                //     }
                // }
                // Si ya no hay filas en la orden, mostrar la fila vacía
                if (orderTableBody.querySelectorAll("tr[data-product-id]").length === 0) {
                    orderCount = 0;
                    orderTableBody.innerHTML = `<tr id="emptyRow">
                        <td class="border p-2 text-center" colspan="7">No hay productos agregados</td>
                    </tr>`;
                }
            });
        });
    }

    // Función para actualizar el total
    function updateTotalAmount() {
        let total = 0;
        document.querySelectorAll(".order-total").forEach(td => {
            total += parseFloat(td.textContent.replace("S/ ", "")) || 0;
        });
        document.getElementById("totalAmount").textContent = "S/ " + total.toFixed(2);
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
