<?php

namespace App\Models;

use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiSunat extends Model
{
    use HasFactory;

    use HasFactory;
    public $url = "https://magustechnologies.com/apisunat";
    public $data;

    public function __construct($tipo)
    {
        #1 boleta-facturas
        #2 notas de credito
        #3 
        switch ($tipo) {
            case '1':
                $this->url = $this->url . '/api/generar/comprobante/electronico';
                break;
            case '2':
                $this->url = $this->url . '/api/generar/nota/electronica';
                break;
            case '3':
                $this->url = $this->url . '/api/enviar/documento/electronico';
                break;
        }
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function enviarData()
    {
        $client = new Client();
        try {
            $response = $client->post($this->url, [
                'json' => $this->data,
                'verify' => false
            ]);
            $responseBody = $response->getBody()->getContents();
            if (strpos($responseBody, "\u{00A9}") === 0) {
                $responseBody = mb_substr($responseBody, 1);
            }
            $res = json_decode($responseBody, true);
            return $res;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
