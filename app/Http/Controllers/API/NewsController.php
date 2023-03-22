<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

class NewsController extends Controller
{

    public function retrieveData(Request $request)
    {
        $q = $request->get("criteria");
        $date = $request->get("fromDate");
        $source = $request->get("source");
        $category = $request->get("category");

        $url = "https://newsapi.org/v2/everything?";

        if ($q != null) {
            $url .= "&q=$q";
        }
        if ($date != null) {
            $url .= "&from=$date";
        }
        if ($source != null) {
            $url .= "&sources=$source";
        }
        if ($category != null) {
            $url .= "&category=$category";
        }

        $url .= "&sortBy=popularity&apiKey=70f108d6779342cf9ca616c945f97605";
        $response = Http::get($url);
        $parseResponse = $response->json($key = null, $default = null);

        return response()->json($parseResponse);
    }

    public function paginateData(Request $request)
    {

        
        // $redis = Redis::connection();
        
        // $articles = $parseResponse['articles'];
        // $redis->set('user_details', json_encode($articles));
        $redis    = Redis::connection();
        $response = $redis->get('user_details');

        $response = json_decode($response);
    }
}
