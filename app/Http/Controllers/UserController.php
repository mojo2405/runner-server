<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\users;
use App\sos;
use Psy\Exception\ErrorException;
use Uuid;
use App\Http\Controllers\DB;
class UserController extends Controller
{
    public function test(Request $request)
    {
        $phone = $request->input('phone');
        $id = $request->input('id');


        echo 123;
    }

    public function registerUser(Request $request)
    {

        $phone = $request->input('phone');
        $id = $request->input('id');
        $runner_number = $request->input('runner_number');

        $user = users::where("id", $id)->first();
        if (isset($user)){
            return response()->json([
                'GUID' => $user->GUID
            ]);
        }
        $user = new users;

        $user->phone = $phone;
        $user->ID = $id;
        $user->GUID = Uuid::generate(4)->string;
        $user->runner_number = $runner_number;
        $user->save();

        return response()->json([
            'GUID' => $user->GUID
        ]);
    }

    public function getAllUsers(){
        $users = users::all();
        return $users;
    }

    public function getAllSos(){
        $users = array();
        $sos = sos::all();
        $counter = 1;
        foreach ($sos as $sos_call){
                $user = users::where("GUID", $sos_call->GUID)->first();
                if (isset($user->phone)){
//                    $users[] = [
//                        "created_at" => $sos_call->created_at->timestamp,
//                        "id" => $user->ID,
//                        "phone" => $user->phone,
//                        "latitude" => $sos_call->latitude,
//                        "longitude" => $sos_call->longitude
//                    ];
                    $users[] = [
                        $counter++,
                        $sos_call->created_at->toDateTimeString(),
                        $user->runner_number,
                        $user->ID,
                        $user->phone,
                        $sos_call->latitude,
                        $sos_call->longitude
                    ];
                }


        }
        return $users;
    }

    public function getNewSos(Request $request){
        $users = array();
        $sos = sos::where("created_at",">=",$request->time)->get();
        sos::where("viewed", 0)->update(["viewed"=>1]);
        foreach ($sos as $sos_call){
            $user = users::where("GUID", $sos_call->GUID)->first();
            if (isset($user->phone)){
                    $users[] = [
                        0,
                        "created_at" => $sos_call->created_at->timestamp,
                        "runner_number" => $user->runner_number,
                        "id" => $user->ID,
                        "phone" => $user->phone,
                        "latitude" => $sos_call->latitude,
                        "longitude" => $sos_call->longitude
                    ];
//                $users[] = [
//                    $sos_call->created_at->toDateTimeString(),
//                    $user->ID,
//                    $user->phone,
//                    $sos_call->latitude,
//                    $sos_call->longitude
//                ];
            }


        }
        return response()->json([
            'results' => $users
        ]);
    }

    public function getAllUsersWithLastLocation(){
        $objects = array();

        $users = $this->getAllUsers();
        foreach ($users as $user){
            $last_location = $user->locations->last();
            if (!isset($last_location->latitude)){
                continue;
            }
            $objects[] = ["GUID" => $user->GUID,
                "phone" =>$user->phone,
                "last_location"=>[$user->ID,$last_location->latitude,$last_location->longitude],
                "id" => $user->ID,
                "html" => ['<div class="info_content" dir="rtl"><h3>פרטי משתמש</h3><p>מספר מתמודד : '.$user->runner_number.'</p><p>תעדות זהות : '.$user->ID.'</p><p>מספר טלפון : '.$user->phone.'</p>
<p>קו רוחב: '.$last_location->latitude.'</p>
<p>קו אורך: '.$last_location->longitude.'</p>
<p>עדכון אחרון :'.$last_location->created_at.'</p></div>']
            ];
        }
        return $objects;
    }

    public function searchUser(Request $request){
        $objects = array();
        $search_string = $request->search;
        $users = Users::where('ID', 'LIKE', "%$search_string%")->get();

        foreach ($users as $user){
            $objects[] = ["GUID" => $user->GUID,
                "phone" =>$user->phone,
                "id" => $user->ID,
                "runner_number" => $user->runner_number
            ];
        }

        return response()->json([
            'results' => $objects
        ]);
    }

    public function getRoute(Request $request){

        $id = $request->id;
        $user = Users::find($id);

        $object = ["GUID" => $user->GUID,
            "phone" =>$user->phone,
            "locations"=>$user->locations,
            "id" => $user->ID,
            "runner_number" => $user->runner_number
        ];


        return response()->json([
            'results' => $object
        ]);
    }

    public function getUnseenSOS (){
        $users = array();
        $sos = sos::where("viewed", 0)->get();
        sos::where("viewed", 0)->update(["viewed"=>1]);

        foreach ($sos as $sos_call){
            $user = users::where("GUID",$sos_call->GUID)->first();
            $users[] = [
                0,
                "phone" =>$user->phone,
                "id" => $user->ID,
                "latitude" =>$sos_call->latitude,
                "longitude" =>$sos_call->longitude,
                "runner_number" => $user->runner_number
            ];
        }

        return response()->json([
            'results' => $users
        ]);
    }

    public function truncateTables(){

        \DB::table('sos')->delete();
        \DB::table('locations')->delete();
        \DB::table('users')->delete();
    }
}
