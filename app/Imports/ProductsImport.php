<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Brand;
use App\Models\Unit;
use App\Models\ProductPrice;
use App\Models\Stock;
use App\Models\UnitType;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Validators\Failure;

class ProductsImport implements OnEachRow, WithStartRow, WithValidation, SkipsOnFailure, WithBatchInserts, WithChunkReading
{
    use SkipsFailures;

    protected $userId;
    protected $failures = []; // Array para almacenar los fallos

    // Recibe el ID del usuario autenticado en el constructor
    public function __construct($userId)
    {
        $this->userId = $userId;
    }
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->failures[] = [
                'row'       => $failure->row(),          // Número de fila en el Excel
                'attribute' => $failure->attribute(),    // Campo que falló
                'errors'    => $failure->errors(),       // Array de mensajes de error
                'values'    => $failure->values(),       // Valores de la fila
            ];
        }
    }

    // Método para obtener los errores recogidos
    public function getFailures()
    {
        return $this->failures;
    }
    /**
     * Comienza en la fila 2 (si la fila 1 es el encabezado).
     * Ajusta este valor si incluyes filas de ejemplo.
     */
    public function startRow(): int
    {
        return 2;
    }

    /**
     * Se espera que el orden de columnas sea:
     * 0 => Código
     * 1 => Código de barras
     * 2 => Descripción
     * 3 => Modelo
     * 4 => Localización
     * 5 => Almacén
     * 6 => Marca
     * 7 => Unidad
     * 8 => Precio Compra
     * 9 => Precio Mayorista
     * 10 => Precio Sucursal A
     * 11 => Precio Sucursal B
     * 12 => Cantidad en Stock
     * 13 => Stock Mínimo
     */
    public function onRow(Row $row)
    {
        $rowData = $row->toArray();

        // var_dump($row->toArray());
        // exit();
        // Normaliza y busca/crea la unidad
        $unitName = ucfirst(strtolower(trim($rowData[7])));
        $unitTypeId = $this->determineUnitType($unitName);
        $unit = Unit::firstOrCreate(
            ['name' => $unitName],
            ['unit_type_id' => $unitTypeId]
        );

        // Normaliza y busca/crea la marca
        $brandName = ucfirst(strtolower(trim($rowData[6])));
        $brand = Brand::firstOrCreate(['name' => $brandName]);

        // Crear el producto en la tabla products

        $product = Product::create([
            'code_sku'      => $rowData[0],
            'code_bar'      => $rowData[1],
            'description'   => $rowData[2],
            'model'         => $rowData[3],
            'location'      => $rowData[4],
            'warehouse_id'  => Warehouse::where('name', $rowData[5])->value('id'),
            'brand_id'      => $brand->id,
            'unit_id'       => $unit->id,
            // Se asume que tienes definido el método generateCode() en el modelo Product
            'code'          => Product::generateCode(),
            'user_register' => $this->userId,
            'status'        => 1,
        ]);

        // Insertar precios (si se proporcionan)
        $priceTypes = [
            'buy'       => 8,  // Precio Compra
            'wholesale' => 9,  // Precio Mayorista
            'sucursalA' => 10,  // Precio Sucursal A
            'sucursalB' => 11, // Precio Sucursal B
        ];

        foreach ($priceTypes as $type => $index) {
            if (isset($rowData[$index]) && $rowData[$index] !== '') {
                ProductPrice::create([
                    'product_id' => $product->id,
                    'type'       => $type,
                    'price'      => $rowData[$index],
                ]);
            }
        }

        // Insertar stock (si se proporcionan datos)
        if ((isset($rowData[12]) && $rowData[12] !== '') || (isset($rowData[13]) && $rowData[13] !== '')) {
            Stock::create([
                'product_id'    => $product->id,
                'quantity'      => isset($rowData[12]) ? $rowData[12] : 0,
                'minimum_stock' => isset($rowData[13]) ? $rowData[13] : 0,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            // Validación de las columnas obligatorias
            '0' => 'required|unique:products,code_sku',
            '1' => 'required|unique:products,code_bar',
            '5' => 'required|exists:warehouses,name',
            // '6' => 'required|exists:brands,name',
            // '7' => 'required|exists:units,name',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '0.required'   => 'El SKU del producto es obligatorio.',
            '0.unique'     => 'El SKU ya existe en la base de datos.',
            '1.required'   => 'El codigo de barras del producto es obligatorio.',
            '1.unique'     => 'El codigo de barras  ya existe en la base de datos.',
            '5.exists'     => 'El almacén ingresado no existe. Verifica el nombre correcto.',
            // '6.exists'     => 'La marca ingresada no es válida.',
            // '7.exists'     => 'La unidad ingresada no es válida.',
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
    public static function generateCode()
    {
        $lastCodigo = Product::max('code') ?? '0000000';
        $nextCodigo = intval($lastCodigo) + 1;
        return str_pad($nextCodigo, 7, '0', STR_PAD_LEFT);
    }
    private function determineUnitType($unitName)
    {
        $unitTypeMappings = [
            'Peso'    => ['kilogramos', 'gramos', 'toneladas'],
            'Volumen' => ['litros', 'mililitros', 'galones'],
            'Cantidad' => ['unidades', 'piezas', 'cajas'],
        ];

        foreach ($unitTypeMappings as $type => $keywords) {
            if (in_array(strtolower($unitName), $keywords)) {
                return UnitType::firstOrCreate(['name' => $type])->id;
            }
        }

        return UnitType::firstOrCreate(['name' => 'Otro'])->id;
    }
}
