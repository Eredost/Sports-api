<?php

namespace App\Tests\Controller\Sport;

use App\Entity\Sport;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SportEditControllerTest extends AbstractSportControllerTest
{
    protected function getRoute(): array
    {
        $sport = $this->entityManager
            ->getRepository(Sport::class)
            ->findOneBy([])
        ;

        return [
            'method' => Request::METHOD_PUT,
            'path'   => '/api/sports/' . $sport->getId(),
        ];
    }

    public function testSportEdit(): void
    {
        $route = $this->getRoute();
        $this->client->request(
            $route['method'],
            $route['path'],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'label' => 'newLabel',
            ]),
        );
        $modifiedSport = json_decode($this->client->getResponse()->getContent(), true);

        self::assertTrue($this->client->getResponse()->isOk());
        self::assertEquals('newLabel', $modifiedSport['label']);
    }

    public function testSportEditWithNonExistentSport(): void
    {
        $route = $this->getRoute();
        $this->client->request(
            $route['method'],
            '/api/sports/0',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'label' => 'labelValue',
            ]),
        );

        self::assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testSportEditWithInvalidValue(): void
    {
        $route= $this->getRoute();
        $this->client->request(
            $route['method'],
            $route['path'],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'label' => '',
            ]),
        );

        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
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
            [Request::METHOD_POST],
            [Request::METHOD_PATCH],
        ];
    }
}
