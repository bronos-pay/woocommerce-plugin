<?php

namespace BronosPay\Exception\Api;

use BronosPay\Exception\ApiErrorException;

/**
 * BadAuthToken is thrown when auth token is not valid and HTTP Status: 401 (Unauthorized).
 */
class BadAuthToken extends ApiErrorException
{
}
