<?php

namespace App\Http\Controllers;

use App\Services\LotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LotController extends Controller
{
    public function getActive(): JsonResponse
    {
        $lots = LotService::getActive();
        return response()->json($lots, Response::HTTP_OK);
    }

    public function getById(int $id): JsonResponse
    {
        $lot = LotService::getById($id);
        return response()->json($lot, Response::HTTP_OK);
    }

    public function joinLot(Request $request, int $id): JsonResponse
    {
        $response = LotService::joinLot($request, $id);
        if (array_key_exists('added', $response)) {
            return response()->json(['data' => 'User joined'], Response::HTTP_OK);
        }
        return response()->json($response, Response::HTTP_UNAUTHORIZED);
    }

    public function createdById(int $id): JsonResponse
    {
        $lots = LotService::createdBy($id);
        return response()->json($lots, Response::HTTP_OK);
    }

    public function wonById(int $id): JsonResponse
    {
        $lots = LotService::wonBy($id);
        return response()->json($lots, Response::HTTP_OK);
    }

    public function create(Request $request): JsonResponse
    {
        $response = LotService::create($request);
        if (array_key_exists('created', $response)) {
            return response()->json(['data' => 'Lot was created'], Response::HTTP_CREATED);
        }
        return response()->json($response, Response::HTTP_BAD_REQUEST);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $response = LotService::update($request, $id);
        if (array_key_exists('updated', $response)) {
            return response()->json(['data' => 'Lot was updated'], Response::HTTP_OK);
        }
        return response()->json($response, Response::HTTP_BAD_REQUEST);
    }

}
