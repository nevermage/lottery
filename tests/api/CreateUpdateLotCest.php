<?php

use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

class CreateUpdateLotCest
{
    private $email = 'user@gmail.com';
    private $password = 'password';
    private $userId;
    private $token;

    public function _before(ApiTester $I)
    {
        $this->userId = $I->createUser(
            $this->email,
            'user@gmail.com',
            $this->password,
            Carbon::now()
        );
        $this->token = $I->getToken($this->email, $this->password);
    }

    public function _after(ApiTester $I)
    {
        $I->deleteUser('user@gmail.com');
    }

    public function unauthorisedCreateLotTest(ApiTester $I)
    {
        $I->sendPost('create', [
            'name' => 'lot name',
        ]);
        $I->seeResponseCodeIs(Response::HTTP_BAD_REQUEST);
        $I->seeResponseContains('{"data":"UnAuthenticated"}');
    }

    public function createLotTest(ApiTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token);
        $I->sendPost('create', ['name' => 'lot name',]);
        $I->seeResponseCodeIs(Response::HTTP_CREATED);
        $I->seeResponseContains('{"data":"Lot was created"}');

        $I->deleteLotsByCreatorEmail('user@gmail.com');
    }

    public function unauthorisedUpdateLotTest(ApiTester $I)
    {
        $lotId = $I->createLot('lot-name', 0, 'accepted');

        $I->sendPost('update/' . $lotId);
        $I->seeResponseCodeIs(Response::HTTP_BAD_REQUEST);
        $I->seeResponseContains('{"data":"UnAuthenticated"}');

        $I->deleteLot($lotId);
    }

    public function notOwnLotUpdateTest(ApiTester $I)
    {
        $I->createUser(
            'username',
            'notOwner@gmail.com',
            'password',
            Carbon::now()
        );
        $lotId = $I->createLot('lot-name', 0, 'accepted');
        $token = $I->getToken('notOwner@gmail.com', 'password');

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendPost('update/' . $lotId, ['name' => 'lot-name2']);
        $I->seeResponseCodeIs(Response::HTTP_BAD_REQUEST);
        $I->seeResponseContains('"data":"user is not owner of lot"');

        $I->deleteUser('notOwner@gmail.com');
        $I->deleteLot($lotId);
    }

    public function updateLotTest(ApiTester $I)
    {
        $userId = $I->createUser(
            'username',
            'lotOwner@gmail.com',
            'password',
            Carbon::now()
        );
        $lotId = $I->createLot('lot-name', $userId, 'accepted');
        $token = $I->getToken('lotOwner@gmail.com', 'password');

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendPost('update/' . $lotId, ['name' => 'lot-name2']);
        $I->seeResponseCodeIs(Response::HTTP_OK);
        $I->seeResponseContains('"data":"Lot was updated"');

        $I->deleteUser('lotOwner@gmail.com');
        $I->deleteLot($lotId);
    }

    public function wrongStatusLotUpdateTest(ApiTester $I)
    {
        $userId = $I->createUser(
            'username',
            'lotOwner@gmail.com',
            'password',
            \Carbon\Carbon::now()
        );
        $lotId = $I->createLot('lot-name', $userId, 'active');
        $token = $I->getToken('lotOwner@gmail.com', 'password');

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendPost('update/' . $lotId, ['name' => 'lot-name2']);
        $I->seeResponseCodeIs(Response::HTTP_BAD_REQUEST);
        $I->seeResponseContains('"data":"unable to update lot"');

        $I->deleteUser('lotOwner@gmail.com');
        $I->deleteLot($lotId);
    }

    public function launchLotTest(ApiTester $I)
    {
        $userId = $I->createUser(
            'username',
            'lotOwner@gmail.com',
            'password',
            Carbon::now()
        );
        $lotId = $I->createLot('lot-name', $userId, 'accepted');
        $token = $I->getToken('lotOwner@gmail.com', 'password');

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendPost('set-roll-time/' . $lotId, ['time' => Carbon::now()->addDays(2)]);
        $I->seeResponseCodeIs(Response::HTTP_OK);
        $I->seeResponseContains('"data":"Time was set"');

        $I->deleteUser('lotOwner@gmail.com');
        $I->deleteLot($lotId);
    }

    public function notOwnerLaunchLotTest(ApiTester $I)
    {
        $userId = $I->createUser(
            'username',
            'lotOwner@gmail.com',
            'password',
            Carbon::now()
        );
        $lotId = $I->createLot('lot-name', 0, 'accepted');
        $token = $I->getToken('lotOwner@gmail.com', 'password');

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendPost('set-roll-time/' . $lotId, ['time' => Carbon::now()->addDays(2)]);
        $I->seeResponseCodeIs(Response::HTTP_BAD_REQUEST);
        $I->seeResponseContains('{"error":"User in not owner of lot"}');

        $I->deleteUser('lotOwner@gmail.com');
        $I->deleteLot($lotId);
    }

    public function wrongLotStatusLaunchTest(ApiTester $I)
    {
        $userId = $I->createUser(
            'username',
            'lotOwner@gmail.com',
            'password',
            Carbon::now()
        );
        $lotId = $I->createLot('lot-name', $userId, 'unmoderated');
        $token = $I->getToken('lotOwner@gmail.com', 'password');

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendPost('set-roll-time/' . $lotId, ['time' => Carbon::now()->addDays(2)]);
        $I->seeResponseCodeIs(Response::HTTP_BAD_REQUEST);
        $I->seeResponseContains('{"error":"Unable to launch lot"}');

        $I->deleteUser('lotOwner@gmail.com');
        $I->deleteLot($lotId);
    }

    public function joinLotWithWrongStatusTest(ApiTester $I)
    {
        $lotId = $I->createLot('lot-name', 0, 'unmoderated');

        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token);
        $I->sendPost('join/' . $lotId);
        $I->seeResponseCodeIs(Response::HTTP_BAD_REQUEST);
        $I->seeResponseContains('{"data":"Lot is not active"}');

        $I->deleteLot($lotId);
    }

    public function joinLotAsCreatorTest(ApiTester $I)
    {
        $lotId = $I->createLot('lot-name', $this->userId, 'active');

        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token);
        $I->sendPost('join/' . $lotId);
        $I->seeResponseCodeIs(Response::HTTP_BAD_REQUEST);
        $I->seeResponseContains('{"data":"Creator can not join to own lot"}');

        $I->deleteLot($lotId);
    }

    public function joinLotTest(ApiTester $I)
    {
        $lotId = $I->createLot('lot-name', 0, 'active');

        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token);
        $I->sendPost('join/' . $lotId);
        $I->seeResponseCodeIs(Response::HTTP_OK);
        $I->seeResponseContains('{"data":"User joined"}');

        $I->deleteLot($lotId);
    }

}
