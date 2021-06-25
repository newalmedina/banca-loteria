<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Country;
use App\Models\Provincia;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GeneralController extends ApiController
{
    public function getPaises(Request $request)
    {
        try {
            $paises = Country::active()
                ->orderBy('short_name', 'ASC')
                ->pluck('numeric_code', 'short_name');

            return response()->json([
                'return' => true,
                'message' => 'OK',
                'data' => $paises
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'return' => false,
                'message' => 'KO',
                'data' => []
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getProvincias(Request $request, $id)
    {
        try {
            $provincias = Provincia::active()
                ->where('country_code', $id)
            ->orderBy('nombre', 'ASC')
            ->pluck('id', 'nombre');

            return response()->json([
                'return' => true,
                'message' => 'OK',
                'data' => $provincias
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'return' => false,
                'message' => 'KO',
                'data' => []
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getMunicipios(Request $request, $id)
    {
        try {
            $provincia = Provincia::find($id);
            $municipios = $provincia
                ->municipios()
                ->active()
                ->orderBy('municipios.nombre', 'ASC')
                ->pluck('municipios.id', 'municipios.nombre');

            return response()->json([
                'return' => true,
                'message' => 'OK',
                'data' => $municipios
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'return' => false,
                'message' => 'KO',
                'data' => []
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
