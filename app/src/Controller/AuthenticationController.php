<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Transport\HttpJsonTransport;
use App\Service\AuthenticationService;

class AuthenticationController extends RestController
{
    public function __construct(AuthenticationService $service)
    {
        $this->service = $service;
    }

    /**
     * @Route("/rest/login", methods={"GET"})
     */
    public function listByLeague(): Response
    {
        return HttpJsonTransport::respond($this->service->login());
    }
}
