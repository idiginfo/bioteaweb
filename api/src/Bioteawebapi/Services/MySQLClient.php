<?php

namespace Bioteawebapi\Services;

class MySQLClient
{
    private $dbal;

    public function __construct($dbal)
    {
        $this->dbal = $dbal;
    }
}