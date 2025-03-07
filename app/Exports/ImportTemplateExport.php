<?php

namespace App\Exports;

use App\Models\Warehouse;
use App\Models\Brand;
use App\Models\Unit;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class ImportTemplateExport implements FromArray, WithHeadings, WithColumnWidths
{
    public function headings(): array
    {
        return [
            'Código',           // Código del producto
            'Código de barras',           // Código del producto
            'Descripción',      // Nombre o descripción
            'Modelo',           // Modelo del producto
            'Localización',     // Ubicación en el almacén
            'Almacén',          // Nombre del almacén
            'Marca',            // Marca del producto
            'Unidad',           // Unidad de medida
            'Precio Compra',    // Precio de compra
            'Precio Mayorista', // Precio mayorista
            'Precio Sucursal A', // Precio para sucursal A
            'Precio Sucursal B', // Precio para sucursal B
            'Cantidad en Stock', // Cantidad en stock
            'Stock Mínimo'      // Stock mínimo
        ];
    }

    public function array(): array
    {
        // Obtener opciones de la base de datos para orientar al usuario
        $warehouses = Warehouse::pluck('name')->implode(', ');
        $brands = Brand::pluck('name')->implode(', ');
        $units = Unit::pluck('name')->implode(', ');

        return [
            // Fila de ejemplo con sugerencias sobre qué ingresar
            [
                'Ej: P001',
                'Ej: 156001',
                'Ej: Shampoo Hidratante 500ml',
                'Ej: XYZ-123',
                'Ej: Pasillo 3, Estante 2',
                "Ej: $warehouses",
                "Ej: $brands",
                "Ej: $units",
                'Ej: 100.00',   // Precio Compra
                'Ej: 90.00',    // Precio Mayorista
                'Ej: 110.00',   // Precio Sucursal A
                'Ej: 115.00',   // Precio Sucursal B
                'Ej: 50',       // Cantidad en Stock
                'Ej: 10'        // Stock Mínimo
            ],
            // Primera fila de datos reales vacía (para que el usuario empiece a llenar aquí)
            ['', '', '', '', '', '', '', '', '', '', '', '', ''],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // Código
            'B' => 15, // Código de Barras
            'C' => 35, // Descripción
            'D' => 20, // Modelo
            'E' => 25, // Localización
            'F' => 50, // Almacén
            'G' => 40, // Marca
            'H' => 40, // Unidad
            'I' => 20, // Precio Compra
            'J' => 20, // Precio Mayorista
            'K' => 20, // Precio Sucursal A
            'L' => 20, // Precio Sucursal B
            'M' => 20, // Cantidad en Stock
            'N' => 20, // Stock Mínimo
        ];
    }
}
