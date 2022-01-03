<?php

namespace App\Http\Controllers;

use App\Services\AdminService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{

    public function getLots()
    {
        $lots = AdminService::getLots();
        return response()->json($lots, Response::HTTP_OK);
    }

    public function updateLot(Request $request, $id)
    {
        $response = AdminService::updateLot($request, $id);
        return response()->json($response, Response::HTTP_OK);
    }

    public function deleteLot($id)
    {
        $response = AdminService::deleteLot($id);
        return response()->json($response, Response::HTTP_OK);
    }

    public function getUsers()
    {
        $Users = AdminService::getUsers();
        return response()->json($Users, Response::HTTP_OK);
    }

    public function updateUser(Request $request, $id)
    {
        $response = AdminService::updateUser($request, $id);
        return response()->json($response, Response::HTTP_OK);
    }

    public function deleteUser($id)
    {
        $response = AdminService::deleteUser($id);
        return response()->json($response, Response::HTTP_OK);
    }

}
