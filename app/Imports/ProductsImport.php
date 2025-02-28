<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Brand;
use App\Models\Unit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ProductsImport implements ToModel, WithStartRow, WithValidation, SkipsOnFailure, WithBatchInserts, WithChunkReading
{
    use SkipsFailures;

    protected $userId;

    // Recibe el ID del usuario autenticado en el constructor
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Comienza en la fila 2 (si la fila 1 es el encabezado)
     */
    public function startRow(): int
    {
        return 2;
    }

    public function model(array $row)
    {
        return new Product([
            // Orden: 0 => Código, 1 => Descripción, 2 => Modelo, 3 => Localización, 4 => Almacén, 5 => Marca, 6 => Unidad
            'code_sku'      => $row[0],
            'description'   => $row[1],
            'model'         => $row[2],
            'location'      => $row[3],
            'warehouse_id'  => Warehouse::where('name', $row[4])->value('id'),
            'brand_id'      => Brand::where('name', $row[5])->value('id'),
            'unit_id'       => Unit::where('name', $row[6])->value('id'),
            // Genera el código de producto
            'code'          => Product::generateCode(), // Asegúrate de que generateCode() sea accesible (por ejemplo, estática)
            // Asigna el usuario que importa
            'user_register' => $this->userId,
            'status'        => '1',
        ]);
    }

    public function rules(): array
    {
        return [
            '0' => 'required|unique:products,code_sku',
            '4' => 'required|exists:warehouses,name',
            '5' => 'required|exists:brands,name',
            '6' => 'required|exists:units,name',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '0.required'   => 'El SKU del producto es obligatorio.',
            '0.unique'     => 'El SKU ya existe en la base de datos.',
            '4.exists'     => 'El almacén ingresado no existe. Verifica el nombre correcto.',
            '5.exists'     => 'La marca ingresada no es válida.',
            '6.exists'     => 'La unidad ingresada no es válida.',
        ];
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
