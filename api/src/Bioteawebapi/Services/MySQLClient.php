<?php

namespace Bioteawebapi\Services;
use Doctrine\DBAL\Connection;

class MySQLClient
{
    /**
     * @var Doctrine\DBAL\Connection
     */
    private $dbal;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param Doctrine\DBAL\Connection
     */
    public function __construct(Connection $dbal)
    {
        $this->dbal = $dbal;
    }
}

/* EOF: MySQLClient.php */