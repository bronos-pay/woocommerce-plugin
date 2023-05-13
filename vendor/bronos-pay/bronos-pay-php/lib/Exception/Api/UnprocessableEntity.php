<?php

namespace BronosPay\Exception\Api;

use BronosPay\Exception\ApiErrorException;

/**
 * UnprocessableEntity is thrown when HTTP Status: 422 (Unprocessable Entity).
 */
class UnprocessableEntity extends ApiErrorException
{
}
