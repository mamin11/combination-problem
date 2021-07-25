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

        // $data = $this->usingFormula($request->rows, $request->columns);
        // $data = $this->withoutFormula($request->columns, $request->rows);
        // dd((int)$request->rows, (int)$request->columns);
        $data = $this->longSolution((int)$request->rows, (int)$request->columns); //results in different answer without type casting

        return view('welcome')->with('data',$data);
    }
    public function longSolution($m, $n)
    {
        // $m = 2;
        // $n = 3;
        // dd($this->getEachNumber(4), $this->getEachNumber(5));
        $combinations = $this->getCombinations($this->getEachNumber($m), $this->getEachNumber($n));
        // // dd($combinations);
        $combinationsWithoutDuplicates = $this->removeDuplicatesFromCombination($combinations);
        // // dd($combinationsWithoutDuplicates);
        // // dd($this->getRectanglesInRows([[1,2,3,4], [5,6,7,8], [9,10,11,12]], [3,2]));
        // dd($this->getRectanglesInCols([[1,2,3,4], [5,6,7,8], [9,10,11,12]], [2,3]));
        $matrix = $this->createMatrix($m,$n);
        // // dd($matrix);
        $total = 0;
        $totalInRows = 0;
        $totalInCols = 0;

        foreach ($combinationsWithoutDuplicates as $combination) {
            if($combination[0] > $combination[1]) {
                $totalInRows += $this->getRectanglesInRows($matrix, $this->getSwappedArrayValues($combination));
                $totalInCols += $this->getRectanglesInCols($matrix, $combination);
            } else {
                $totalInRows += $this->getRectanglesInRows($matrix, $combination);
                $totalInCols += $this->getRectanglesInCols($matrix, $this->getSwappedArrayValues($combination));
            }
        }
        $total = $totalInRows + $totalInCols;
        // dd($total);
        return $total;
    }
    
    public function usingFormula($m, $n)
    {
        return (($m*($m+1))/2) * (($n*($n+1))/2);
    }
    
    function withoutFormula($m, $n) {
        $mSum = 0;
        $nSum = 0;
        if($m !== $n) {
            for ($a=1; $a <= $m; $a++) { 
                $mSum += $a;
            }
            for ($b=1; $b <= $n; $b++) { 
                $nSum += $b;
            }
            return $mSum * $nSum;
        } else {
            for ($b=1; $b <= $n; $b++) { 
                $nSum += $b;
            }
            return $nSum * $nSum;
        }
    }

    function getRectanglesInRows($rows, $combination) {
        //THIS FUNCTION GOES HORIZANTALLY ie checks combinations from left to right.
        //we pass an array of rows array and combination
        //eg row -> [[1,2,3,4], [5,6,7,8], [9,10,11,12]] and combination -> [1,2] 
        //the function calculates how many combinations of 1 by 2s are in the grid
        $counter = 0;
        $rowsCount = count($rows);
        // dd($rowsCount);
        for ($i=0; $i < count($rows[0]); $i++) { 
            //check if current index + rectangle width/grids exists in the row
            //if not, it is out of bound and we don't increment the counter
            if(array_key_exists($i + $combination[1]-1, $rows[0])) {
                $counter++;
            }
        }
        if($combination[0] > 1) {
            //counterLocal holds how deep the combinations can go in the grid
            //eg if combination[0] is 3 and the row is 3 grids deep, the combination can only go one depth of the grid
            //if it was 2, it can go twice in the depth. And if it was 1 it would be three times.
            $counterLocal = 0; 
            for ($i=0; $i < $rowsCount; $i++) { 
                if(array_key_exists($i + $combination[0]-1, $rows)) {
                    $counterLocal++;
                }
            }
            return $counter * $counterLocal;
        }
        return $counter * $rowsCount;
    }

    function getRectanglesInCols($cols, $combination) {
        //this function works the same as getRectanglesInRows except it goes deep the grid ie checks combinations from top to bottom.
        if($combination[0] !== $combination[1]) {
            $counter = 0;
            $colsCount = count($cols);
            $colsCountRow = count($cols[0]);
            // dd($colsCount);
            for ($i=0; $i < count($cols); $i++) { 
                if(array_key_exists($i + $combination[0]-1, $cols)) {
                    $counter++;
                }
            }
            if($combination[0] > 1) {
                $counterLocal = 0; 
                for ($i=0; $i < $colsCountRow; $i++) { 
                    if(array_key_exists($i + $combination[1]-1, $cols[0])) {
                            $counterLocal++;
                    }
                }
                return $counter * $counterLocal;
            }
            return $counter * $colsCount;
        }
    }

    function getEachNumber($number) {
        //pass a number and it returns each number till zero
        //eg if you pass 3, it returns 3, 2, 1, then sort it in asc
        $arr = [];
        while ($number > 0) {
            $arr[] = $number;
            $number--;
        }
        sort($arr);
        return $arr;
    }

    function getCombinations($arrayM, $arrayN) {
        $bothArraysCombined = [];
        for ($i=0; $i < count($arrayM); $i++) { 
            for ($j=0; $j < count($arrayN); $j++) { 
                //if bothCombinedArray is empty
                if(count($bothArraysCombined) === 0) {
                    $bothArraysCombined[] = array($arrayM[$i], $arrayN[$j]);
                } else {
                    //if both arrayM and arrayN are equal
                    if(count($arrayM) === count($arrayN)) {
                        //check if the next combination already exists
                        if(!$this->contains_array($bothArraysCombined, array($arrayM[$i], $arrayN[$j])) ) {
                            //add the combination
                            $bothArraysCombined[] = array($arrayM[$i], $arrayN[$j]);
                        }
                    } else {
                        //if they are not equal, check which is greater and add an array of equal values of its last item
                        //eg if arrayM is size 2 and arrayN is size 3, the last item of the combined array should be [3,3]
                        // This is accounted for when arrayM and arrayN are equal
                        if(count($arrayM) > count($arrayN)) {
                            if(!$this->contains_array($bothArraysCombined, array($arrayM[$i], $arrayN[$j])) ) {
                                //add the combination
                                $bothArraysCombined[] = array($arrayM[$i], $arrayN[$j]);
                            }

                            if($i === (count($arrayM) - 1)) {
                                //do this only we get to the last index
                                $bothArraysCombined[] = array($arrayM[count($arrayM) -1], $arrayM[count($arrayM) -1]);
                            }
                        } 
                        if(count($arrayN) > count($arrayM)) {
                            if(!$this->contains_array($bothArraysCombined, array($arrayM[$i], $arrayN[$j])) ) {
                                //add the combination
                                $bothArraysCombined[] = array($arrayM[$i], $arrayN[$j]);
                            }

                            if($i === (count($arrayM) - 1)) {
                                //do this only we get to the last index
                                $bothArraysCombined[] = array($arrayN[count($arrayN) -1], $arrayN[count($arrayN) -1]);
                            }
                        }
                    }

                }
            }
        }
        return $bothArraysCombined;
        // return [[1,1], [1,2], [1,3], [2,1], [2,2]];
    }

    function removeDuplicatesFromCombination($array) {
        $arr = [];
        for ($i=0; $i < count($array); $i++) { 
            if($i === 0) {
                $arr[] = $array[$i];
            } else {
                // skip the first index because there is nothing before it to check if it exists
                //get a slice of the array up to the current index
                $slicedArray = array_slice($array,0,$i);
                //check if the arrays swapped version exists using $this->contains_array()
                if(!$this->contains_array($slicedArray, $this->getSwappedArrayValues($array[$i]) )) {
                    $arr[] = $array[$i];
                }
                //if it does, remove/skip it, else keep checking

            }
        }
        return $arr;
    }

    function getSwappedArrayValues($array) {
        //pass an array of size two and it returns array with their positions swapped
        //eg pass array 2,1 and it returns 1,2
        if(count($array) === 2) {
            $newArr = array($array[1], $array[0]);
            return $newArr;
        }
    }

    function contains_array($hayStack, $needle){
        for ($i=0; $i < count($hayStack); $i++) { 
            if($hayStack[$i] === $needle) {
                return true;
            }
        }
        return false;
    }

    function createMatrix($m, $n) {
        $array = [];
        $indx = 1;
        for ($a=0; $a < $m; $a++) { 
            $sub = [];
            for ($b=0; $b < $n; $b++) { 
                $sub[] = $indx;
                $indx++;
            }
            $array[] = $sub;
        }

        return $array;
    }
}
