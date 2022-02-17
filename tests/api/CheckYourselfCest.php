<?php

use Symfony\Component\HttpFoundation\Response;

class CheckYourselfCest
{
    public function _before(ApiTester $I)
    {
        $I->createUser('username', 'user@gmail.com', 'password');
    }

    public function _after(ApiTester $I)
    {
        $I->deleteUser('user@gmail.com');
    }

    public function checkUnauthenticatedUserTest(ApiTester $I)
    {
        $I->sendGet('check-user');
        $I->seeResponseCodeIs(Response::HTTP_UNAUTHORIZED);
        $I->seeResponseContains('{"data":"UnAuthenticated"}');
    }

    public function checkUserTest(ApiTester $I)
    {
        $token = $I->getToken('user@gmail.com', 'password');
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGet('check-user');
        $I->seeResponseCodeIs(Response::HTTP_OK);
        $I->seeResponseMatchesJsonType([
            'id' => 'integer',
            'name' => 'string',
            'email' => 'string:email',
            'image_path' => 'string|null',
            'role_id' => 'integer',
            'created_at' => 'string:date',
            'updated_at' => 'string:date',
            'email_verified_at' => 'string:date|null',
        ]);
    }

    public function myLotsUnauthenticatedTest(ApiTester $I)
    {
        $I->sendGet('my-lots');
        $I->seeResponseContains('{"error":"UnAuthenticated"}');
    }

    public function myLotsTest(ApiTester $I)
    {
        $userId = $I->createUser(
            'username',
            'lotOwner@gmail.com',
            'password',
            \Carbon\Carbon::now()
        );
        $lotId = $I->createLot('lot-name', $userId, 'accepted');
        $token = $I->getToken('lotOwner@gmail.com', 'password');
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGet('my-lots');
        $I->seeResponseCodeIs(Response::HTTP_OK);

        $I->deleteUser('lotOwner@gmail.com');
        $I->deleteLot($lotId);
    }

}
