<?php

namespace App\Service\Message\Flash;

use App\Service\ExceptionSerializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class FlashMessageGenerator
{
    public function __construct(private Request &$generatingObject){}

    public function generate(FlashMessageTypeEnum $type, string $message){
        $this->generatingObject->getSession()->getFlashBag()->add($type->value, $message);
    }

    public function generateSuccess(string $message){
        $this->generate(FlashMessageTypeEnum::TYPE_SUCCESS, $message);
    }

    public function generateWarning(string $message){
        $this->generate(FlashMessageTypeEnum::TYPE_WARNING, $message);
    }

    public function generateInfo(string $message){
        $this->generate(FlashMessageTypeEnum::TYPE_INFO, $message);
    }

    public function generateError(string|\Throwable $message){
        $_message = $message;
        if($message instanceof \Throwable)
            $_message = ExceptionSerializer::serialize($message);
        $this->generate(FlashMessageTypeEnum::TYPE_DANGER, $_message);
    }
}