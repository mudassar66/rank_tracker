<?php

namespace App\Http\Controllers;

use App\Models\Search;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
        $groupedData = collect($data)->groupBy('domain');
        foreach ($groupedData as $site => $group){
            $color = sprintf('#%06X', mt_rand(1, 0xFFFFFF));
            $tableData[$site] = [
                'title' => $group[0]['title'],
                 'description' => $group[0]['description'],
                  'url' => [],
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
            foreach($iterations as $index => $iteration){
                if(!is_null($iteration->search_results)){
                    $searchResults = json_decode($iteration->search_results, true);
                    $items = collect($searchResults['result'][0]['items'])->where('domain',$site );
                    $rank = [];
                    $urls = [];
                    if(count($items) == 0){
                         $rank[] = 'X';
                    }
                    foreach($items as $item){
                        $data['data'][] = ['x' =>"IT-".($index+1)."-".Carbon::createFromFormat('Y-m-d H:i:s', $iteration->updated_at)->format('Y/m/d H:i:s'), 'y' =>  $item['rank_group']];
                        $rank[] = $item['rank_group'];
                        $tableData[$site]['url'][] = $item['url'];
                    }
                    $tableData[$site]['ranks'][] = implode(',', $rank);
                }
            }
             $graphData[] = $data;
        }
    
        return view('search-results')->with(['graphData' => ['labels' => $count, 'datasets' => $graphData], 'tableData' => $tableData, 'total' => $total, 'completed' => $completed]);
    }
}
