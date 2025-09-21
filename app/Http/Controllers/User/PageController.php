<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

class PageController extends Controller
{
    public function brandStory()
    {
        return view('user.pages.brand-story');
    }
}


