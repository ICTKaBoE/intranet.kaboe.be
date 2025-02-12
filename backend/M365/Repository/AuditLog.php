<?php

namespace M365\Repository;

use M365\GraphHelper;
use M365\Interface\Repository;
use Microsoft\Graph\Generated\AuditLogs\SignIns\SignInsRequestBuilderGetRequestConfiguration;
use Ouzo\Utilities\Clock;

class AuditLog extends Repository
{
    public function getWindowsSignIn($days = 0, $select = [])
    {
        $config = new SignInsRequestBuilderGetRequestConfiguration();
        $config->queryParameters = SignInsRequestBuilderGetRequestConfiguration::createQueryParameters();
        $config->queryParameters->top = 999;
        $config->queryParameters->filter = "((appDisplayName eq 'Windows Sign In') and (createdDateTime ge " . Clock::now()->minusDays($days)->format("Y-m-d") . "))";
        if ($select) $config->queryParameters->select = $select;

        return $this->iterate(GraphHelper::$appClient->auditLogs()->signIns()->get($config)->wait());
    }
}
