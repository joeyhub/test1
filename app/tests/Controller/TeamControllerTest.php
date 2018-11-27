<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Service\AuthenticationService;

class TeamControllerTest extends WebTestCase
{
    public function testCreate()
    {
        self::bootKernel();
        $service = self::$container->get(AuthenticationService::class);
        $client = static::createClient();

        $data = ['name' => 'Test Team 1', 'strip' => ['blue', 'red'], 'league' => 1];
        $headers = [
            'HTTP_'.AuthenticationService::HEADER => AuthenticationService::PREFIX.$service->login(),
        ];

        // Note: There's a way to create routes from their id as well.
        $client->request('PUT', '/rest/team', [], [], $headers, json_encode($data));

        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
    }
}
