<?php

namespace App\Http\Controllers;

use App\Helpers\AnalyzerHelper;
use App\Helpers\Helper;
use App\Jobs\Analyze;
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
use App\Exports\AnalyzerResultsExport;
use Maatwebsite\Excel\Facades\Excel;

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
        $search = Search::find($id);
        if(empty($search)){
            return redirect()->back()->withErrors(['Search result not found']);
        }
        Analyze::dispatch($request->all(), $search);
        Session::put('urls' , json_decode($request->urls));
        return redirect(route('analyzer_results', $id));
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


    public function indexAnalyzerResults(Request $request, $id)
    {
        $urls = [];
        if(Session::has('urls')){
            $urls = Session::get('urls');
        }
        return view('analyzer-results')->with(['id' => $id, 'urls' => $urls]);
    }

    public function getAnalyzerResults(Request $request){
        try {
            $totalRequests = count($request->urls);
            $urls = AnalyzerResult::whereIn('url', $request->urls)->where('default', 0)->get()->groupBy('url');
            $completedRequests = $urls->count();
            $search = Search::find($request->id);
            $defaultUrlData = [];
            $defaultUrl = null;
            if($search){
                $cleanKeywords = explode(' ', $this->strip_stopwords($search->keyword));
                $defaultUrl = AnalyzerResult::where('url', $search->compare_with)->where('default', 1)->first();
                if($defaultUrl){
                    $defaultUrlData = $this->setEntityData($defaultUrl, $cleanKeywords);
                }
            }
            $data = [];
            $analyticsData = [];
            foreach ($urls as $url => $results) {
                $analyzerData = [];
                foreach ($results as $result) {
                    if ($result->analyzer == AnalyzerResult::$TEXT_RAZOR) {
                        $analyzerData[AnalyzerResult::$TEXT_RAZOR] = $this->setEntityData($result, $cleanKeywords);
                    }
                }
                $analyticsData['wordCount'][] = ['url' => $url, 'count' => $analyzerData[AnalyzerResult::$TEXT_RAZOR]['wordCount']];
                $analyticsData['keyword'][] = ['url' => $url, 'count' => $analyzerData[AnalyzerResult::$TEXT_RAZOR]['keywordCount']];
                $analyticsData['entites'][] = ['url' => $url, 'count' => $analyzerData[AnalyzerResult::$TEXT_RAZOR]['wikiData']];
                $analyticsData['lsi'][] = ['url' => $url, 'count' => $analyzerData[AnalyzerResult::$TEXT_RAZOR]['lsData']];
                $analyticsData['rw'][] = ['url' => $url, 'count' => $analyzerData[AnalyzerResult::$TEXT_RAZOR]['relevantWords']];
                $analyticsData['rd'][] = ['url' => $url, 'count' => $analyzerData[AnalyzerResult::$TEXT_RAZOR]['relevantDensity']];
                $data[] = ['url' => $url, 'data' => $analyzerData];
            }
            [$collectiveData, $averageData] = $this->setCollectiveData($data);
            if($defaultUrl && count($defaultUrlData) > 0 && !isset($defaultUrlData['error'])){
                $defUrl = $defaultUrl->url;
                $analyticsData['wordCount'][] = ['url' => $defUrl, 'count' => $defaultUrlData['wordCount']];
                $analyticsData['keyword'][] = ['url' => $defUrl, 'count' =>$defaultUrlData['keywordCount']];
                $analyticsData['entites'][] = ['url' => $defUrl, 'count' =>$defaultUrlData['wikiData']];
                $analyticsData['lsi'][] = ['url' => $defUrl, 'count' =>$defaultUrlData['lsData']];
                $analyticsData['rw'][] = ['url' => $defUrl, 'count' =>$defaultUrlData['relevantWords']];
                $analyticsData['rd'][] = ['url' => $defUrl, 'count' =>$defaultUrlData['relevantDensity']];
            }else{
                $analyticsData['wordCount'][] = ['url' => '', 'count' => 0];
                $analyticsData['keyword'][] = ['url' => '', 'count' =>0];
                $analyticsData['entites'][] = ['url' => '', 'count' =>0];
                $analyticsData['lsi'][] = ['url' => '', 'count' =>0];
                $analyticsData['rw'][] = ['url' => '', 'count' =>0];
                $analyticsData['rd'][] = ['url' => '', 'count' =>0];
            }


            array_unshift($analyticsData['wordCount'],$averageData['wordCount']);
            array_unshift($analyticsData['keyword'], $averageData['keywordCount']);
            array_unshift($analyticsData['entites'], $averageData['wikiData']);
            array_unshift($analyticsData['lsi'],$averageData['lsData']);
            array_unshift($analyticsData['rw'], $averageData['relevantWords']);
            array_unshift($analyticsData['rd'], $averageData['relevantDensity']);

            return response()->json(['data' => $data, 'defaultUrlData' => $defaultUrlData,
                'collectiveData' => $collectiveData, 'averageData' => $averageData, 'analyticsData' => $analyticsData,
                'totalRequests' => $totalRequests, 'completedRequests' => $completedRequests]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }

    }
    public function setEntityData($result, $cleanKeywords){
        $response = $result->results;
        $entitiesData = [];
        $wikiData = [];
        $lsData = [];
        $text = $this->generateText($result->html);
        $wordCount = $this->getWordCount($text);
        $keywordCount = 0;
        foreach ($cleanKeywords as $keyword){
            $keywordCount += $this->getCountInHtml($text, $keyword);
        }

        if (isset($response['response']['entities'])) {
            $entities = $response['response']['entities'];
            $groupEntities = collect($entities)->reject(function ($value, $key) {
                return is_numeric($value['entityId']);
            })->groupBy('entityId');
            foreach ($groupEntities as $entity => $entityData) {
                $first = $entityData->first();
                $data = [
                    'entity' => $entity,
                    'matchedText' => $first['matchedText'],
                    'type' => ($first['type'] ?? []),
                    'freebaseTypes' => ($first['freebaseTypes'] ?? []),
                    'confidenceScore' => ($first['confidenceScore'] ?? ''),
                    'relevanceScore' => ($first['relevanceScore'] ?? ''),
                    'count' => count($entityData),
                    'htmlCount' => $this->getCountInHtml($text, $first['matchedText'])
                ];
                $entitiesData[]= $data;
                if(isset($first['wikiLink'])){
                    $data['wikiLink'] = $first['wikiLink'];
                    $wikiData[]= $data['htmlCount'];
                }else{
                    $lsData[]= $data['htmlCount'];
                }
            }
        }elseif (isset($response['error'])){
            $entitiesData[]= ['error'=> $response['error']];
        }
        $relevantWords = array_sum($wikiData)+count($lsData)+$keywordCount;
        $relevantDensity = ($wordCount > 0 ? number_format(($relevantWords/$wordCount)*100, 3) : 0);
        return ['entitiesData' => $entitiesData, 'wikiData' => array_sum($wikiData),
            'lsData' => array_sum($lsData), 'wordCount' => $wordCount, 'keywordCount' => $keywordCount,
            'relevantWords' => $relevantWords, 'relevantDensity' => $relevantDensity];
    }
    // remove stopwords from string
    function strip_stopwords($str = "")
    {
        $stopwords = Helper::getStopWords();

        // 1.) break string into words
        // [^-\w\'] matches characters, that are not [0-9a-zA-Z_-']
        // if input is unicode/utf-8, the u flag is needed: /pattern/u
        $words = preg_split('/[^-\w\']+/', $str, -1, PREG_SPLIT_NO_EMPTY);

        // 2.) if we have at least 2 words, remove stopwords
        if(count($words) > 1)
        {
            $words = array_filter($words, function ($w) use (&$stopwords) {
                return !in_array(strtolower($w), $stopwords);
                # if utf-8: mb_strtolower($w, "utf-8")
            });
        }

        // check if not too much was removed such as "the the" would return empty
        if(!empty($words))
            return implode(" ", $words);
        return $str;
    }

    public function setCollectiveData($data){
        $mergedData = [];
        $avgKeyword = 0;
        $avgWordCount = 0;
        $avgEntity = 0;
        $avgLsiWords = 0;
        $avgRelevantWords = 0;
        $avgRelevantDensity = 0;
        collect($data)->pluck('data.TEXTRAZOR')->map(function( $value, $key) use (&$mergedData,&$avgKeyword,
            &$avgWordCount,&$avgEntity,&$avgLsiWords,&$avgRelevantWords,&$avgRelevantDensity){
            $mergedData = array_merge($value['entitiesData'], $mergedData);
            $avgKeyword += $value['keywordCount'];
            $avgWordCount += $value['wordCount'];
            $avgEntity += $value['wikiData'];
            $avgLsiWords += $value['lsData'];
            $avgRelevantWords += $value['relevantWords'];
            $avgRelevantDensity += $value['relevantDensity'];
        });
        $total = count($data);
        if($total > 0){
            $avgData = [
                'keywordCount' => number_format($avgKeyword/$total, 3),
                'wordCount' => number_format($avgWordCount/$total, 3),
                'wikiData' => number_format($avgEntity/$total, 3),
                'lsData' => number_format($avgLsiWords/$total, 3),
                'relevantWords' => number_format($avgRelevantWords/$total, 3),
                'relevantDensity' => number_format($avgRelevantDensity/$total, 3),
            ];
        }else{
            $avgData = [
                'keywordCount' => 0,
                'wordCount' => 0,
                'wikiData' => 0,
                'lsData' => 0,
                'relevantWords' => 0,
                'relevantDensity' => 0,
            ];
        }
        $collectiveData = [];
        collect($mergedData)->groupBy('entity')->each(function($item, $key) use(&$collectiveData){
            $collectiveData[$key] = ['count' => [$item->min('count'), $item->max('count')],
            'htmlCount' => [$item->min('htmlCount'), $item->max('htmlCount')]
            ];
        });

        return [$collectiveData, $avgData];
    }
    public function generateText($html){
        $dom = new Dom;
        $dom->loadStr($html);
        $text = [];
        $this->getHtmlText($dom->find('body')[0], $text);
        return $text;
    }

    public function getHtmlText($node, &$text){
        if($node){
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

    public function getWordCount($text){
        $count = 0;
        $text = implode(' ', $text);
        $splittedText = explode(' ', $text);
        collect($splittedText)->map(function($word, $index) use (&$count){
            if(strlen($word) > 3 && !is_numeric($word)){
                $count ++;
            }
        });
        return $count;
    }
    public function getCountInHtml($text, $entity){
      return substr_count(strtolower(implode(' ', $text)), strtolower($entity));
    }


    public function exportCollectiveResults(Request $request)
    {
        return Excel::download(new AnalyzerResultsExport($request->data), 'analyzer_results.xlsx');
    }

}
