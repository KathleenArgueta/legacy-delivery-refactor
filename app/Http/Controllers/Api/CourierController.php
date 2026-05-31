<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Courier;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CourierController extends Controller
{
    public function available(): JsonResponse
    {
        $couriers = Courier::where('available', true)->with('user')->get();
        return response()->json($couriers);
    }

    public function updateLocation(Request $request, Courier $courier): JsonResponse
{
    $this->authorize('update', $courier);

    $request->validate([
        'latitude'  => 'required|numeric',
        'longitude' => 'required|numeric',
    ]);

    $courier->updateLocation($request->latitude, $request->longitude);

    return response()->json(['message' => 'Location updated.']);
}

public function updateAvailability(Request $request, Courier $courier): JsonResponse
{
    $this->authorize('update', $courier);

    $request->validate(['available' => 'required|boolean']);
    $courier->available = $request->available;
    $courier->save();

    return response()->json(['message' => 'Availability updated.']);
}
}
