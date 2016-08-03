<?php

namespace Opensaucesystems\Lxd\Exception;

use Exception;

class ClientConnectionException extends Exception
{
    protected $message = 'LXD client connection failed.';
}
