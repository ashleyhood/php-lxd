<?php

namespace Opensaucesystems\Lxd\Exception;

use Exception;

class ClientAuthenticationFailed extends Exception
{
    protected $message = 'LXD client certificate is not trusted.';
}
