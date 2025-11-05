<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index()
    {
        return response()->json(Activity::with('children')->whereNull('parent_id')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'parent_id' => 'nullable|exists:activities,id',
        ]);

        $level = 1;
        if ($request->parent_id) {
            $parent = Activity::find($request->parent_id);
            $level = $parent->level + 1;
        }

        if ($level > 3) {
            return response()->json(['message' => 'Max activity nesting level is 3'], 422);
        }

        $activity = Activity::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'level' => $level
        ]);

        return response()->json($activity, 201);
    }
}
