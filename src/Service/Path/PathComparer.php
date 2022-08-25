<?php

namespace App\Service\Path;

use JetBrains\PhpStorm\Pure;

class PathComparer
{
    #[Pure]
    public static function compare(string $path1, string $path2): bool
    {
        return PathParser::parse($path1) === PathParser::parse($path2);
    }
}