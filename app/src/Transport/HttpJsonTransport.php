<?php

namespace App\Transport;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

abstract class HttpJsonTransport
{
    public static function respond($data, int $code = Response::HTTP_OK): Response
    {
        return new JsonResponse($data, $code);
    }

    public static function getInput(Request $request): object
    {
        /**
         * Note: Allowing deserialization to object could have some security concerns though it also preserves type
         * properly (empty object/array problem).
         * Note: Low depth set because it's not needed to be particularly high though it's also not very future proof.
         */
        $data = json_decode($request->getContent(), false, 8);

        // Note: Can't wait for PHP 7.3 with throw on error instead of this.
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \InvalidArgumentException('Error parsing JSON: '.json_last_error_msg());
        }

        return $data;
    }

    public static function getExceptionResponse(\Throwable $exception): Response
    {
        $response = new JsonResponse(null, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);

        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        } else {
            $response->setStatusCode(JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Note: This might clobber other special headers set above.
        $response->headers->set('Content-Type', 'application/problem+json');
        // Note: This might show messages that should be logged only. There should be an interface for displayable errors instead.
        $response->setData([
            'status' => $response->getStatusCode(),
            'detail' => $exception->getMessage(),
        ]);

        return $response;
    }
}
