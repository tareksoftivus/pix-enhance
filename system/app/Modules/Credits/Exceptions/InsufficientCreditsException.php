<?php

namespace App\Modules\Credits\Exceptions;

use RuntimeException;

class InsufficientCreditsException extends RuntimeException
{
    public function __construct(int $available, int $required)
    {
        parent::__construct(__('You need :required credits, but only :available are available.', [
            'available' => $available,
            'required' => $required,
        ]));
    }
}
