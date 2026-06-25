<?php

namespace App\Http\Controllers;

use App\Models\PageVisit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PageVisitController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'page' => ['required', 'string', 'max:255'],
        ]);

        $pageVisit = PageVisit::firstOrCreate([
            'user_id' => $request->user()->id,
            'page' => $data['page'],
        ], [
            'visits' => 0,
        ]);

        $pageVisit->increment('visits');
        $pageVisit->last_visited_at = now();
        $pageVisit->save();
        $pageVisit->refresh();

        return response()->json([
            'page' => $pageVisit->page,
            'visits' => $pageVisit->visits,
            'updated_at' => $pageVisit->updated_at,
        ]);
    }
}
