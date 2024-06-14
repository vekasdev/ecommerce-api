<?php

namespace App\interfaces;

use App\model\EntityService;

interface EntityServiceProviderInterface {
    function provide($class,$id) : EntityServiceInterface;
}