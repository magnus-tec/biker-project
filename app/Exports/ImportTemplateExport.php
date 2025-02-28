<?php

namespace App\Exports;

use App\Models\Warehouse;
use App\Models\Brand;
use App\Models\Unit;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class ImportTemplateExport implements FromArray, WithHeadings, WithColumnWidths
{
    public function headings(): array
    {
        return [
            'Código',       // Código del producto
            'Descripción',  // Nombre o descripción
            'Modelo',       // Modelo del producto
            'Localización', // Ubicación en el almacén
            'Almacén',      // Nombre del almacén
            'Marca',        // Marca del producto
            'Unidad',       // Unidad de medida
        ];
    }

    public function array(): array
    {
        // Obtener opciones de la base de datos
        $warehouses = Warehouse::pluck('name')->implode(', ');
        $brands = Brand::pluck('name')->implode(', ');
        $units = Unit::pluck('name')->implode(', ');

        return [
            // Fila de ejemplo con sugerencias sobre qué ingresar
            ['Ej: P001', 'Ej: Shampoo Hidratante 500ml', 'Ej: XYZ-123', 'Ej: Pasillo 3, Estante 2', "Ej: $warehouses", "Ej: $brands", "Ej: $units"],

            // Primera fila de datos reales vacía (para que el usuario empiece a llenar aquí)
            ['', '', '', '', '', '', ''],
        ];
    }

    // Método para definir los anchos de columna
    public function columnWidths(): array
    {
        return [
            'A' => 15, // Código
            'B' => 35, // Descripción
            'C' => 20, // Modelo
            'D' => 25, // Localización
            'E' => 50, // Almacén
            'F' => 40, // Marca
            'G' => 40, // Unidad
        ];
    }
}
