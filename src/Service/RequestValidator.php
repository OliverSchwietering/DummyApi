<?php

namespace App\Service;

use App\Entity\DummyApi;
use App\Entity\DummyApiEndpoint;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;

class RequestValidator
{
    public static function valid(DummyApi $dummyApi, Request $request): bool
    {
        dd(array_map(function(DummyApiEndpoint $endpoint){return $endpoint->getDummyApiHeaders()->getValues();}, $dummyApi->getDummyApiEndpoints()->getValues()));
        $requiredHeaders = array_merge($dummyApi->getDummyApiHeaders()->getValues(), array_map(function(DummyApiEndpoint $endpoint){return $endpoint->getDummyApiHeaders()->getValues();}, $dummyApi->getDummyApiEndpoints()->getValues()));
        $givenHeaders = $request->headers;

        $headersValid = self::validateHeaders($requiredHeaders, $givenHeaders);
        return false;
    }

    private static function validateHeaders(array $requiredHeaders, HeaderBag $givenHeaders): bool
    {

    }
}