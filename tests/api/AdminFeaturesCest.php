<?php

use Symfony\Component\HttpFoundation\Response;

class AdminFeaturesCest
{
    private $adminToken;

    public function _before(ApiTester $I)
    {
        $I->createAdmin('admin@gmail.com');
        $this->adminToken = $I->getToken('admin@gmail.com', 'password');
    }

    public function _after(ApiTester $I)
    {
        $I->deleteUser('admin@gmail.com');
    }

    public function makeRequestAsNotAdminTest(ApiTester $I)
    {
        $I->createUser('user', 'user@gmail.com', 'password');
        $token = $I->getToken('user@gmail.com', 'password');

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGet('admin/lots');
        $I->seeResponseCodeIs(Response::HTTP_UNAUTHORIZED);

        $I->deleteUser('user@gmail.com');
    }

    public function getLotsTest(ApiTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->adminToken);
        $I->sendGet('admin/lots');
        $I->seeResponseCodeIs(Response::HTTP_OK);
        $I->seeResponseMatchesJsonType([
            'id' => 'integer',
            'creator_id' => 'integer',
            'winner_id' => 'integer | null',
            'name' => 'string',
            'status' => 'string',
            'image_path' => 'string | null',
            'description' => 'string | null',
            'roll_time' => 'string | null',
            'created_at' => 'string:date',
            'updated_at' => 'string:date',
        ]);
    }

    public function getUsersTest(ApiTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->adminToken);
        $I->sendGet('admin/users');
        $I->seeResponseCodeIs(Response::HTTP_OK);
        $I->seeResponseMatchesJsonType([
            'id' => 'integer',
            'name' => 'string',
            'email' => 'string',
            'image_path' => 'string | null',
            'role_id' => 'integer',
            'email_verified_at' => 'string | null',
            'api_token' => 'string | null',
            'created_at' => 'string:date | null',
            'updated_at' => 'string:date | null'
        ]);
    }

    public function lotUpdateTest(ApiTester $I)
    {
        $lotId = $I->createLot('lot name', 0, 'unmoderated');
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->adminToken);
        $I->sendPut('admin/lot-update/' . $lotId, [
            'name' => 'new lot name',
            'image_path' => '/path/to/image.png'
        ]);
        $I->seeResponseCodeIs(Response::HTTP_OK);
        $I->seeResponseContains('"data":"lot was updated"');
        $I->deleteLot($lotId);
    }

    public function lotDeleteTest(ApiTester $I)
    {
        $lotId = $I->createLot('lot name', 0, 'unmoderated');
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->adminToken);
        $I->sendDelete('admin/lot-delete/' . $lotId);
        $I->seeResponseCodeIs(Response::HTTP_OK);
        $I->seeResponseContains('"data":"lot was deleted"');
    }

    public function userUpdateTest(ApiTester $I)
    {
        $lotId = $I->createUser('username', 'user@gmail.com', 'password');
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->adminToken);
        $I->sendPut('admin/user-update/' . $lotId, [
            'name' => 'new username',
            'email' => 'newuser@gmail.com'
        ]);
        $I->seeResponseCodeIs(Response::HTTP_OK);
        $I->seeResponseContains('{"data":"User was updated"}');
        $I->deleteUser('newuser@gmail.com');
    }

    public function userDeleteTest(ApiTester $I)
    {
        $userId = $I->createUser('username', 'user@gmail.com', 'password');
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->adminToken);
        $I->sendDelete('admin/user-delete/' . $userId);
        $I->seeResponseCodeIs(Response::HTTP_OK);
        $I->seeResponseContains('{"data":"User was deleted"}');
    }

}
