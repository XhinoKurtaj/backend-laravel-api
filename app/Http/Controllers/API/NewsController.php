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

        $url = "https://newsapi.org/v2/everything";

        $keyword = $request->get('keyword');
        $date = $request->get('date');
        $category = $request->get('category');
        $source = $request->get('source');

        $params = array(
            'q' => $keyword,
            'from' => base64_encode($date),
            'category' => $category,
            'sources' => $source,
            'apiKey' => $apiKey
        );

        $queryString = http_build_query($params);
        $response = Http::get($url . '?' . $queryString);
        $data = $response->json();

        if (isset($data['articles'])) {
            $articles = $data['articles'];
            $status = $data['status'];
            $totalResults = $data['totalResults'];


            return response()->json([
                'articles' =>  $articles,
                'totalResults' => $totalResults,
                'status' => $status,
            ], 200);
        } else {
            return response()->json([
                'articles' =>  [],
                'totalResults' => 0,
                'status' => 400,
            ], 200);
        }
    }

    public function retrieveTheGuardianNews(Request $request)
    {
        $apiKey = env("THE_GUARDIAN_KEY", null);
        $url = "https://content.guardianapis.com/search";

        $keyword = $request->get('keyword');
        $date = $request->get('date');
        $category = $request->get('category');
        $source = $request->get('source');
        $page = $request->get('page', 1);

        $params = array(
            'q' => $keyword,
            'from-date' => $date,
            'section' => $category,
            'source' => $source,
            'api-key' => $apiKey,
            'page' => $page,
        );

        $queryString = http_build_query($params);
        $response = Http::get($url . '?' . $queryString);
        $data = $response->json();

        if (isset($data['response'])) {
            $articles = $data['response']['results'];
            $totalResults = $data['response']['total'];
            $resultsPerPage = $data['response']['pageSize'];
            $currentPage = $page;
            $lastPage = $data['response']['pages'];

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
}
