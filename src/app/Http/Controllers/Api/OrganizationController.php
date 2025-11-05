<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Activity;
use App\Models\Building;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrganizationController extends Controller
{
    public function show($id)
    {
        $org = Organization::with(['building', 'phones', 'activities'])->find($id);
        if (!$org) return response()->json(['message' => 'Not found'], 404);
        return response()->json($org);
    }

    public function byBuilding(Request $request)
    {
        $buildingId = $request->query('building_id');
        if (!$buildingId) return response()->json(['message' => 'building_id required'], 400);

        $orgs = Organization::with(['phones', 'activities', 'building'])
            ->where('building_id', $buildingId)
            ->get();

        return response()->json($orgs);
    }

    public function byActivity($activityId)
    {
        $orgs = Organization::whereHas('activities', function ($q) use ($activityId) {
            $q->where('activities.id', $activityId);
        })->with('phones', 'activities', 'building')->get();

        return response()->json($orgs);
    }

    public function searchByActivity($activityId)
    {
        $root = Activity::find($activityId);
        if (!$root) return response()->json(['message' => 'Activity not found'], 404);


        $ids = [$root->id];
        foreach ($root->children as $child) {
            $ids[] = $child->id;
            foreach ($child->children as $grand) {
                $ids[] = $grand->id;
            }
        }

        $orgs = Organization::whereHas('activities', function ($q) use ($ids) {
            $q->whereIn('activities.id', $ids);
        })->with('phones', 'activities', 'building')->get();

        return response()->json($orgs);
    }

    public function searchByName(Request $request)
    {
        $name = $request->query('name', '');
        $orgs = Organization::with('phones', 'activities', 'building')
            ->where('name', 'like', '%' . $name . '%')
            ->get();
        return response()->json($orgs);
    }

    public function nearby(Request $request)
    {
        $lat = (float)$request->query('lat');
        $lng = (float)$request->query('lng');
        $radius = (float)$request->query('radius_km', 1); // km

        if (!$lat || !$lng) return response()->json(['message' => 'lat and lng required'], 400);

        $haversine = "(6371 * acos(cos(radians(?)) * cos(radians(buildings.latitude)) * cos(radians(buildings.longitude) - radians(?)) + sin(radians(?)) * sin(radians(buildings.latitude))))";
        $orgs = Organization::select('organizations.*')
            ->join('buildings', 'organizations.building_id', 'buildings.id')
            ->selectRaw("organizations.*, $haversine AS distance", [$lat, $lng, $lat])
            ->having('distance', '<=', $radius)
            ->orderBy('distance')
            ->with(['phones', 'activities', 'building'])
            ->get();

        return response()->json($orgs);
    }

    public function inRectangle(Request $request)
    {
        $minLat = $request->query('min_lat');
        $maxLat = $request->query('max_lat');
        $minLng = $request->query('min_lng');
        $maxLng = $request->query('max_lng');

        if (is_null($minLat) || is_null($maxLat) || is_null($minLng) || is_null($maxLng)) {
            return response()->json(['message' => 'min_lat,max_lat,min_lng,max_lng required'], 400);
        }

        $orgs = Organization::whereHas('building', function ($q) use ($minLat, $maxLat, $minLng, $maxLng) {
            $q->whereBetween('latitude', [$minLat, $maxLat])
                ->whereBetween('longitude', [$minLng, $maxLng]);
        })->with('phones', 'activities', 'building')->get();

        return response()->json($orgs);
    }

}
