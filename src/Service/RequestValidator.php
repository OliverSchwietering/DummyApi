<?php

namespace App\Service;

use App\Entity\DummyApi;
use App\Entity\DummyApiEndpoint;
use App\Entity\DummyApiHeader;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;

class RequestValidator
{
    public const HEADER_TYPE_REQUIRED_VALUE = "required-value";
    public const HEADER_TYPE_REQUIRED_ANY = "required-any";

    public static function valid(DummyApi $dummyApi, DummyApiEndpoint $dummyApiEndpoint, Request $request): bool
    {
        $methodValid = self::validateMethod($dummyApiEndpoint->getAllowedMethods(), $request->getMethod());
        if(!$methodValid) return false;

        $requiredHeaders = [];
        $givenHeaders = $request->headers;

        /** @var DummyApiHeader $header */
        foreach($dummyApi->getDummyApiHeaders()->getValues() as $header){
            $requiredHeaders[$header->getName()] = $header;
        }

        /** @var DummyApiHeader $header */
        foreach($dummyApiEndpoint->getDummyApiHeaders()->getValues() as $header){
            $requiredHeaders[$header->getName()] = $header;
        }

        $headersValid = self::validateHeaders($requiredHeaders, $givenHeaders);
        if(!$headersValid) return false;

        return true;
    }

    private static function validateMethod(array $allowedMethods, string $requestMethod): bool
    {
        $_allowedMethods = array_map(function(string $method){return strtolower($method);}, $allowedMethods);

        return in_array(strtolower($requestMethod), $_allowedMethods);
    }

    private static function validateHeaders(array $requiredHeaders, HeaderBag $givenHeaders): bool
    {
        /** @var DummyApiHeader $header */
        foreach($requiredHeaders as $header){
            $headerKey = strtolower($header->getName());
            switch($header->getType()){
                case self::HEADER_TYPE_REQUIRED_ANY:
                    if(!$givenHeaders->has($headerKey)) return false;
                    break;
                case self::HEADER_TYPE_REQUIRED_VALUE:
                    if(!$givenHeaders->has($headerKey) || $givenHeaders->get($headerKey) !== $header->getValue()) return false;
                    break;
            }
        }
        return true;
    }
}