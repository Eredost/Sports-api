<?php

namespace App\Tests\Controller\Sport;

use App\Entity\Sport;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SportDeleteControllerTest extends AbstractSportControllerTest
{
    protected function getRoute(): array
    {
        $sport = $this->entityManager
            ->getRepository(Sport::class)
            ->findOneBy([])
        ;

        return [
            'method' => Request::METHOD_DELETE,
            'path'   => '/api/sports/' . $sport->getId(),
        ];
    }

    public function testSportDeletion(): void
    {
        $route = $this->getRoute();
        $this->client->request(
            $route['method'],
            $route['path'],
        );

        self::assertEquals(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());
    }

    public function testSportDeletionWithNonExistentSport(): void
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
