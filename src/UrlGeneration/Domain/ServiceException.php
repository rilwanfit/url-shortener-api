<?php
declare(strict_types=1);

namespace App\UrlGeneration\Domain;

use Exception;

class ServiceException extends Exception
{
    public static function duringUrlShortning(): self
    {
        return new self('There has been an error while trying to shorten the Url');
    }
}