<?php

namespace App\Controller;

use App\Entity\DummyApi;
use App\Entity\DummyApiEndpoint;
use App\Service\Path\PathComparer;
use App\Service\Path\PathParser;
use App\Service\RequestValidator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Uuid;

class DummyApiController extends AbstractController
{

    public function __construct(private ManagerRegistry $doctrine){}

    #[Route('/dummy-api/{dummyApiId}', name: 'app_dummy_api_base')]
    public function dummyApiBase(): Response
    {
        return new JsonResponse(["message" => "This is the base path"]);
    }

    #[Route('/dummy-api/{dummyApiId}/{path}', name: 'app_dummy_api', requirements: ['path' => '.+'])]
    public function dummyApiEndpoint(string $dummyApiId, string $path, Request $request): Response
    {
        $repository = $this->doctrine->getRepository(DummyApi::class);
        if(!\Symfony\Component\Uid\Uuid::isValid($dummyApiId)) return new JsonResponse(["message" => "Id not valid"]);
        /** @var DummyApi|null $entity */
        $entity = $repository->findOneBy(["id" => $dummyApiId]);
        if($entity === null) return new JsonResponse(["message" => "Dummy Api not found"]);
        $endpoints = $entity->getDummyApiEndpoints()->getValues();
        $search = array_filter($endpoints, function(DummyApiEndpoint $endpoint)use($path){return PathComparer::compare($endpoint->getPath(), $path);});
        if(empty($search)) return new JsonResponse(["message" => "Endpoint not found"]);
        $dummyApiEndpoint = $search[array_key_first($search)];

        $requestValid = RequestValidator::valid($entity, $dummyApiEndpoint, $request);
        if(!$requestValid) return new JsonResponse(["message" => "Given request data is not valid (missing header-variable or wrong method?)"]);

        $contentType = match($dummyApiEndpoint->getContentType()){
            "json" => "application/json",
            "xml" => "application/xml",
            default => null
        };

        $response = new Response();
        $response->setStatusCode($dummyApiEndpoint->getResponseCode() ?? 200);
        $response->setContent($dummyApiEndpoint->getResponseContent() ?? null);
        if($contentType !== null)
            $response->headers->set('Content-Type', $contentType);
        return $response;
    }

}
