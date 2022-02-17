<?php

use Symfony\Component\HttpFoundation\Response;

class UserListsCest
{

    private $id;
    private $lotId;

    public function _before(ApiTester $I)
    {
        $this->id = $I->createUser(
            'username',
            'user@gmail.com',
            'password',
            \Carbon\Carbon::now()
        );
        $this->token = $I->getToken('user@gmail.com', 'password');
        $this->lotId = $I->createLotWonBy($this->id);
    }

    public function _after(ApiTester $I)
    {
        $I->deleteUser('user@gmail.com');
        $I->deleteLot($this->lotId);
    }

    public function getAllUserTest(ApiTester $I)
    {
        $I->sendGet('users');
        $I->seeResponseCodeIs(Response::HTTP_OK);
        $I->seeResponseMatchesJsonType([
            'id' => 'integer',
            'name' => 'string',
            'email' => 'string',
            'image_path' => 'string | null',
            'role_id' => 'integer',
            'email_verified_at' => 'string | null',
            'created_at' => 'string:date'
        ]);
    }

    public function getWinnersTest(ApiTester $I)
    {
        $I->sendGet('winners');
        $I->seeResponseCodeIs(Response::HTTP_OK);

        $I->seeResponseMatchesJsonType([
            'id' => 'integer',
            'name' => 'string',
            'lot' => 'string',
            'lid' => 'integer',
            'lotImage' => 'string | null',
            'userImage' => 'string | null',
        ]);
    }

}
