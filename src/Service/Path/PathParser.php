<?php

namespace App\Service\Path;

class PathParser
{
    public static function parse(string $path): string
    {
        if(str_contains($path, "?"))
            $path = explode("?", $path)[0];
        if(str_contains($path, "#"))
            $path = explode("#", $path)[0];
        if(!str_starts_with($path, "/"))
            $path = sprintf("/%s",$path);
        if(!str_ends_with($path, "/"))
            $path = sprintf("%s/",$path);
        return trim($path);
    }
}