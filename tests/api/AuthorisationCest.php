<?php

use Symfony\Component\HttpFoundation\Response;

class AuthorisationCest
{
    public function _before(ApiTester $I)
    {
    }

    // tests
    public function testLogin(ApiTester $I)
    {
        $I->sendGet('check-user');
        $I->seeResponseCodeIs(Response::HTTP_UNAUTHORIZED);
        $I->seeResponseIsJson();
        $I->seeResponseContains('{"data":"UnAuthenticated"}');
    }

    public function testCheckUser(ApiTester $I)
    {
        $username = 'username';
        $email = 'user@gmail.com';
        $password = 'password';

        $I->createUser($username, $email, $password);

        $I->sendPost('login', [
            'email' => $email,
            'password' => $password
        ]);
        $I->seeResponseCodeIs(Response::HTTP_OK);
        $I->seeResponseMatchesJsonType(['token' => 'string']);
        $bearerToken = json_decode($I->grabResponse(), true)['token'];

        $I->haveHttpHeader('Authorization', 'Bearer ' . $bearerToken);
        $I->sendGet('check-user');
        $I->seeResponseCodeIs(200);
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
}
