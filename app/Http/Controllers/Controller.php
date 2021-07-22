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
        $matrix = $this->createMatrix(3,3);
        dd($matrix);
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

    function createMatrix($m, $n) {
        $array = [];

        for ($a=0; $a < $m; $a++) { 
            $sub = [];
            for ($b=0; $b < $n; $b++) { 
                $sub[] = 1;
            }
            $array[] = $sub;
        }

        return $array;
    }
}
