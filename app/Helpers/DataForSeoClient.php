<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class DataForSeoClient
{
    protected $baseUrl;
    protected $userName;
    protected $password;
    public $priority;

    public function __construct($search_engine = 'google')
    {
        $this->baseUrl = $search_engine == 'google'?Config::get('dataforseo.google_base_url'):Config::get('dataforseo.bing_base_url');
        $this->userName = Config::get('dataforseo.user_name');
        $this->password = Config::get('dataforseo.password');
        $this->priority = Config::get('dataforseo.priority');
    }

    public function taskPost($data){

        $response = Http::withBasicAuth($this->userName, $this->password)
            ->withBody(json_encode($data), 'application/json')
            ->post($this->baseUrl.'/task_post');
        return $response;
    }
    public function getRegularTask($taskId){
        $response = Http::withBasicAuth($this->userName, $this->password)
            ->post($this->baseUrl.'/task_get/regular/'.$taskId);
        return $response;
    }



}
