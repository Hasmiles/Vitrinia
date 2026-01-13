<?php

namespace App\Http\Controllers;

use App\Http\Resources\OptionResource;
use App\Models\Option;
use App\Models\Status;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    public function options(){
        $options = Option::where('active', 1)->with('sub_option')->get();
        return response()->json([
            'success' => true,
            'data' => OptionResource::collection($options)
        ]);
    }

    public function statuses(){
        $statuses = Status::where('active',1)->get();
        return response()->json([
            'success' => true,
            'data' => $statuses
        ]);
    }
}
