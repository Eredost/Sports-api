<?php

namespace App\Tests\Controller\Auth;

use App\DataFixtures\Providers\UserProvider;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GetTokenControllerTest extends WebTestCase
{
    private ?KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    private function loginRequest(string $body): void
    {
        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $body
        );
    }

    public function testSuccessfulLogIn(): void
    {
        $userCredentials = UserProvider::getUsers()[0];
        $this->loginRequest(json_encode([
                'username' => $userCredentials['username'],
                'password' => $userCredentials['plainPassword'],
            ])
        );
        $data = json_decode($this->client->getResponse()->getContent(), true);

        self::assertArrayHasKey('token', $data);
        self::assertTrue($this->client->getResponse()->isOk());
    }

    public function testLogInWithMissingUsername(): void
    {
        $this->loginRequest(json_encode([
                'password' => 'Pas3word#',
            ])
        );

        self::assertTrue($this->client->getResponse()->isClientError());
    }

    public function testLogInWithMissingPassword(): void
    {
        $this->loginRequest(json_encode([
                'username' => 'PolarBear',
            ])
        );

        self::assertTrue($this->client->getResponse()->isClientError());
    }

    public function testLogInWithInvalidCredentials(): void
    {
        $userCredentials = UserProvider::getUsers()[0];
        $this->loginRequest(json_encode([
                'username' => $userCredentials['username'],
                'password' => 'invalidPassword',
            ])
        );

        self::assertTrue($this->client->getResponse()->isClientError());
    }

    public function testLogInWithMalformedJSONBody(): void
    {
        $this->loginRequest('["username" => "Servietsky", "plainPassword" => "Ylq?12$PeviTYdm8"]');

        self::assertTrue($this->client->getResponse()->isClientError());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->client = null;
    }
}
