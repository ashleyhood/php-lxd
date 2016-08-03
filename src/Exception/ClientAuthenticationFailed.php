<?php

namespace Opensaucesystems\Lxd\Exception;

use Exception;

class ClientAuthenticationFailed extends Exception
{
    protected $message = 'LXD client certificates are not trusted.';
}
