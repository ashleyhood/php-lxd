<?php

namespace Opensaucesystems\Lxd\Exception;

use Exception;

class SourceImageException extends Exception
{
    protected $message = "Specify source image by alias, fingerprint, or properties, or create an empty container";
}
