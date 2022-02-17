<?php

use Symfony\Component\HttpFoundation\Response;

class UserInformationCest
{
    private $id;
    private $token;

    public function _before(ApiTester $I)
    {
        $this->id = $I->createUser(
            'username',
            'user@gmail.com',
            'password',
            \Carbon\Carbon::now()
        );
        $this->token = $I->getToken('user@gmail.com', 'password');
    }

    public function _after(ApiTester $I)
    {
        $I->deleteUser('user@gmail.com');
    }

    public function getUserInfoTest(ApiTester $I)
    {
        $I->sendGet('user/' . $this->id);
        $I->seeResponseCodeIs(Response::HTTP_OK);
        $I->seeResponseMatchesJsonType([
            'id' => 'integer',
            'name' => 'string',
            'email' => 'string',
            'image_path' => 'string | null',
            'role_id' => 'integer',
            'email_verified_at' => 'string | null',
            'created_at' => 'string | null'
        ]);
    }

    public function getUserLots(ApiTester $I)
    {
        $lotId = $I->createLot('lot-name', $this->id, 'accepted');
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token);
        $I->sendGet('my-lots');
        $I->seeResponseCodeIs(Response::HTTP_OK);

        $I->deleteLot($lotId);
    }

    public function emptyLotsWonByTest(ApiTester $I)
    {
        $I->sendGet('lots-won-by/' . $this->id);
        $I->seeResponseCodeIs(Response::HTTP_OK);
        $I->seeResponseContains('');
    }

    public function lotsWonByTest(ApiTester $I)
    {
        $lotId = $I->createLotWonBy($this->id);
        $I->sendGet('lots-won-by/' . $this->id);
        $I->seeResponseCodeIs(Response::HTTP_OK);
        $I->deleteLot($lotId);
    }

    public function lotsCreatedByTest(ApiTester $I)
    {
        $lotId = $I->createLot('lot-name', $this->id, 'accepted');
        $I->sendGet('lots-created-by/' . $this->id);
        $I->seeResponseCodeIs(Response::HTTP_OK);
        $I->deleteLot($lotId);
    }

}
