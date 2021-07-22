<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index()
    {
        $data = null;
        return view('welcome')->with('data',$data);
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'rows' => 'required|numeric',
            'columns' => 'required|numeric',
        ]);

        $data = 12;

        return view('welcome')->with('data',$data);
    }
}
