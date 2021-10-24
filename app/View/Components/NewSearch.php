<?php

namespace App\View\Components;

use App\Helpers\Helper;
use Illuminate\View\Component;

class NewSearch extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public $countries;
    public $devices;
    public function __construct()
    {
        $this->devices = Helper::getDevices();
        $this->countries = Helper::getCountries();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.new-search');
    }
}
