<?php
declare(strict_types = 1);

namespace App\Http\Controllers;

use Ufo\Client\Connection\Scopes;

final class Demo
{
    public function getScopes(Scopes $scopes)
    {
        $scopes->getScopes();
    }
}
