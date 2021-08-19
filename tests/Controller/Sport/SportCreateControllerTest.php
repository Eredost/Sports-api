<?php

namespace App\Tests\Controller\Sport;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SportCreateControllerTest extends AbstractSportControllerTest
{
    protected function getRoute(): array
    {
        return [
            'method' => Request::METHOD_POST,
            'path'   => '/api/sports',
        ];
    }

    public function testSuccessfulSportCreation(): void
    {
        $route = $this->getRoute();
        $newSportLabel = 'brandNewLabel';
        $this->client->request(
            $route['method'],
            $route['path'],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'label' => $newSportLabel,
            ])
        );
        $sport = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
        self::assertEquals($newSportLabel, $sport['label']);
        self::assertNotFalse(strtotime($sport['createdAt']));
    }

    public function testCreationWithMissingLabel(): void
    {
        $route = $this->getRoute();
        $this->client->request(
            $route['method'],
            $route['path'],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json']
        );

        self::assertTrue($this->client->getResponse()->isClientError());
    }
    
    public function testCreationWithMalformedJSONBody(): void
    {
        $route = $this->getRoute();
        $this->client->request(
            $route['method'],
            $route['path'],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{{}'
        );

        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
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
            [],
            [],
            ['CONTENT_TYPE' => 'application/json']
        );

        self::assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode());
    }

    public function notAllowedMethodsProvider(): array
    {
        return [
            [Request::METHOD_DELETE],
            [Request::METHOD_PATCH],
            [Request::METHOD_PUT],
        ];
    }
}
