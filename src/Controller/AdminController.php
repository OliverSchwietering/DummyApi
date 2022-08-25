<?php

namespace App\Controller;

use App\Entity\DummyApi;
use App\Entity\DummyApiEndpoint;
use App\Entity\DummyApiHeader;
use App\Entity\User;
use App\Service\Message\Flash\FlashMessageGenerator;
use App\Service\Message\Flash\FlashMessageTypeEnum;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    public function __construct(private ManagerRegistry $doctrine){}

    #[Route("/", name: 'app_root')]
    public function rootRedirect(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->redirectToRoute('app_admin');
    }

    #[Route('/admin', name: 'app_admin')]
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $flashMessageGenerator = new FlashMessageGenerator($request);
        return $this->render('admin/index.html.twig', []);
    }

    #[Route('/admin/_error', name: 'app_admin_error')]
    public function error(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $body = [];
        $err = $request->get('error-message');
        if($err !== null)
            $body = ["error" => ["message" => $err]];
        return $this->render('admin/error.html.twig', $body);
    }

    #[Route('/admin/dummy-api/create', name: 'app_admin_api_create')]
    public function apiCreate(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $entity = new DummyApi();
        return $this->render('admin/api_detail.html.twig', ["entityClass" => DummyApi::class,"entity" => $entity,"create" => true]);
    }

    #[Route('/admin/dummy-api/{id}', name: 'app_admin_api_detail')]
    public function apiDetail(string $id, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $api = $this->doctrine->getRepository(DummyApi::class)->findOneBy(["id" => $id, "user" => $this->getUser()]);
        return $this->render('admin/api_detail.html.twig', ["entityClass" => DummyApi::class,"entity" => $api, "dummyEndpointEntityClass" => DummyApiEndpoint::class, "dummyHeaderEntityClass" => DummyApiHeader::class]);
    }
}
