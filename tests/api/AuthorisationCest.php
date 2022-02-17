<?php

use Symfony\Component\HttpFoundation\Response;

class AuthorisationCest
{
    public function _before(ApiTester $I)
    {
        $I->createUser('username', 'user@gmail.com', 'password');
    }

    public function _after(ApiTester $I)
    {
        $I->deleteUser('user@gmail.com');
    }

    public function loginTest(ApiTester $I)
    {
        $email = 'user@gmail.com';
        $password = 'password';

        $I->sendPost('login', [
            'email' => $email,
            'password' => $password
        ]);
        $I->seeResponseCodeIs(Response::HTTP_OK);
        $I->seeResponseMatchesJsonType(['token' => 'string']);
    }

    public function registerWithExitedEmailTest(ApiTester $I)
    {
        $I->sendPost('register', [
            'name' => 'name',
            'email' => 'user@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);
        $I->seeResponseCodeIs(Response::HTTP_BAD_REQUEST);
        $I->seeResponseContains('{"data":"This email is already taken"}');
    }

    public function registerTest(ApiTester $I)
    {
        $I->sendPost('register', [
            'name' => 'name',
            'email' => 'registerTest@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);
        $I->seeResponseCodeIs(Response::HTTP_OK);
        $I->seeResponseContains('{"data":"Confirm your email!"}');
        $I->deleteUser('registerTest@gmail.com');
    }

    public function verifyEmailTest(ApiTester $I)
    {
        $token = $I->getApiToken('user@gmail.com');
        $I->sendGet('verify', [
            'hash' => $token
        ]);
        $I->seeResponseCodeIs(Response::HTTP_OK);
        $I->seeResponseContains('{"data":"Email confirmed"}');
    }

    public function wrongTokenVerifyEmailTest(ApiTester $I)
    {
        $I->sendGet('verify', [
            'hash' => 'wrong token'
        ]);
        $I->seeResponseCodeIs(Response::HTTP_BAD_REQUEST);
        $I->seeResponseContains('{"data":"No users with this token"}');
    }

    public function requestPasswordResetForWrongEmailTest(ApiTester $I)
    {
        $I->sendGet('password-reset-mail', ['email' => 'not-exist@email.com']);
        $I->seeResponseCodeIs(Response::HTTP_BAD_REQUEST);
        $I->seeResponseContains('{"error":"No users with this email"}');
    }

    public function requestPasswordResetEmailTest(ApiTester $I)
    {
        $I->createUser(
            'passwordReset',
            'passwordReset@gmail.com',
            'password',
            Carbon\Carbon::now()
        );
        $I->sendGet('password-reset-mail', ['email' => 'passwordReset@gmail.com']);
        $I->seeResponseCodeIs(Response::HTTP_OK);
        $I->seeResponseContains('{"data":"Check your email"}');

        $I->deleteUser('passwordReset@gmail.com');
    }

    public function passwordResetTest(ApiTester $I)
    {
        $I->createUser(
            'passwordReset',
            'passwordReset@gmail.com',
            'password',
            Carbon\Carbon::now()
        );
        $I->sendPost('password-reset', [
            'token' => $I->getApiToken('passwordReset@gmail.com'),
            'password' => 'new-password',
        ]);
        $I->seeResponseCodeIs(Response::HTTP_OK);
        $I->seeResponseContains('{"data":"Password updated!"}');

        $I->deleteUser('passwordReset@gmail.com');
    }

    public function samePasswordResetTest(ApiTester $I)
    {
        $I->createUser(
            'passwordReset',
            'passwordReset@gmail.com',
            'password',
            Carbon\Carbon::now()
        );

        $I->sendPost('password-reset', [
            'token' => $I->getApiToken('passwordReset@gmail.com'),
            'password' => 'password',
        ]);
        $I->seeResponseCodeIs(Response::HTTP_BAD_REQUEST);
        $I->seeResponseContains('{"error":"Your password is the same as old one"}');

        $I->deleteUser('passwordReset@gmail.com');
    }

}
