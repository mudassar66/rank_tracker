<?php

namespace App\Helpers;

use TextRazor;
use TextRazorSettings;

class AnalyzerHelper
{
    public function __construct()
    {
    }

    public function getTextRazorResults($url){
        TextRazorSettings::setApiKey(env('TEXTRAZOR_APIKEY', 'c4fc1a9f2a97b5303ae3411ce54b328edbc54bea4c8a34c3bb630402'));
        $textrazor = new TextRazor();
        $textrazor->addExtractor('entities');
        $response = $textrazor->analyzeUrl($url);
        return $response;

    }
}
