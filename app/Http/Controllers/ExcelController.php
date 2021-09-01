<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Excel;
use App\users;
use App\sos;
use App\location;

class ExcelController extends Controller
{
    function export(){

        $users = users::all();
        $locations = location::all();
        $sos = sos::all();

        return Excel::create('runners_excel', function($excel) use ($users,$locations,$sos) {
            $excel->sheet('משתמשים', function($sheet) use ($users)
            {
                $sheet->fromArray($users);
            });
            $excel->sheet('מיקומים', function($sheet) use ($locations)
            {
                $sheet->fromArray($locations);
            });
            $excel->sheet('SOS', function($sheet) use ($sos)
            {
                $sheet->fromArray($sos);
            });
        })->download('xls');
    }
}
