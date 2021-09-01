<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\location;
use App\sos;
use App\users;

class LocationController extends Controller
{

    public function update(Request $request){

        $long = $request->input('long');
        $lat = $request->input('lat');
        $guid = $request->input('GUID');

        $location = new location;

        $location->longitude = $long;
        $location->latitude = $lat;
        $location->GUID= $guid;
        $location->save();

    }

    public function sos(Request $request){

        $long = $request->input('long');
        $lat = $request->input('lat');
        $guid = $request->input('GUID');

        $location = new sos;

        $location->longitude = $long;
        $location->latitude = $lat;
        $location->GUID= $guid;
        $location->viewed = 0;
        $location->save();

    }
}
