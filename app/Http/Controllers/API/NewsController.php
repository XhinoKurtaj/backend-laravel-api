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
        // https://newsapi.org/v2/top-headlines?sources=techcrunch&apiKey=70f108d6779342cf9ca616c945f97605
        $url = "https://newsapi.org/v2/everything?q=$q";
        if ($date != null) {
            $url .= "&from=$date";
        }
        $url .= "&sortBy=popularity&apiKey=70f108d6779342cf9ca616c945f97605";

        // $redis = Redis::connection();

        $response = Http::get($url);
        $parseResponse = $response->json($key = null, $default = null);

        // $articles = $parseResponse['articles'];
        // $redis->set('user_details', json_encode($articles));
        return response()->json($parseResponse);
     
    }

    public function paginateData(Request $request)
    {

        $redis    = Redis::connection();
        $response = $redis->get('user_details');

        $response = json_decode($response);
    }
}
