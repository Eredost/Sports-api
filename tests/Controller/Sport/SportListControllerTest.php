<?php

namespace App\Tests\Controller\Sport;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SportListControllerTest extends AbstractSportControllerTest
{
    protected function getRoute(): array
    {
        return [
            'method' => Request::METHOD_GET,
            'path'   => '/api/sports',
        ];
    }

    public function testSportList(): void
    {
        $route = $this->getRoute();
        $this->client->request(
            $route['method'],
            $route['path'],
            ['limit' => 3]
        );
        $sports = json_decode($this->client->getResponse()->getContent(), true);

        self::assertTrue($this->client->getResponse()->isOk());
        self::assertCount(3, $sports);
    }

    public function testSportListWithSearchTerm(): void
    {
        $route = $this->getRoute();
        $this->client->request(
            $route['method'],
            $route['path'],
            ['term' => 'hello-world']
        );

        self::assertTrue($this->client->getResponse()->isOk());
    }

    public function testSportListWithInvalidParams(): void
    {
        $route = $this->getRoute();
        $this->client->request(
            $route['method'],
            $route['path'],
            ['offset' => '','limit' => -3, 'order' => 'any']
        );
        $sports = json_decode($this->client->getResponse()->getContent(), true);

        self::assertTrue($this->client->getResponse()->isOk());
        self::assertCount(10, $sports);
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
            [Request::METHOD_DELETE],
            [Request::METHOD_PUT],
        ];
    }
}
