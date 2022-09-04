<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait JsonResponseTrait {

    /**
     * This function is to standardize Json Response.
     *
     * @param $data
     * @param $responseCode
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function sendJsonResponse($data, $responseCode)
    {

        return response([
            'data' => $data,
            'user' => !empty(Auth::id()) ? User::where('id', Auth::id())->first() : null,
            'isAdmin' => !empty(Auth::id()) ? User::find(Auth::id())->isAdmin() : false,
            'response_code' => $responseCode
        ]);

    }
}
