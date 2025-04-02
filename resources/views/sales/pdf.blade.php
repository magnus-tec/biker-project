<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Pedido #{{ $sale->id }}</title>
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
            margin-bottom: 3px;
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
                    <h2>RUC : {{ $sale->companies->ruc }}</h2>
                    <h2>{{ $sale->documentType->name }}</h2>
                    <h2>{{ $sale->serie }}-{{ $sale->number }}</h2>
                </div>
            </div>
        </div>

        <!-- Información del Cliente y Venta -->
        <table class="info-table" width="100%" style="border: 1px solid #000; border-collapse: collapse;">
            <tr>
                <th style="text-align: right; padding: 5px; ">DNI CLIENTE :</th>
                <td style="text-align: left; padding: 5px; ">{{ $sale->customer_dni }}</td>
                <th style="text-align: right; padding: 5px;">FECHA Y HORA :</th>
                <td style="text-align: left; padding: 5px;">
                    {{-- {{ \Carbon\Carbon::parse($sale->fecha_registro)->format('d/m/Y') }} --}}
                    {{ $sale->fecha_registro }}
                </td>
            </tr>
            <tr>
                <th style="text-align: right; padding: 5px;">CLIENTE :</th>
                <td style="text-align: left; padding: 5px;">{{ $sale->customer_names_surnames }}
                </td>
                <th style="text-align: right; padding: 5px;">MONEDA :</th>
                <td style="text-align: left; padding: 5px;"></td>
            </tr>
            <tr>
                <th style="text-align: right; padding: 5px;">DIRECCIÓN :</th>
                <td style="text-align: left; padding: 5px;">{{ $sale->customer_address }}</td>
                <th style="text-align: right; padding: 5px;">PAGO :</th>
                <td style="text-align: left; padding: 5px;"></td>
            </tr>
            <tr>
                <th style="text-align: right; padding: 5px;">VENDEDOR :</th>
                <td style="text-align: left; padding: 5px;">
                    {{ $sale->userRegister->name ?? 'N/A' }}
                </td>
                <th></th>
                <td></td>
            </tr>
        </table> <!-- Detalle de la Venta -->
        <table width="100%" style="border-collapse: collapse; border: 1px solid #000;margin-top: 3px;">
            <thead>
                <tr>
                    <th style="border: 1px solid #000; padding: 5px; text-align: center;">ITEM</th>
                    <th style="border: 1px solid #000; padding: 5px; text-align: center;">Cantidad</th>
                    <th style="border: 1px solid #000; padding: 5px; text-align: center;">DESCRIPCIÓN</th>
                    <th style="border: 1px solid #000; padding: 5px; text-align: center;">MEDIDA</th>
                    <th style="border: 1px solid #000; padding: 5px; text-align: center;">PRECIO</th>
                    <th style="border: 1px solid #000; padding: 5px; text-align: center;">SUB TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($sale->saleItems) && is_iterable($sale->saleItems))
                    @php
                        $totalRows = 33;
                        $rowCount = 0;
                    @endphp

                    @foreach ($sale->saleItems as $index => $saleItem)
                        @php
                            $descripcion = $saleItem->item->description ?? ($saleItem->item->name ?? 'N/A');
                            $cantidad = str_pad($saleItem->quantity ?? 0, 4, '0', STR_PAD_LEFT); // Formato 0001
                            $precio = $saleItem->unit_price ?? 0;
                            $subtotal = $cantidad * $precio;
                            $unidad = $saleItem->item->unit->name ?? 'N/A';
                            $rowCount++;
                        @endphp
                        <tr>
                            <td style="padding: 2px; text-align: center; border-right: 1px solid #000">
                                {{ $index + 1 }}
                            </td>
                            <td style="padding: 2px; text-align: center; border-right: 1px solid #000">
                                {{ $cantidad }}
                            </td>
                            <td style="padding: 2px; text-align: center; border-right: 1px solid #000">
                                {{ $descripcion }}
                            </td>
                            <td style="padding: 2px; text-align: center; border-right: 1px solid #000">
                                {{ $unidad }}
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
                            <td style="padding: 2px; text-align: center; border-right: 1px solid #000">&nbsp;</td>
                        </tr>
                    @endfor
                @endif
            </tbody>

        </table>

        <!-- Pie de Página -->
        <table style="width:100%;margin-top:50px">
            <tr>
                <td style="width: 20%;">
                    @if ($sale->document_type_id != 6)
                        <img src="{{ $imagenQr }}" alt="Código QR" width="100%">
                    @endif
                </td>
                <td style="width: 50%;">
                    {{-- <p><strong>¡GRACIAS POR SU PREFERENCIA! ¡DIOS LES BENDIGA!</strong></p> --}}
                    <div style="width:79%;float: right;font-size: 12px;">
                        Consulte su comprobante: <br>
                        <a href="http://www.sunat.gob.pe/ol-ti-itconsvalicpe/ConsValiCpe.htm"
                            style="text-decoration: none;color:#000"><strong>http://www.sunat.gob.pe/ol-ti-itconsvalicpe/ConsValiCpe.htm</strong></a>
                        <br /><br />Código Hash:<br />
                        <strong>{{ $hashBase64 }}</strong>
                    </div>
                </td>
                <td style="width: 40%;">
                    <table class="totals">
                        <tr>
                            <td>Total Op. Gravado:</td>
                            <td>S/ {{ number_format($sale->total_price - $sale->igv, 2) }}</td>
                        </tr>
                        <tr>
                            <td>IGV:</td>
                            <td>S/ {{ number_format($sale->igv, 2) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Total a Pagar:</strong></td>
                            <td><strong>S/ {{ number_format($sale->total_price, 2) }}</strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
