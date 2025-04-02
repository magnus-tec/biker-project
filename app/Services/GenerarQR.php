<?php

namespace App\Services;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

// use Endroid\QrCode\Builder;
// use Endroid\QrCode\Writer\PngWriter;

class GenerarQR
{

    private $ruc;
    private $cod_sunat;
    private $serie;
    private $numero;
    private $igv;
    private $total;
    private $fecha_emision;
    private $tipo_documento;
    private $documento;
    private $hash;

    function __construct($ruc, $cod_sunat, $serie, $numero, $igv, $total, $fecha_emision, $documento, $hash)
    {
        // 6 => RUC
        // 5 => DNI (PREGUNTAR)
        $tipo_documento = (strlen($documento) === 11) ? 6 : 5;
        $this->ruc = $ruc;
        $this->cod_sunat = $cod_sunat;
        $this->serie = $serie;
        $this->numero = $numero;
        $this->igv = $igv;
        $this->total = $total;
        $this->fecha_emision = $fecha_emision;
        $this->documento = $documento;
        $this->tipo_documento = $tipo_documento;
        $this->hash = $hash;
    }

    public function obtenerQR()
    {
        $contentQr = $this->ruc . '|' . $this->cod_sunat . '|' . $this->serie . '|' . $this->numero . '|' . $this->igv . '|' . $this->total . '|' . $this->fecha_emision . '|' . $this->tipo_documento . '|' . $this->documento . '|' . $this->hash;
        // Generar el código QR
        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($contentQr) // Contenido del QR
            ->size(200) // Tamaño del QR
            ->build();

        $qrImageBase64 = base64_encode($result->getString()); // Obtén la imagen como string y conviértela
        $qrImageSrc = 'data:image/png;base64,' . $qrImageBase64;
        return $qrImageSrc;
    }
}
