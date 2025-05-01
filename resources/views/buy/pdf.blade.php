<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Pedido #{{ $buy->id }}</title>
    <style>
        /* Márgenes de la página para DomPDF */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 11px;
        }

        .container {
            width: 100%;
        }

        .header {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .logo {
            display: table-cell;
            width: 60%;
            vertical-align: middle;
        }

        .logo img {
            max-width: 100%;
            height: auto;
        }

        .order-info {
            display: table-cell;
            width: 40%;
            vertical-align: middle;
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }

        .order-info h2 {
            margin-top: 0;
        }

        .totals {
            width: auto;
            margin-left: auto;
            /* Alinea la tabla a la derecha */
            border-collapse: collapse;
            border: 1px solid #000;
            margin-top: 20px;


        }

        .totals td {
            padding: 5px;
            /* border: 1px solid #000; */
            text-align: left;
            padding: 5px 15px 5px 15px;

        }

        .totals td:first-child {
            text-align: right;
            /* Alinea los títulos a la izquierda */
        }

        .company-details span {
            display: block;
            padding: .1px;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Encabezado / Logo y Datos de la Empresa -->
        <div class="container">
            <div class="header">
                <div class="logo">
                    <img src="https://via.placeholder.com/150x80?text=LOGO" alt="Logo biker">
                    <div class="company-details">
                        <span>Central Telefónica: (XXX) XXX-XXXX</span><br>
                        <span>Email: info@biker.com</span><br>
                        <span>Website: www.biker.com</span><br>
                        <span>Dirección: Av. biker #123, Lima - Perú</span>
                    </div>
                </div>
                <div class="order-info">
                    <h2>Nota de compra </h2>
                    <h2>{{ $buy->serie }}-{{ $buy->number }}</h2>
                </div>
            </div>
        </div>

        <!-- Información del Cliente y Venta -->
        <table class="info-table" width="100%" style="border: 1px solid #000; border-collapse: collapse;">
            <tr>
                <th style="text-align: right; padding: 5px; ">DNI CLIENTE :</th>
                <td style="text-align: left; padding: 5px; ">{{ $buy->customer_dni }}</td>
                <th style="text-align: right; padding: 5px;">FECHA Y HORA :</th>
                <td style="text-align: left; padding: 5px;">
                    {{-- {{ \Carbon\Carbon::parse($sale->fecha_registro)->format('d/m/Y') }} --}}
                    {{ $buy->fecha_registro }}
                </td>
            </tr>
            <tr>
                <th style="text-align: right; padding: 5px;">CLIENTE :</th>
                <td style="text-align: left; padding: 5px;">{{ $buy->customer_names_surnames }}
                </td>
                <th style="text-align: right; padding: 5px;"></th>
                <td style="text-align: left; padding: 5px;"></td>
            </tr>
            <tr>
                <th style="text-align: right; padding: 5px;"></th>
                <td style="text-align: left; padding: 5px;"></td>
                <th style="text-align: right; padding: 5px;"></th>
                <td style="text-align: left; padding: 5px;"></td>
            </tr>
            <tr>
                <th style="text-align: right; padding: 5px;">COMPRADOR :</th>
                <td style="text-align: left; padding: 5px;">
                    {{ $buy->userRegister->name ?? 'N/A' }}
                </td>
                <th></th>
                <td></td>
            </tr>
        </table> <!-- Detalle de la Venta -->
        <table width="100%" style="border-collapse: collapse; border: 1px solid #000;margin-top: 3px;">
            <thead>
                <tr>
                    <th style="border: 1px solid #000; padding: 5px; text-align: center;">#</th>
                    <th style="border: 1px solid #000; padding: 5px; text-align: center;">PRODUCTO</th>
                    <th style="border: 1px solid #000; padding: 5px; text-align: center;">CANTIDAD</th>
                    <th style="border: 1px solid #000; padding: 5px; text-align: center;">PRECIO</th>
                    <th style="border: 1px solid #000; padding: 5px; text-align: center;">SUB TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($buy->buyItems) && is_iterable($buy->buyItems))
                    @php
                        $totalRows = 38;
                        $rowCount = 0;
                    @endphp

                    @foreach ($buy->buyItems as $index => $buyItem)
                        @php
                            $descripcion = $buyItem->product->description ??'N/A';
                            $cantidad = $buyItem->quantity; //
                            $precio = $buyItem->price ?? 0;
                            $subtotal = $cantidad * $precio;
                            $rowCount++;
                        @endphp
                        <tr>
                            <td style="padding: 2px; text-align: center; border-right: 1px solid #000">
                                {{ $index + 1 }}
                            </td>
                            
                            <td style="padding: 2px; text-align: center; border-right: 1px solid #000">
                                {{ $descripcion }}
                            </td>
                            <td style="padding: 2px; text-align: center; border-right: 1px solid #000">
                                {{ $cantidad }}
                            </td>
                            <td style="padding: 2px; text-align: center; border-right: 1px solid #000">
                                {{ number_format($precio, 2) }}
                            </td>
                            <td style="padding: 2px; text-align: center; border-right: 1px solid #000">
                                {{ number_format($subtotal, 2) }}
                            </td>
                        </tr>
                    @endforeach

                    {{-- Agregar filas vacías hasta completar el total de filas --}}
                    @for ($i = $rowCount; $i < $totalRows; $i++)
                        <tr>
                            <td style="padding: 2px; text-align: center; border-right: 1px solid #000">&nbsp;</td>
                            <td style="padding: 2px; text-align: center; border-right: 1px solid #000">&nbsp;</td>
                            <td style="padding: 2px; text-align: center; border-right: 1px solid #000">&nbsp;</td>
                            <td style="padding: 2px; text-align: center; border-right: 1px solid #000">&nbsp;</td>
                            <td style="padding: 2px; text-align: center; border-right: 1px solid #000">&nbsp;</td>
                        </tr>
                    @endfor
                @else
                    {{-- Si no hay productos, llenar con filas vacías --}}
                    @for ($i = 0; $i < 10; $i++)
                        <tr>
                            <td style="padding: 2px; text-align: center; border-right: 1px solid #000">&nbsp;</td>
                            <td style="padding: 2px; text-align: center; border-right: 1px solid #000">&nbsp;</td>
                            <td style="padding: 2px; text-align: left; border-right: 1px solid #000">&nbsp;</td>
                            <td style="padding: 2px; text-align: center; border-right: 1px solid #000">&nbsp;</td>
                            <td style="padding: 2px; text-align: center; border-right: 1px solid #000">&nbsp;</td>
                        </tr>
                    @endfor
                @endif
            </tbody>

        </table>



        <!-- Totales -->
        <table class="totals">
            <tr>
                <td>Total Op. Gravado:</td>
                <td>S/ {{ number_format($buy->total_price - $buy->igv, 2) }}</td>
            </tr>
            <tr>
                <td>IGV:</td>
                <td>S/ {{ number_format($buy->igv, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Total a Pagar:</strong></td>
                <td><strong>S/ {{ number_format($buy->total_price, 2) }}</strong></td>
            </tr>
        </table>
        <!-- Pie de Página -->
    </div>
</body>

</html>
