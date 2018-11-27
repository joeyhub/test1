<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\Tools\SchemaTool;
use App\DataFixtures\FootballFixtures;
use App\Service\AuthenticationService;

// Note: As this app is so small it's not really worth breaking this up into tests per controller yet.
class ControllerTest extends WebTestCase
{
    public static function setUpBeforeClass()
    {
        self::bootKernel();

        $entityManager = self::$container->get('doctrine')->getManager();

        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($entityManager);
        // Note: When using an inmemory database it doesn't appear to survive when the tests run.
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);

        (new FootballFixtures())->load($entityManager);

        $entityManager->flush();
        parent::setUpBeforeClass();
    }

    private function request(string $method, string $path, $data, bool $authenticate = true): Response
    {
        self::bootKernel();

        $service = self::$container->get(AuthenticationService::class);
        $client = static::createClient();

        $headers = [];
        if ($authenticate) {
            $headers['HTTP_'.AuthenticationService::HEADER] = AuthenticationService::PREFIX.$service->login();
        }

        // Note: There's a way to create routes from their id as well.
        $client->request($method, $path, [], [], $headers, json_encode($data));

        return $client->getResponse();
    }

    public function testForbiddenWhenUnauthenticated()
    {
        $response = $this->request('GET', '/rest/team/by/league/1', null, false);
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testCreateTeam()
    {
        $data = ['name' => 'Test Team 1', 'strip' => ['blue', 'red'], 'league' => 1];
        $response = $this->request('PUT', '/rest/team', $data);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testUpdateTeam()
    {
        // Note: Leaving id in here to prove a point (issue with extraneous members being ignored).
        $data = ['id' => 2, 'name' => 'Test Team 2', 'strip' => ['blue', 'red'], 'league' => 1];

        // Note: There's a way to create routes from their id as well.
        $response = $this->request('PUT', '/rest/team/1', $data);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testGetTeamsByLeague()
    {
        $response = $this->request('GET', '/rest/team/by/league/1', null);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testDeleteLeague()
    {
        $response = $this->request('DELETE', '/rest/league/1', null);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }
}
