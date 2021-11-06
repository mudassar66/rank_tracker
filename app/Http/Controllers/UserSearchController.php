<?php

namespace App\Http\Controllers;

use App\Models\Search;
use http\Client\Response;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use TextRazor;
use TextRazorSettings;

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
        if(empty($search)){

        }
        $iterations = $search->iterations()->orderBy('updated_at', 'desc')->get();
        $data = [];
        $count = [];
        $labels = [];
        $total =$iterations->count();
        $completed =$iterations->where('search_results','!=', null)->count();
        foreach ($iterations as $index => $iteration){
            if(!is_null($iteration->search_results)){
                $searchResults = json_decode($iteration->search_results, true);
                // dd($searchResults);
                $items = $searchResults['result'][0]['items'];
                $data = array_merge($data,$items);

                $count[] = "IT-".($index+1)."-".Carbon::createFromFormat('Y-m-d H:i:s', $iteration->updated_at)->format('Y/m/d H:i:s');

            }
        }
        $graphData = [];
        $tableData = [];
        $groupedData = collect($data)->groupBy('url');
        $boxplot_data = [];
        foreach ($groupedData as $site => $group){
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
                'data'=> [],
                'lineTension'=> 0,
                'fill'=> false,
                'borderWidth' => 1.5,
                'borderColor'=> $color,
                'backgroundColor' => $color
            ];
            $labels[] = $group[0]['url'];

            foreach($iterations as $index => $iteration){
                if(!is_null($iteration->search_results)){
                    $searchResults = json_decode($iteration->search_results, true);
                    $items = collect($searchResults['result'][0]['items'])->where('url',$site );
                    $rank = [];
                    $urls = [];
                    if(count($items) == 0){
                        $rank[] = 'X';
                    }
                    foreach($items as $item){
                        if($item['type'] != 'organic')
                            continue;
                        $data['data'][] = [
                            'x' =>"IT-".($index+1)."-".Carbon::createFromFormat('Y-m-d H:i:s', $iteration->updated_at)->format('Y/m/d H:i:s'),
                            'y' =>  $item['rank_absolute']
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
        foreach ($tableData as $url=>$url_data){
            $boxplot_data[] = $url_data['ranks'];
        }
//        dump($boxplot_data);
//        echo "<pre>";
//        print_r($graphData);
//        echo "</pre>";

        return view('search-results')->with(['graphData' => ['labels' => $count, 'datasets' => $graphData], 'tableData' => $tableData, 'total' => $total, 'completed' => $completed, 'boxpot_data'=>$boxplot_data, 'boxpot_data_labels'=>$labels]);
    }

    public function analyze(Request $request){
        try {
            foreach ($request->urls as $url){
                TextRazorSettings::setApiKey(env('TEXTRAZOR_APIKEY', 'c4fc1a9f2a97b5303ae3411ce54b328edbc54bea4c8a34c3bb630402'));
                $text = 'Barclays misled shareholders and the public about one of the biggest investments in the banks history, a BBC Panorama investigation has found.';
                $textrazor = new TextRazor();
                $textrazor->addExtractor('entities');
                $response = $textrazor->analyzeUrl($url);
                $data = [];
                if (isset($response['response']['entities'])) {
                    $data[$url] = collect($response['response']['entities'])->groupBy('entityId');
                }else{
                    $data[$url] = [];
                }
            }
            Log::info('Textrazor response', $data);
            return response()->json(['data' => $data]);
        }catch (\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
