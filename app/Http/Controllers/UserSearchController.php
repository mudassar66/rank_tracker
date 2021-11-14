<?php

namespace App\Http\Controllers;

use App\Helpers\AnalyzerHelper;
use App\Models\AnalyzerResult;
use App\Models\Search;
use http\Client\Response;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use TextRazor;
use TextRazorSettings;
use PHPHtmlParser\Dom;

class UserSearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $search = Search::find($id);
        if (empty($search)) {

        }
        $iterations = $search->iterations()->orderBy('updated_at', 'desc')->get();
        $data = [];
        $count = [];
        $labels = [];
        $total = $iterations->count();
        $completed = $iterations->where('search_results', '!=', null)->count();
        foreach ($iterations as $index => $iteration) {
            if (!is_null($iteration->search_results)) {
                $searchResults = json_decode($iteration->search_results, true);
                // dd($searchResults);
                $items = $searchResults['result'][0]['items'];
                $data = array_merge($data, $items);

                $count[] = "IT-" . ($index + 1) . "-" . Carbon::createFromFormat('Y-m-d H:i:s', $iteration->updated_at)->format('Y/m/d H:i:s');

            }
        }
        $graphData = [];
        $tableData = [];
        $groupedData = collect($data)->groupBy('url');
        $boxplot_data = [];
        foreach ($groupedData as $site => $group) {
            $color = sprintf('#%06X', mt_rand(1, 0xFFFFFF));
            $tableData[$site] = [
                'title' => $group[0]['title'],
                'description' => $group[0]['description'],
                'url' => $group[0]['url'],
                'domain' => $group[0]['domain'],
                'ranks' => []
            ];
            $data = [
                'label' => "$site",
                'data' => [],
                'lineTension' => 0,
                'fill' => false,
                'borderWidth' => 1.5,
                'borderColor' => $color,
                'backgroundColor' => $color
            ];
            $labels[] = $group[0]['url'];

            foreach ($iterations as $index => $iteration) {
                if (!is_null($iteration->search_results)) {
                    $searchResults = json_decode($iteration->search_results, true);
                    $items = collect($searchResults['result'][0]['items'])->where('url', $site);
                    $rank = [];
                    $urls = [];
                    if (count($items) == 0) {
                        $rank[] = 'X';
                    }
                    foreach ($items as $item) {
                        if ($item['type'] != 'organic')
                            continue;
                        $data['data'][] = [
                            'x' => "IT-" . ($index + 1) . "-" . Carbon::createFromFormat('Y-m-d H:i:s', $iteration->updated_at)->format('Y/m/d H:i:s'),
                            'y' => $item['rank_absolute']
                        ];
                        $rank[] = $item['rank_absolute'];
//                        $tableData[$site]['url'][] = $item['url'];
                    }
                    $tableData[$site]['ranks'][] = implode(',', $rank);
                }
            }
            $graphData[] = $data;
        }

        // Box plot prepare data
        foreach ($tableData as $url => $url_data) {
            $boxplot_data[] = $url_data['ranks'];
        }
