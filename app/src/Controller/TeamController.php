<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Transport\HttpJsonTransport;
use App\Model\Strip;
use App\Service\TeamService;

class TeamController extends RestController
{
    public function __construct(TeamService $service)
    {
        $this->service = $service;
    }

    private static function getPersistArguments(Request $request): array
    {
        $input = HttpJsonTransport::getInput($request);

        return [$input->name, Strip::jsonUnserialize(...$input->strip), $input->league];
    }

    /**
     * @Route("/rest/team", methods={"PUT"})
     */
    public function create(Request $request): Response
    {
        $id = $this->service->create(...self::getPersistArguments($request));

        return HttpJsonTransport::respond($id, Response::HTTP_CREATED);
    }

    /**
     * @Route("/rest/team/{id}", requirements={"id"="\d+"}, methods={"PUT"})
     */
    public function update(string $id, Request $request): Response
    {
        $this->service->update((int) $id, ...self::getPersistArguments($request));

        return HttpJsonTransport::respond(null);
    }

    /**
     * @Route("/rest/team/by/league/{league}", requirements={"league"="\d+"}, methods={"GET"})
     */
    public function listByLeague(string $league): Response
    {
        return HttpJsonTransport::respond($this->service->findByLeague((int) $league));
    }
}
