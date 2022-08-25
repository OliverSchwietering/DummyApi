<?php

namespace App\Service;

class ExceptionSerializer
{
    public static function serialize(\Throwable $e): string
    {
        $env = "prod";
        try {
            if(isset($_ENV["APP_ENV"]))
                $env = $_ENV["APP_ENV"];
        }
        catch (\Throwable $exception){}

        return $env === "dev" ? sprintf("ERROR '%s' thrown on line %d in file '%s'", $e->getMessage(), $e->getLine(), $e->getFile()) : sprintf("Error thrown: '%s'", $e->getMessage());
    }
}