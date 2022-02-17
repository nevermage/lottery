<?php

use Symfony\Component\HttpFoundation\Response;

class LotListsCest
{

    public function _before(ApiTester $I)
    {

    }

    public function _after(ApiTester $I)
    {

    }

    public function getLots(ApiTester $I)
    {
        $I->sendGet('lots');
        $I->seeResponseCodeIs(Response::HTTP_OK);
        $I->seeResponseMatchesJsonType([
            'id' => 'integer',
            'name' => 'string',
            'creator' => 'string',
            'image_path' => 'string | null',
            'description' => 'string | null',
            'roll_time' => 'string | null',
        ]);
    }

    public function getUnmoderatedLotByIdTest(ApiTester $I)
    {
        $lotId = $I->createLot('lot-name', 1, 'unmoderated');
        $I->sendGet('lot/' . $lotId);
        $I->seeResponseCodeIs(Response::HTTP_METHOD_NOT_ALLOWED);
        $I->seeResponseContains('{"error":"Unable to get lot"}');

        $I->deleteLot($lotId);
    }

    public function getLotByIdTest(ApiTester $I)
    {
        $lotId = $I->createLot('lot-name', 1, 'active');
        $I->sendGet('lot/' . $lotId);
        $I->seeResponseCodeIs(Response::HTTP_OK);
        $I->seeResponseMatchesJsonType([
            'id' => 'integer',
            'name' => 'string',
            'creator_id' => 'integer',
            'creator' => 'string',
            'winner' => 'string | null',
            'image_path' => 'string | null',
            'description' => 'string | null',
            'roll_time' => 'string | null',
            'roll_timer' => 'integer | null',
            'winner_id' => 'integer | null',
            'status' => 'string'
        ]);

        $I->deleteLot($lotId);
    }

}


