<?php


namespace App\Tests\Security;


use App\Security\TokenGenerator;
use PHPUnit\Framework\TestCase;

class TokenGeneratorTest extends TestCase
{
    public function testTokenGeneration()
    {
        $tokenGen = new TokenGenerator();
        $token = $tokenGen->getRandomSecureToken(30);
//        $token[15] = '*';

        $this->assertEquals(30, strlen($token));

        // not ideal, it does not check all characters in $token
//        $this->assertEquals(1 ,preg_match("/[A-Za-z0-9]/", $token));

        // return true if there are only alphanumeric characters in $token
        self::assertTrue(ctype_alnum($token), 'Token contains incorrect characters.');
    }

}