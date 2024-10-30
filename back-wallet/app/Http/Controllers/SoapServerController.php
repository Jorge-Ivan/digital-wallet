<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WalletService;
use SoapServer;
use SoapFault;

class SoapServerController extends Controller
{
    private $walletService;

    public function __construct()
    {
        $this->walletService = new WalletService();
    }

    public function handle(Request $request)
    {
        $route_wsdl = storage_path('soap/wallet.wsdl');
        $server = new SoapServer($route_wsdl);
        
        // Establece el controlador actual como objeto SOAP
        $server->setObject($this);

        try {
            ob_start();
            $server->handle();
            $response = ob_get_clean();

            // Retorna la respuesta con el encabezado correcto
            return response($response)->header('Content-Type', 'text/xml');
        } catch (SoapFault $e) {
            \Log::error("SOAP Fault: (faultcode: {$e->faultcode}, faultstring: {$e->faultstring})");
            return response($this->generateSoapFault($e->faultcode, $e->faultstring))
                ->header('Content-Type', 'text/xml');
        } catch (\Exception $e) {
            \Log::error("Exception: {$e->getMessage()}");
            return response($this->generateSoapFault('Server', 'An internal server error occurred.'))
                ->header('Content-Type', 'text/xml');
        }
    }
    
    public function registerUser($request)
    {
        try {
            $result = $this->walletService->registerClient($request->document, $request->names, $request->email, $request->cellphone);
            return $result;
        } catch (\Exception $e) {
            return $this->generateSoapFault('Server', $e->getMessage());
        }
    }
    
    public function chargeBalance($request)
    {
        try {
            $result = $this->walletService->chargeBalance($request->document, $request->cellphone, $request->value);
            return $result;
        } catch (\Exception $e) {
            return $this->generateSoapFault('Server', $e->getMessage());
        }
    }

    private function generateSoapFault($code, $message)
    {
        return [
            'status' => 'false',
            'cod_error' => $code,
            'message_error' => htmlspecialchars($message),
            'data' => null,
        ];
    }
}
