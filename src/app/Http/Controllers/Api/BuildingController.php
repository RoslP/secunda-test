<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Building;

class BuildingController extends Controller
{
    public function index()
    {
        return response()->json(Building::all());
    }

    public function show($id)
    {
        return response()->json(Building::with('organizations')->findOrFail($id));
    }
}
