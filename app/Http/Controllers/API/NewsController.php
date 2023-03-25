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

        $apiKey = env("NEWSAPI_KEY", null);

        $q = $request->get("keyword");
        $date = $request->get("fromDate");
        $source = $request->get("source");
        $category = $request->get("category");

        $type = "everything";
        $part_url = "";

        if ($q != null) {
            $part_url .= "&q=$q";
        }
        if ($date != null) {
            $encD = base64_encode($date);
            $part_url .= "&from=$encD";
        }
        if ($source != null) {
            $part_url .= "&sources=$source";
        }
        if ($category != null) {
            $part_url .= "&category=$category";
            $type = "top-headlines";
        }

        $url = "https://newsapi.org/v2/$type?";
        $url .= substr($part_url, 1);
        $url .= "&sortBy=popularity&apiKey=$apiKey";

        $response = Http::get($url);
        $parseResponse = $response->json($key = null, $default = null);

        return response()->json($parseResponse);
    }

    public function retrieveTheGuardianNews(Request $request)
    {
        $apiKey = env("THE_GUARDIAN_KEY", null);

        $q = $request->get("keyword");
        $date = $request->get("fromDate");
        $section = $request->get("source");

        $page = $request->get("page");

        $part_url = "";

        if ($q != null) {
            $part_url .= "&q=$q";
        }
        if ($page != null) {
            $part_url .= "&page=$page";
        }
        if ($section != null) {
            $part_url .= "&section=$section";
        }
        if ($date != null) {
            $part_url .= "&from=$date";
        }


        $url = "https://content.guardianapis.com/search?";
        $url .= substr($part_url, 1);
        $url .= "&&api-key=$apiKey";

        $apiRes = Http::get($url);
        $parseResponse = $apiRes->json()['response'];
        $response = [
            'status' => $parseResponse['status'],
            'total' => $parseResponse['total'],
            'startIndex' => $parseResponse['startIndex'],
            'pageSize' => $parseResponse['pageSize'],
            'currentPage' => $parseResponse['currentPage'],
            'pages' => $parseResponse['pages'],
            'articles' => $parseResponse['results'],
        ];

        return response()->json($response);
    }
    public function retrieveNewYorkTimesData(Request $request)
    {
        // API endpoint and API key
        $url = 'https://api.nytimes.com/svc/search/v2/articlesearch.json';
        $apiKey = env("NEW_YORK_TIMES_KEY", null);

        $keyword = $request->input('keyword');
        $dateFilter = $request->input('date');
        $categoryFilter = $request->input('category');
        $sourceFilter = $request->input('source');
        $page = $request->input('page', 1);

        $params = array(
            'q' => $keyword,
            'api-key' => $apiKey,
            'fq' => array(),
            'page' => $page,
        );

        if (!empty($dateFilter)) {
            $params['fq'][] = 'pub_date:[' . $dateFilter . ' TO NOW]';
        }
        if (!empty($categoryFilter)) {
            $params['fq'][] = 'section_name:"' . $categoryFilter . '"';
        }
        if (!empty($sourceFilter)) {
            $params['fq'][] = 'source:"' . $sourceFilter . '"';
        }

        $queryString = http_build_query($params);

        $response = Http::get($url . '?' . $queryString);
        $data = $response->json();

        if (isset($data['response']['docs'])) {
            $articles = $data['response']['docs'];
            $totalResults = $data['response']['meta']['hits'];
            $resultsPerPage = count($articles);
            $currentPage = $page;
            $lastPage = ceil($totalResults / $resultsPerPage);

            return response()->json([
                'articles' =>  $articles,
                'totalResults' => $totalResults,
                'resultsPerPage' => $resultsPerPage,
                'currentPage' => $currentPage,
                'lastPage' => $lastPage,
            ], 200);
        } else {
            return response()->json([
                'articles' =>  [],
                'totalResults' => 0,
                'resultsPerPage' => 0,
                'currentPage' => $page,
                'lastPage' => 1,
            ], 200);
        }
    }


    public function paginateData(Request $request)
    {


        // // Display results to user
        // if (isset($data['response']['docs'])) {
        //     $articles = $data['response']['docs'];
        //     return view('articles', compact('articles'));
        //     return response()->json($parseResponse);
        // } else {
        //     return view('articles', ['articles' => []]);
        // }



        // $apiKey = env("NEW_YORK_TIMES_KEY", null);

        // $q = $request->get("keyword");
        // $date = $request->get("fromDate");
        // // $section = $request->get("source");

        // $page = $request->get("page");

        // $part_url = "";

        // if ($q != null) {
        //     $part_url .= "&q=$q";
        // }
        // if ($page != null) {
        //     $part_url .= "&page=$page";
        // }
        // // if ($section != null) {
        // //     $part_url .= "&section=$section";
        // // }
        // if ($date != null) {
        //     $part_url .= "&from=$date";
        // }

        // $url = "https://api.nytimes.com/svc/search/v2/articlesearch.json?";
        // $url .= substr($part_url, 1);
        // $url .= "&api-key=$apiKey";

        // $response = Http::get($url);
        // $parseResponse = $response->json($key = null, $default = null);
        // // https://api.nytimes.com/svc/search/v2/articlesearch.json?q=tiktok&&page=200&api-key=ta1II5cb1dN5i9YYF0rcikXhUz5RQPSN

        // return response()->json($parseResponse);

        // $redis = Redis::connection();

        // $articles = $parseResponse['articles'];
        // $redis->set('user_details', json_encode($articles));
        $redis    = Redis::connection();
        $response = $redis->get('user_details');

        $response = json_decode($response);
    }
}
