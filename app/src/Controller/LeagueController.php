<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Transport\HttpJsonTransport;
use App\Service\LeagueService;

class LeagueController extends RestController
{
    public function __construct(LeagueService $service)
    {
        $this->service = $service;
    }

    /**
     * @Route("/rest/league/{id}", requirements={"id"="\d+"}, methods={"DELETE"})
     */
    public function delete(string $id): Response
    {
        $this->service->delete((int) $id, $input->name, $input->league);

        return HttpJsonTransport::respond(null);
    }
}
