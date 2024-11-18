<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Setup\Controller;

use Magento\Framework\App\SetupInfo;
use Magento\Framework\Config\ConfigOptionsListConstants;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class Elasticsearch extends AbstractActionController
{
    public const NOSQL_DB_OPENSEARCH = 'opensearch';

    public const NOSQL_DB_ELASTICSEARCH = 'elasticsearch';

    /**
     * Displays web configuration form
     *
     * @return array|ViewModel
     */
    public function indexAction()
    {
        $view = new ViewModel();
        $view = new ViewModel(
            [
                'nosqldb'   => [
                    self::NOSQL_DB_OPENSEARCH,
                    self::NOSQL_DB_ELASTICSEARCH,
                ],
            ]
        );

        $view->setTerminal(true);
        return $view;
    }
}
