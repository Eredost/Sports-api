<?php

namespace App\Tests\Controller\Sport;

use App\Entity\Sport;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SportShowControllerTest extends AbstractSportControllerTest
{
    protected function getRoute(): array
    {
        $sport = $this->entityManager
            ->getRepository(Sport::class)
            ->findOneBy([])
        ;

        return [
            'method' => Request::METHOD_GET,
            'path'   => '/api/sports/' . $sport->getId(),
        ];
    }

    public function testSportShow(): void
    {
        $route = $this->getRoute();
        $this->client->request(
            $route['method'],
            $route['path'],
        );

        self::assertTrue($this->client->getResponse()->isOk());
    }

    public function testSportShowWithNonExistentSport(): void
    {
        $route = $this->getRoute();
        $this->client->request(
            $route['method'],
            '/api/sports/0',
        );

        self::assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider notAllowedMethodsProvider
     */
    public function testCreationWithNotAllowedHTTPMethod($method): void
    {
        $route = $this->getRoute();
        $this->client->request(
            $method,
            $route['path'],
        );

        self::assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode());
    }

    public function notAllowedMethodsProvider(): array
    {
        return [
            [Request::METHOD_POST],
        ];
    }
}
