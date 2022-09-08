<?php

namespace App\Controller;

use App\Entity\DummyApi;
use App\Entity\DummyApiEndpoint;
use App\Service\Message\Flash\FlashMessageGenerator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/entity')]
class EntityController extends AbstractController
{
    private FlashMessageGenerator $flashMessageGenerator;

    public function __construct(private ManagerRegistry $doctrine){
    }

    #[Route('/create', name: 'app_entity_create')]
    public function create(Request $request): Response
    {
        $this->flashMessageGenerator = new FlashMessageGenerator($request);
        try {
            $parameters = $this->parseEntityParameters($request);
            if($parameters instanceof Response)
                return $parameters;

            $entityClass = $parameters["entity-class"];
            $successRedirect = $parameters["success-redirect"];
            $successRedirectType = $parameters["success-redirect-type"] ?? "route";
            $parameters = $parameters["parameters"];

            $entity = new $entityClass();
            $this->calculateDefaultFieldValues($entity, $request);
            foreach($parameters as $key => $value){
                $setter = sprintf("set%s", ucfirst($key));
                if(!method_exists($entity, $setter)) continue;
                $entity->$setter($value);
            }

            $this->doctrine->getManager()->persist($entity);
            $this->doctrine->getManager()->flush();
            return match ($successRedirectType) {
                "path" => $this->redirect($successRedirect),
                "entity-detail-route" => $this->redirectToRoute($successRedirect, ["id" => $entity->getId()]),
                "route" => $this->redirectToRoute($successRedirect),
                default => $this->redirectToRoute('app_admin'),
            };
        }
        catch (\Throwable $e){
            return $this->render('admin/error_page.html.twig', ["error" => $e]);
        }
    }


    #[Route('/update/{id}', name: 'app_entity_update')]
    public function update(string $id, Request $request): Response
    {
        $this->flashMessageGenerator = new FlashMessageGenerator($request);
        try {
            $parameters = $this->parseEntityParameters($request);
            if($parameters instanceof Response)
                return $parameters;

            $entityClass = $parameters["entity-class"];
            $successRedirect = $parameters["success-redirect"];
            $successRedirectType = $parameters["success-redirect-type"] ?? "route";
            $parameters = $parameters["parameters"];

            $entity = $this->doctrine->getRepository($entityClass)->findOneBy(["id" => $id]);
            $this->calculateDefaultFieldValues($entity, $request);
            foreach($parameters as $key => $value){
                $setter = sprintf("set%s", ucfirst($key));
                if(!method_exists($entity, $setter)) continue;
                $entity->$setter($value);
            }

            $this->doctrine->getManager()->flush();

            return match ($successRedirectType) {
                "path" => $this->redirect($successRedirect),
                "entity-detail-route" => $this->redirectToRoute($successRedirect, ["id" => $entity->getId()]),
                "route" => $this->redirectToRoute($successRedirect),
                default => $this->redirectToRoute('app_admin'),
            };
        }
        catch (\Throwable $e){
            return $this->render('admin/error_page.html.twig', ["error" => $e]);
        }
    }


    #[Route('/delete/{id}', name: 'app_entity_delete')]
    public function delete(string $id, Request $request): Response
    {
        $this->flashMessageGenerator = new FlashMessageGenerator($request);

        try {
            $parameters = $this->parseEntityParameters($request);
            if($parameters instanceof Response)
                return $parameters;

            $entityClass = $parameters["entity-class"];
            $successRedirect = $parameters["success-redirect"];
            $successRedirectType = $parameters["success-redirect-type"] ?? "route";

            $entity = $this->doctrine->getRepository($entityClass)->findOneBy(["id" => $id]);
            if($entity !== null) {
                $this->doctrine->getManager()->remove($entity);
                $this->doctrine->getManager()->flush();
            }
            else {
                $this->flashMessageGenerator->generateError(sprintf('%s with id "%s" not found', $entityClass, $id));
            }

            return match ($successRedirectType) {
                "path" => $this->redirect($successRedirect),
                "route" => $this->redirectToRoute($successRedirect),
                default => $this->redirectToRoute('app_admin'),
            };
        }
        catch (\Throwable $e){
            return $this->render('admin/error_page.html.twig', ["error" => $e]);
        }
    }

    private function calculateDefaultFieldValues(&$entity, Request $request){
        $reflectionClass = new \ReflectionClass($entity);
        foreach($reflectionClass->getProperties() as $reflectionProperty){
            $propName = $reflectionProperty->getName();
            $setter = sprintf("set%s", ucfirst($propName));
            $value = null;
            switch($propName){
                case "user":
                    $value = $this->getUser();
                    break;
                case "dummyApi":
                    $id = $request->request->get('dummyApiId');
                    if($id === null) break;
                    $value = $this->doctrine->getRepository(DummyApi::class)->findOneBy(["id" => $id]);
                    break;
                case "dummyApiEndpoint":
                    $id = $request->request->get('dummyApiEndpointId');
                    if($id === null) break;
                    $value = $this->doctrine->getRepository(DummyApiEndpoint::class)->findOneBy(["id" => $id]);
                    break;
                case "responseCode":
                    $value = (int)$request->request->get('responseCode');
                    break;
            }

            if($value === null){
                continue;
            }
            if(!method_exists($entity, $setter)){
                $this->flashMessageGenerator->generateError(sprintf("Method '%s' could not be found in entity '%s'", $setter, $entity::class));
                continue;
            }
            try {
                $entity->$setter($value);
            }
            catch (\Throwable $e){
                $this->flashMessageGenerator->generateError(sprintf("Field '%s' could not be set", $propName));
            }
        }
    }

    private function parseEntityParameters(Request $request): array|Response
    {
        $parameters = $request->request->all();
        if(!isset($parameters["entity-class"]) || !isset($parameters["success-redirect"])){
            $this->flashMessageGenerator->generateError('Not all parameters provided');
            $ref = $request->headers->get('referer');
            if($ref === null)
                return $this->redirectToRoute('app_admin');
            return $this->redirect($ref);
        }
        $entityClass = $parameters["entity-class"];
        $successRedirect = $parameters["success-redirect"];
        $successRedirectType = $parameters["success-redirect-type"] ?? "route";
        unset($parameters["entity-class"]);
        unset($parameters["success-redirect"]);
        unset($parameters["success-redirect-type"]);
        return [
            "entity-class" => $entityClass,
            "success-redirect" => $successRedirect,
            "success-redirect-type" => $successRedirectType,
            "parameters" => $parameters
        ];
    }
}
