<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NewsController extends Controller
{


    public function retrieveData(Request $request)
    {
        $response = Http::get('https://newsapi.org/v2/everything?q=apple&from=2023-03-20&to=2023-03-20&sortBy=popularity&apiKey=70f108d6779342cf9ca616c945f97605');
        $parseResponse = $response->json($key = null, $default = null);
        $articles = $parseResponse['articles'];
        return response()->json($parseResponse);
    }
}
