<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Feeds;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;

class FeedController extends Controller
{
    public function create(Request $request)
    {

        $feed = Feeds::create([
            'user_id' => $request->user()->id,
            'type' => $request->type,
            'feed' => $request->feed
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Feed Created Successfully',
            'feed' => $feed
        ], 200);
    }

    public function getUserFeeds(Request $request)
    {
        $feeds = DB::table('feeds')->where('user_id', $request->user()->id)->get();
        return response()->json([
            'status' => true,
            'feeds' => $feeds
        ], 200);
    }

    public function delete(Request $request)
    {
        DB::table('feeds')
            ->where('user_id', $request->user()->id)
            ->where('id', $request->id)
            ->delete();
        return response()->json([
            'status' => true,
            'message' => 'Feed Removed Successfully'
        ], 200);
    }
}
