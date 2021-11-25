<?php

namespace App\Http\Controllers;

use App\Helpers\DataForSeoClient;
use App\Helpers\Helper;
use App\Models\Search;
use App\Models\SearchIteration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Send api call
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function taskPost(Request $request)
    {
        try {

            $countries = Helper::getCountries();
            $validator = Validator::make($request->all(), [
                'search_engine' => 'required',
                'compare_with' => 'required',
                'keyword' => 'required|string|max:500',
                'country' => 'required|string|in:' . implode(',', array_keys($countries)),
                'device' => 'required|string|in:' . implode(',', array_keys(Helper::getDevices())),
                'iterations_count' => 'required|numeric|min:1|max:100'
            ]);

            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator->errors());
            }
            $input = $request->all();
            $input['user_id'] = Auth::user()->id;
            $search = Search::create($input);

            $requestData = [];
            $client = new DataForSeoClient($request->search_engine);
            for ($i = 1; $i <= $request->iterations_count ;$i++) {
                $data['language_code'] = 'en';
                $data['location_name'] = $countries[$request->country];
                $data['device'] = $request->device;
                $data['keyword'] = $request->keyword;
                $data['priority'] = $client->priority;
                $data['postback_url'] = url('api/postbackscript');
                $data['postback_data'] = 'regular';
                array_push($requestData, $data);
            }

            $response = $client->taskPost($requestData);
            if($response->successful()){
                $resData = json_decode($response->body(), true);
                if(isset($resData['status_code'])){
                    if($resData['status_code'] == 20000){
                        Log::info('Post task response received', $resData);
                        foreach ($resData['tasks'] as $task){
                             Log::info('Posted Task###################', $task);
                            SearchIteration::create([
                                'search_id' => $search->id,
                                'task_id' => $task['id']
                            ]);
                        }
                        Flash::success('Request send successfully.');
                    }else{
                        Flash::error($resData['status_message']);
                    }
                }else{
                    Log::info($resData);
                    Flash::error('Something went wrong.');
                }

            }else{
                Flash::error('Something went wrong with API.');
            }
        }
        catch (\Exception $e){
            Flash::success($e->getMessage());
        }
    return Redirect::back();
    }

    /**
     * Get results
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function taskPostBackScript(Request $request)
    {
        try {

           Log::info('Response received from Dataforseo');
            $client = new DataForSeoClient();
            $post_arr = json_decode(gzdecode($request->getContent()), true);
            Log::info('Decoded response', $post_arr);
            // you can find the full list of the response codes here https://docs.dataforseo.com/v3/appendix/errors
            if (isset($post_arr['status_code']) AND $post_arr['status_code'] === 20000) {
                Log::info('Success response received');
                foreach($post_arr['tasks'] as $task){
                     Log::info('Task Data', $task);
                     if($task['status_code'] == 20000){
                         $taskId = $task['id'];
                         Log::info('Task ID####'.$taskId);
                        $iteration = SearchIteration::where('task_id', $taskId)->first();
                        if(!empty($iteration)){
                            $iteration->update(['search_results' => $task]);
                            $parent = $iteration->parent;
                            $total = $parent->iterations()->count();
                             $notCompleted = $parent->iterations()->where('search_results', null)->count();
                            if($notCompleted == 0){
                                $parent->update(['status' => 'COMPLETED']);
                            }else if($notCompleted < $total){
                                 $parent->update(['status' => 'PARTIAL_COMPLETED']);
                            }
                        }
                     }else{
                          Log::error('Error on task', $task['status_message']);
                     }

                }

            } else {
               Log::error('Not success');
            }

        }
        catch (\Exception $e){
            Log::error($e->getMessage());
        }

    }
}
