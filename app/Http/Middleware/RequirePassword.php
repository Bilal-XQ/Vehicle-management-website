<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\RequirePassword as Middleware;

class RequirePassword extends Middleware
{
    /**
     * The password timeout.
     *
     * @var int
     */
    protected $passwordTimeout = 10800;
}
