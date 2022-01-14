<?php

namespace App\Http\Controllers;

use App\Services\AdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{

    public function getLots(): JsonResponse
    {
        $lots = AdminService::getLots();
        return response()->json($lots, Response::HTTP_OK);
    }

    public function updateLot(Request $request, $id): JsonResponse
    {
        $response = AdminService::updateLot($request, $id);
        return response()->json($response, Response::HTTP_OK);
    }

    public function deleteLot(int $id): JsonResponse
    {
        $response = AdminService::deleteLot($id);
        return response()->json($response, Response::HTTP_OK);
    }

    public function getUsers(): JsonResponse
    {
        $Users = AdminService::getUsers();
        return response()->json($Users, Response::HTTP_OK);
    }

    public function updateUser(Request $request, int $id): JsonResponse
    {
        $response = AdminService::updateUser($request, $id);
        return response()->json($response, Response::HTTP_OK);
    }

    public function deleteUser(int $id): JsonResponse
    {
        $response = AdminService::deleteUser($id);
        return response()->json($response, Response::HTTP_OK);
    }

}