//        dump($boxplot_data);
//        echo "<pre>";
//        print_r($graphData);
//        echo "</pre>";

        return view('search-results')->with(['id' => $id, 'graphData' => ['labels' => $count, 'datasets' => $graphData], 'tableData' => $tableData, 'total' => $total, 'completed' => $completed, 'boxpot_data' => $boxplot_data, 'boxpot_data_labels' => $labels]);
    }


    public function analyze(Request $request, $id)
    {
        try {
            foreach (json_decode($request->urls) as $url) {
                foreach (AnalyzerResult::$analyzers as $analyzer) {
                    $result = AnalyzerResult::where('analyzer', $analyzer)->where('url', $url)->first();
                    if (empty($result) || $request->has('renew_all')) {
                        $analyzerHelper = new AnalyzerHelper();
                        switch ($analyzer) {
                            case AnalyzerResult::$TEXT_RAZOR:
                                $response = $analyzerHelper->getTextRazorResults($url);
                                $html = $this->getHtml($url);
                                $a = AnalyzerResult::UpdateOrCreate([
                                    'url' => $url,
                                    'analyzer' => $analyzer,
                                ], [
                                    'results' => $response,
                                    'html' => $html
                                ]);
                                break;
                            case AnalyzerResult::$WATSON:
                                break;
                        }
                    }
                }
            }
            return view('analyzer-results')->with(['id' => $id, 'urls' => json_decode($request->urls)]);
//            Session::put('urls',  json_decode($request->urls));
//             return redirect(route('analyzer_results', ['id' => $id]));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }
    }

    public function reanalyzeUrl(Request $request)
    {
        try {
            $url = $request->url;
            $data = [];
            $analyzerData = [];
            foreach (AnalyzerResult::$analyzers as $analyzer) {
                $analyzerHelper = new AnalyzerHelper();
                switch ($analyzer) {
                    case AnalyzerResult::$TEXT_RAZOR:
                        $response = $analyzerHelper->getTextRazorResults($url);
                        $html = $this->getHtml($url);
                        $result = AnalyzerResult::UpdateOrCreate([
                            'url' => $url,
                            'analyzer' => $analyzer,
                        ], [
                            'results' => $response,
                            'html' => $html
                        ]);
                        if (isset($response['response']['entities'])) {
                            $analyzerData[$result->analyzer] = $this->setEntityData($result);
                        } else {
                            $analyzerData[$result->analyzer] = [];
                        }
                        break;
                    case AnalyzerResult::$WATSON:
                        break;
                }
            }
            $data = ['url' => $url, 'data' => $analyzerData];
            return response()->json(['data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function indexAnalyzerResults($id)
    {
        return view('analyzer-results')->with(['id' => $id]);
    }

    public function getAnalyzerResults(Request $request){
        try {
            $urls = AnalyzerResult::whereIn('url', $request->urls)->get()->groupBy('url');
            $data = [];
            foreach ($urls as $url => $results) {
                $analyzerData = [];
                foreach ($results as $result) {
                    if ($result->analyzer == AnalyzerResult::$TEXT_RAZOR) {
                        $analyzerData[AnalyzerResult::$TEXT_RAZOR] = $this->setEntityData($result);
                    }
                }
                $data[] = ['url' => $url, 'data' => $analyzerData];
            }
    
            $collectiveData = $this->setCollectiveData($data);
            return response()->json(['data' => $data, 'collectiveData' => $collectiveData]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }

    }
    public function setEntityData($result){
        $response = $result->results;
        $entitiesData = [];
        if (isset($response['response']['entities'])) {
            $entities = $response['response']['entities'];
            $groupEntities = collect($entities)->reject(function ($value, $key) {
                return is_numeric($value['entityId']);
            })->groupBy('entityId');
            $html = $result->html;
            if(empty($html)){
                $html = $this->getHtml($result->url);
                $result->update(['html' => $html]);
            }
            foreach ($groupEntities as $entity => $entityData) {
                $first = $entityData->first();
            
                $entitiesData[]= [
                    'entity' => $entity,
                    'matchedText' => $first['matchedText'],
                    'type' => ($first['type'] ?? []),
                    'freebaseTypes' => ($first['freebaseTypes'] ?? []),
                    'confidenceScore' => ($first['confidenceScore'] ?? ''),
                    'relevanceScore' => ($first['relevanceScore'] ?? ''),
                    'count' => count($entityData),
                    'htmlCount' => $this->getCountInHtml($html, $first['matchedText'])
                ];
            }
        }
        return $entitiesData;
    }

    public function setCollectiveData($data){
        $mergedData = [];
        collect($data)->pluck('data.TEXTRAZOR')->map(function( $value, $key) use (&$mergedData){
            $mergedData = array_merge($value, $mergedData);
        });
        $data = [];
        collect($mergedData)->groupBy('entity')->each(function($item, $key) use(&$data){
            $data[$key] = ['count' => [$item->min('count'), $item->max('count')],
            'htmlCount' => [$item->min('htmlCount'), $item->max('htmlCount')]
        ];
        });
        return $data;
    }

    public function getHtml($url){
        $dom = new Dom;
        $dom->loadFromUrl($url);
        return $dom->outerHtml;
    }

    public function getCountInHtml($html, $entity){
        $dom = new Dom;
        $dom->loadStr($html);
        $text = [];
        $this->getHtmlText($dom->find('body')[0], $text);
        // if(strtolower($entity) == 'projectors')
        //     dd(substr_count(strtolower(implode(' ', $text)), strtolower($entity)),strtolower(implode(' ', $text)), strtolower($entity));
        return substr_count(strtolower(implode(' ', $text)), strtolower($entity));
    }


    public function getHtmlText($node, &$text){
        if(get_class($node) == "PHPHtmlParser\Dom\Node\HtmlNode"){
            if(trim($node->text) != ""){
                $text[] = trim($node->text);
            }
            $children = $node->getChildren();
            foreach($children as $childNode){
                $this->getHtmlText($childNode, $text);
            }
        }
        
    }

}
