<?php

namespace App\Tests\Controller\Sport;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SportCreateControllerTest extends AbstractSportControllerTest
{
    private const ROUTE_PATH = '/api/sports';
    
    private const HTTP_METHOD = Request::METHOD_POST;

    protected function getRoute(): array
    {
        return [
            'method' => self::HTTP_METHOD,
            'path'   => self::ROUTE_PATH,
        ];
    }

    public function testSuccessfulSportCreation(): void
    {
        $newSportLabel = 'brandNewLabel';
        $this->client->request(
            self::HTTP_METHOD,
            self::ROUTE_PATH,
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
        $this->client->request(
            self::HTTP_METHOD,
            self::ROUTE_PATH,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json']
        );

        self::assertTrue($this->client->getResponse()->isClientError());
    }
    
    public function testCreationWithMalformedJSONBody(): void
    {
        $this->client->request(
            self::HTTP_METHOD,
            self::ROUTE_PATH,
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
        $this->client->request(
            $method,
            self::ROUTE_PATH,
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
