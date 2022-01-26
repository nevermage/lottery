<?php

class AuthorisationCest
{
    public function _before(ApiTester $I)
    {
    }

    // tests
    public function testUnAuthUser(ApiTester $I)
    {
        $I->sendGet('/check-user');
        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();
        $I->seeResponseContains('{"data":"UnAuthenticated"}');
    }

    public function testAuthUser(ApiTester $I)
    {
        $bearerToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6IkJpbGwuSGFtQGdtYWlsLmNvbSIsInBhc3N3b3JkIjoiJDJ5JDEwJFBrOFI3TlhraTBcLzE2endJdlpZWjFlRThXYmhxQ1A2U0Q2ZU9TZlNKa2lJUTdNU1VjR0pscSJ9.cjrctCv8Wbll_1Fz6n2kx1SzsBYTKG9vFqubg-jBsuM';
        $I->haveHttpHeader('Authorization', 'Bearer ' . $bearerToken);
        $I->sendGet('/check-user');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'id' => 'integer',
            'name' => 'string',
            'email' => 'string:email',
            'image_path' => 'string|null',
            'role_id' => 'integer',
            'created_at' => 'string:date',
            'updated_at' => 'string:date',
            'email_verified_at' => 'string:date',
        ]);

    }
}
