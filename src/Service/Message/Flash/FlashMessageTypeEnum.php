<?php

namespace App\Service\Message\Flash;

enum FlashMessageTypeEnum: string
{
    case TYPE_SUCCESS = "success";
    case TYPE_WARNING = "warning";
    case TYPE_DANGER = "danger";
    case TYPE_INFO = "info";
}