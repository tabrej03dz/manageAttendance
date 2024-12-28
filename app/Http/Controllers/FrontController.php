<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function mainpage()
    {
        return view('mainpage.index');
    }

    public function blogs()
    {
        return view('mainpage.blog');
    }

    public function blogDetailsPage()
    {
        return view('mainpage.blogDetailsPage');
    }

    public function reqDemo()
    {
        return view('mainpage.reqaDemo');
    }
    public function thankyou()
    {
        return view('mainpage.thankyou');
    }


}
