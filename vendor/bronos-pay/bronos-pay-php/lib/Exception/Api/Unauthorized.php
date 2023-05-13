<?php

namespace BronosPay\Exception\Api;

use BronosPay\Exception\ApiErrorException;

/**
 * Unauthorized is thrown when HTTP Status: 401 (Unauthorized).
 */
class Unauthorized extends ApiErrorException
{
}
