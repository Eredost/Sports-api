<?php

namespace App\Tests\Controller;

use App\DataFixtures\Providers\UserProvider;
use App\Entity\Sport;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SportControllerTest extends WebTestCase
{
    private ?KernelBrowser $client;

    private ?EntityManagerInterface $entityManager;

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

    public function testSportListWithoutJWT(): void
    {
        $this->client->setServerParameter('HTTP_Authorization', '');
        $this->client->request('GET','/api/sports', [], [], ['CONTENT_TYPE' => 'application/json']);

        self::assertTrue($this->client->getResponse()->isClientError());
    }

    public function testSportListWithInvalidJWT(): void
    {
        $this->client->setServerParameter('HTTP_Authorization', 'Bearer invalidJWT');
        $this->client->request('GET','/api/sports', [], [], ['CONTENT_TYPE' => 'application/json']);

        self::assertTrue($this->client->getResponse()->isClientError());
    }

    public function testSportList(): void
    {
        $this->client->request(
            'GET',
            '/api/sports',
            ['limit' => 3]
        );
        $sports = json_decode($this->client->getResponse()->getContent(), true);

        self::assertTrue($this->client->getResponse()->isOk());
        self::assertCount(3, $sports);
    }

    public function testSportListWithInvalidParams(): void
    {
        $this->client->request(
            'GET',
            '/api/sports',
            ['offset' => '','limit' => -3, 'order' => 'any']
        );
        $sports = json_decode($this->client->getResponse()->getContent(), true);

        self::assertTrue($this->client->getResponse()->isOk());
        self::assertCount(10, $sports);
    }

    public function testSportEdit(): void
    {
        $originalSport = $this->entityManager
            ->getRepository(Sport::class)
            ->findOneBy([])
        ;

        $this->client->request(
            'PUT',
            '/api/sports/' . $originalSport->getId(),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'label' => 'newLabel',
            ])
        );
        $modifiedSport = json_decode($this->client->getResponse()->getContent(), true);

        self::assertTrue($this->client->getResponse()->isOk());
        self::assertNotEquals($modifiedSport['label'], $originalSport->getLabel());
    }

    public function testSportEditWithNonExistentSport(): void
    {
        $this->client->request(
            'PUT',
            '/api/sports/0',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'label' => 'labelValue',
            ])
        );

        self::assertTrue($this->client->getResponse()->isClientError());
    }

    public function testSportEditWithInvalidValue(): void
    {
        $sport = $this->entityManager
            ->getRepository(Sport::class)
            ->findOneBy([])
        ;

        $this->client->request(
            'PUT',
            '/api/sports/' . $sport->getId(),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'label' => '',
            ])
        );

        self::assertTrue($this->client->getResponse()->isClientError());
    }

    public function testSportDeletion(): void
    {
        $sport = $this->entityManager
            ->getRepository(Sport::class)
            ->findOneBy([])
        ;

        $this->client->request(
            'DELETE',
            '/api/sports/' . $sport->getId(),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json']
        );

        self::assertEquals(204, $this->client->getResponse()->getStatusCode());
    }

    public function testSportDeletionWithNonExistentSport(): void
    {
        $this->client->request(
            'DELETE',
            '/api/sports/0',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json']
        );

        self::assertTrue($this->client->getResponse()->isClientError());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->client = null;
    }
}
