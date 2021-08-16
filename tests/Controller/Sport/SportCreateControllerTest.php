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
        $newSportLabel = 'brandNewLabel';
        $this->client->request(
            Request::METHOD_POST,
            '/api/sports',
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
    }

    public function testSportCreationWithMissingLabel(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            '/api/sports',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json']
        );

        self::assertTrue($this->client->getResponse()->isClientError());
    }
}
