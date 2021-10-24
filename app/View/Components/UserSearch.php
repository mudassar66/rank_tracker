<?php

namespace App\View\Components;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class UserSearch extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public $userSearches;
    public function __construct()
    {
        $this->userSearches = Auth::user()->searches()->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.user-search');
    }
}
