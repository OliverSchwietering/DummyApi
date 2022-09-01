<?php

namespace App\Service\Twig;

use App\Entity\DummyApi;
use App\Entity\DummyApiEndpoint;
use App\Entity\DummyApiHeader;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Twig\TwigFunction;

class TwigExtension extends \Twig\Extension\AbstractExtension
{

    public function __construct(private ManagerRegistry $doctrine, private Security $security, private RequestStack $requestStack){}

    public function getFunctions()
    {
        return [
            new TwigFunction('getMenuData', [$this, 'getMenuData']),
            new TwigFunction('getApis', [$this, 'getApis']),
            new TwigFunction('getApiClassName', [$this, 'getApiClassName']),
            new TwigFunction('getApiEndpointClassName', [$this, 'getApiEndpointClassName']),
            new TwigFunction('getApiHeaderClassName', [$this, 'getApiHeaderClassName']),
            new TwigFunction('getUser', [$this->security, 'getUser'])
        ];
    }

    #[ArrayShape(["apis" => "\string[][]"])]
    public function getMenuData(): array
    {
        $menu = [
            "menuEntries" => ["dashboard" => [], "api" => [], "account" => []],
            "apis" => []
        ];

        $currentRoute = $this->getCurrentRoute();
        foreach($menu["menuEntries"] as $key => $entry){
            switch($key){
                case "dashboard":
                    if($currentRoute === "app_admin" || str_contains($currentRoute, "app_admin_dashboard"))
                        $menu["menuEntries"][$key] = ["active" => true];
                    break;
                case "api":
                    if(str_contains($currentRoute, "app_admin_api"))
                        $menu["menuEntries"][$key] = ["active" => true];
                    break;
                case "account":
                    if(str_contains($currentRoute, "app_admin_account"))
                        $menu["menuEntries"][$key] = ["active" => true];
                    break;
            }
        }

        $apis = $this->doctrine->getRepository(DummyApi::class)->findBy([
            "user" => $this->security->getUser()
        ]);
        /** @var DummyApi $api */
        foreach($apis as $api){
            $menu["apis"][] = [
                "id" => $api->getId(),
                "name" => $api->getName()
            ];
        }
        return $menu;
    }

    public function getApis(): array
    {
        return $this->doctrine->getRepository(DummyApi::class)->findBy([
            "user" => $this->security->getUser()
        ]);
    }

    public function getApiClassName(): string
    {
        return DummyApi::class;
    }

    public function getApiEndpointClassName(): string
    {
        return DummyApiEndpoint::class;
    }

    public function getApiHeaderClassName(): string
    {
        return DummyApiHeader::class;
    }

    private function getCurrentRoute(){
        return $this->requestStack->getCurrentRequest()->attributes->get('_route') ?? 'app_admin';
    }
}