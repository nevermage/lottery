<?php

namespace App\Http\Controllers;

use App\Services\LotService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LotController extends Controller
{
    public function getActive()
    {
        $lots = LotService::getActive();
        return response()->json($lots, Response::HTTP_OK);
}

    public function getById($id)
    {
        $lot = LotService::getById($id);
        return response()->json($lot, Response::HTTP_OK);
    }

    public function joinLot(Request $request, $id)
    {
        $response = LotService::joinLot($request, $id);
        if ($response == null) {
            return response()->json(['data' => 'User was added'], Response::HTTP_OK);
        }
        return response()->json($response, Response::HTTP_UNAUTHORIZED);
    }

    public function createdById($id)
    {
        $lots = LotService::createdBy($id);
        return response()->json($lots, Response::HTTP_OK);
    }

    public function wonById($id)
    {
        $lots = LotService::wonBy($id);
        return response()->json($lots, Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $response = LotService::create($request);
        if ($response == null) {
            return response()->json(
                ['data' => 'UnAuthenticated'],
                Response::HTTP_UNAUTHORIZED
            );
        }
        if (is_object($response)) {
            return response()->json(
                [$response],
                Response::HTTP_BAD_REQUEST
            );
        }
        return response()->json(
            ['data' => $response],
            Response::HTTP_CREATED
        );
    }

    public function update(Request $request, $id)
    {
        $response = LotService::update($request, $id);
        return $response
        ?
        response()->json(
            [$response],
            Response::HTTP_BAD_REQUEST
        )
        :
        response()->json(
            ['data' => 'Lot was updated'],
            Response::HTTP_OK
        );
    }

}
