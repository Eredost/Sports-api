<?php

namespace App\Tests\Controller\Sport;

use App\DataFixtures\Providers\UserProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractSportControllerTest extends WebTestCase
{
    protected ?KernelBrowser $client;

    protected ?EntityManagerInterface $entityManager;

    /**
     * Must return an array with route "path" and "method" as key
     *
     * return [
     *     'method' => 'POST',
     *     'path'   => '/api/sports'
     * ];
     */
    abstract protected function getRoute(): array;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $userCredentials = UserProvider::getUsers()[0];
        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => $userCredentials['username'],
                'password' => $userCredentials['plainPassword'],
            ])
        );

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));
    }

    public function testAccessWithoutJWT(): void
    {
        $route = $this->getRoute();

        $this->client->setServerParameter('HTTP_Authorization', '');
        $this->client->request(
            $route['method'],
            $route['path']
        );

        self::assertEquals(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    public function testAccessWithInvalidJWT(): void
    {
        $route = $this->getRoute();

        $this->client->setServerParameter('HTTP_Authorization', 'Bearer invalid_jwt');
        $this->client->request(
            $route['method'],
            $route['path']
        );

        self::assertEquals(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->client = null;
        $this->entityManager->close();
    }
}
