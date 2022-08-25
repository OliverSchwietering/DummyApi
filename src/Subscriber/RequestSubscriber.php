<?php

namespace App\Subscriber;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Cmf\Component\Routing\Event\RouterGenerateEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class RequestSubscriber extends AbstractController implements \Symfony\Component\EventDispatcher\EventSubscriberInterface
{

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            RequestEvent::class => "onRequest",
            RouterGenerateEvent::class => "debug"
        ];
    }

    public function debug(RouterGenerateEvent $event){
        dd($event);
    }

    public function onRequest(RequestEvent $requestEvent){
        /*$urlParts = explode("/", $requestEvent->getRequest()->getRequestUri());
        foreach($urlParts as $key => $part){
            if($part === "") unset($urlParts[$key]);
        }
        if(empty($urlParts) || count($urlParts) < 2) return $requestEvent;
        $urlParts = array_values($urlParts);
        if($urlParts[0] === "dummy-api"){
            $id = $urlParts[1];
            unset($urlParts[0]);
            unset($urlParts[1]);
            dump($urlParts);
            dump('Location: /dummy-api/'.$id.'#'.implode("/", $urlParts));
            //dd(implode("/", $urlParts));
            header('Location: /dummy-api/'.$id.'#'.implode("/", $urlParts));
            exit(0);
            dd($this);
            return $this->redirectToRoute('app_dummy_api', ["dummyApiId" => $id, "path" => implode("/", $urlParts)]);
        }*/
        return $requestEvent;
    }
}