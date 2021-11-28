<?php

namespace App\Jobs;

use App\Helpers\AnalyzerHelper;
use App\Helpers\Helper;
use App\Models\AnalyzerResult;
use App\Models\Search;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use PHPHtmlParser\Dom;

class Analyze implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $urls;
    protected $search;
    protected $renew_all;
    protected $analyzerHelper;

    public function __construct($input, $search)
    {
        $this->urls = $input['urls'];
        $this->search = $search;
        $this->renew_all = isset($input['renew_all']) ?? false;
        $this->analyzerHelper = new AnalyzerHelper();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $urls = json_decode($this->urls);
            $search = $this->search;
            if($search){
                $compare_with = $search->compare_with;
                if(!empty($compare_with)){
                    $this->analyze($compare_with, 1);
                }
            }
            foreach ($urls as $url) {
                $this->analyze($url);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
    public function getHtml($url){
        $page = '';
        //Read a web page and check for errors:
        $result = Helper::get_web_page( $url);
        if ( $result['errno'] != 0 ) {
            //error: bad url, timeout, redirect loop ...
            $page = 'Bad url or timeout';
        }
        if ( $result['http_code'] != 200 ) {
            //error: no page, no permissions, no service ...
            $page = 'No page, no service, no permission';
        }
        if($page == ''){
            $page = $result['content'];
        }
        return $page;
    }

    private function analyze($url, $default=0){
        foreach (AnalyzerResult::$analyzers as $analyzer) {
            $result = AnalyzerResult::where('analyzer', $analyzer)->where('url', $url)
                ->where('default', $default)->first();
            if (empty($result) || $this->renew_all) {
                switch ($analyzer) {
                    case AnalyzerResult::$TEXT_RAZOR:
                        $response = $this->analyzerHelper->getTextRazorResults($url);
                        $html = '';
                        if (isset($response['response']['entities'])) {
                            $html = $this->getHtml($url);
                        }
                        $result = AnalyzerResult::UpdateOrCreate([
                            'url' => $url,
                            'analyzer' => $analyzer,
                            'default' => $default
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
}
