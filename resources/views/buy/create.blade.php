<x-app-layout>
    <x-slot name="header" class="max-w-max mx-auto p-2 text-sm">
    </x-slot>
    <div class="max-w-max mx-auto p-5 text-sm">
        <div class="grid grid-cols-3 gap-6">
            <!-- Formulario de Cliente -->
            <div class="col-span-2 bg-white p-6 rounded-lg shadow">
                <h2 class="text-lg font-bold mb-4">Proveedor</h2>
                <input type="text" id="dni_personal" placeholder="Ingrese Documento"
                    class="w-full p-2 border rounded mb-2">
                <input type="text" placeholder="Nombre del cliente" id="nombres_apellidos"
                    class="w-full p-2 border rounded mb-2">
                <input type="text" placeholder="Direccion del cliente" id="direccion"
                    class="w-full p-2 border rounded mb-2">
                <!-- Botón que abre el modal -->
                <button class="bg-yellow-400 p-2 rounded w-3/12 mt-2" id="buscarProductos">Consultar
                    Productos</button>
                <!-- Modal -->
                <div id="buscarProductosModal"
                    class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 p-4 hidden">
                    <div class="bg-white p-6 rounded-lg shadow-lg max-w-4xl w-full">
                        <h3 class="text-lg font-bold mb-4">Productos</h3>

                        <!-- Campo de búsqueda dentro del modal -->
                        <div class="mb-4 flex items-center ">
                            <div class="w-8/12">
                                <input type="text" placeholder="Buscar por codigo del producto..."
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
                                        <th class="px-2 py-1 border">Precio Compra</th>
                                        <th class="px-2 py-1 border">Stock Actual</th>
                                        <th class="px-2 py-1 border">Stock Mínimo</th>
                                        <th class="px-2 py-1 border">Cantidad</th>
                                        <th class="px-2 py-1 border">Aumentar Stock</th>
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
                    <label class="font-bold">Tipo de documento</label>
                    <!-- Se agrega id para capturar el valor -->
                    <select id="documentType" class="w-full p-2 border rounded">
                        <option value="">Seleccione</option>
                        @foreach ($documentTypes as $documentType)
                            <option value="{{ $documentType->id }}">{{ $documentType->name === 'NOTA DE VENTA' ? 'NOTA DE COMPRA' : $documentType->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <label>Fecha</label>
                <!-- Se agrega id para la fecha -->
                <input type="date" id="orderDate" value="{{ date('Y-m-d') }}"
                    class="w-full p-2 border rounded mb-4">
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
                    <button class="bg-blue-500 text-white p-2 rounded" onclick="save()">Guardar</button>
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
                {{-- <div>
                    <button class="bg-blue-500 text-white px-4 py-2  rounded-md rounded-l-none mr-5"
                        id="btnBuscarProductList">Buscar</button>
                </div> --}}
            </div>

            <table class="w-full border-collapse border border-gray-300" id="orderTable">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border p-2">Item</th>
                        <th class="border p-2">Producto</th>
                        <th class="border p-2">Cantidad</th>
                        <th class="border p-2">Precio Compra</th>
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
    </div>
</x-app-layout>

<script>
    let services = [];
    let orderCount = 0; // para numerar los ítems
    const searchInput = document.getElementById("searchProduct");
    let products = []; // Productos disponibles en el modal
    let quotationItems = [];
    let orderTableBody = document.getElementById("orderTableBody");
    const totalAmountEl = document.getElementById("totalAmount");
    let payments = [];
  
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
                    .filter(product => !quotationItems.some(item => item.product_id === product.id))
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
                    <td class="px-2 py-1 border">${product.code_sku}</td>
                    <td class="px-2 py-1 border">${product.description}</td>
                    <td class="px-2 py-1 border">
                        ${(() => {
                            const precioBuy = product.prices.find(price => price.type === 'buy');
                            return `<input 
                                        type="text" 
                                        class="p-2 border rounded data-price-id-${product.id}" 
                                        data-product-id="${product.id}" 
                                        style="width: 120px;" 
                                        value="${precioBuy ? precioBuy.price : ''}" 
                                        data-price-id="${precioBuy ? precioBuy.id : ''}" 
                                        >`;
                        })()}
                    </td>                    <td class="px-2 py-1 border">${product.stock.quantity}</td>
                    <td class="px-2 py-1 border">${product.stock.minimum_stock}</td>
                    <td class="px-2 py-1 border">
                        <input type="number" class="p-2 border rounded data-quantity-id-${product.id} value="1" min="1" max="${product.stock.quantity}" data-product-id="${product.id}">
                    </td>
                   

                    <td class="px-2 py-1 border subtotal-cell" id="subtotal-${product.id}" style="display: none;">0</td>
                    <td class="px-2 py-1 border">
                        <button class="bg-blue-500 text-white px-3 py-1 rounded" data-product-id="${product.id}"  onclick="agregarProducto(${product.id})">Aumentar stock</button>
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
        priceSelect.addEventListener("input", () => updateModalSubtotal(productId, quantity, priceSelect));
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
        const price = document.querySelector(`.data-price-${productId}`).value;
        const totalValueCell = document.querySelector(`.data-total-value-${productId}`);
        const quantity = parseFloat(quantityInput.value) || 0;
        totalValueCell.textContent = (price * quantity).toFixed(2);

        console.log('price',price);
        console.log('quantity',quantity);
        console.log('productId',productId);
        quotationItems.forEach(item => {
            if (item.product_id == productId) {
                item.quantity = quantity;
                item.price = price;
            }
        })
        updateInformationCalculos();

    }

    // guardar cotizacion 
    async function save() {
        try {

            const orderData = buildOrderData();
            const response = await fetch('{{ route('buy.addStock') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    ...orderData,
                })
            });

            if (!response.ok) throw new Error("Error en la petición");
            const data = await response.json();
            alert("La cotización se ha guardado correctamente.");
        } catch (error) {
            console.error("Error al guardar la orden:", error);
            alert("Error al guardar la orden.");
        }
    }



    //agregar productos
    function agregarProducto(productId) {
        const quantity = document.querySelector(`.data-quantity-id-${productId}`).value;
        const price = document.querySelector(`.data-price-id-${productId}`).value;
        const response = products.find(product => product.id == productId);
        if (response) {
            const product = {
                product_id: productId,
                description: response.description,
                price: price,
                quantity: quantity,
            }
            quotationItems.push(product);
            addProductTo(product);
            updateInformationCalculos();

        }
        products = products.filter(product => product.id != productId)
          // Ocultar la fila del input quantity
          const inputQuantity = document.querySelector(`.data-quantity-id-${productId}`);
        if (inputQuantity) {
            const row = inputQuantity.closest("tr");
            if (row) row.style.display = "none";
        }
    }

    function addProductTo(product) {
        console.log(product)
        const emptyRow = document.getElementById("emptyRow");
        if (emptyRow) {
            emptyRow.remove();
        }
        orderCount++;
        const orderRow = document.createElement("tr");
        orderRow.setAttribute("data-product-id", product.product_id);
        orderRow.innerHTML = `
            <td class="border p-2 text-center">${orderCount}</td>
            <td class="border p-2">${product.description}</td>
            <td class="border p-2">
                <input type="number" class="p-2 border rounded data-quantity-value-${product.product_id}" oninput="updatePriceAndTotal(${product.product_id})"
                       value="${product.quantity}" 
                       max="${product.maximum_stock}"
                       min="1"
                       style="width: 60px;">
            </td>
            <td class="border p-2 " >
                <input
                    type="number" 
                    class="border rounded p-1 text-center w-full total-price-input-${product.product_id} data-price-${product.product_id}" 
                    value="${product.price}" 
                    data-item-id="${product.product_id}"
                     oninput="updatePriceAndTotal(${product.product_id})"
                    >
            </td>
            <td class="border p-2 data-total-value-${product.product_id}" >${product.price * product.quantity} </td>
            <td class="border p-2 text-center">
                <button class="bg-red-500 text-white px-2 py-1 rounded eliminar-btn" 
                       onclick="deleteProduct(${product.product_id})">
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
            totalAmount += item.quantity * item.price;
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
    // eliminar producto
    function deleteProduct(productId) {
        quotationItems = quotationItems.filter(product => product.product_id != productId);
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
        };
    }

    // // Extraer los datos del cliente
    function getCustomerData() {
        return {
            customer_dni: document.getElementById("dni_personal").value.trim(),
            customer_names_surnames: document.getElementById("nombres_apellidos").value.trim(),
            customer_address: document.getElementById("direccion").value.trim(),
            document_type_id: document.getElementById("documentType").value,
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
    function buscarDNIRuc(DNIRuc) {
        if (DNIRuc.length === 8) {
            fetch(`https://dniruc.apisperu.com/api/v1/dni/${DNIRuc}?token=${token}`)
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
        }else{
            document.getElementById('nombres_apellidos').value = '';
        }
        if (DNIRuc.length === 11) {
            fetch(`https://dniruc.apisperu.com/api/v1/ruc/${DNIRuc}?token=${token}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la solicitud');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log(data);
                    document.getElementById('nombres_apellidos').value = data.razonSocial;
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('No se pudo encontrar el ruc');
                });
        }else{
            document.getElementById('nombres_apellidos').value = '';
        }
    }

    // Evento cuando el usuario escribe en el campo DNI
    inputDni.addEventListener('input', () => {
        inputDni.value = inputDni.value.replace(/\s+/g, '');

        buscarDNIRuc(inputDni.value);
    });
</script>
