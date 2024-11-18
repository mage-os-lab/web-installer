<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Setup\Model;

use Magento\Framework\Setup\Option\AbstractConfigOption;
use Magento\Framework\Setup\Option\SelectConfigOption;
use Magento\Framework\Setup\Option\TextConfigOption;

/**
 * Search engine configuration options for install
 */
class SearchConfigOptionsList
{
    /**
     * Input key for the options
     */
    const INPUT_KEY_SEARCH_ENGINE = 'search-engine';
    const INPUT_KEY_ELASTICSEARCH_HOST = 'opensearch-host';
    const INPUT_KEY_ELASTICSEARCH_PORT = 'opensearch-port';
    const INPUT_KEY_ELASTICSEARCH_ENABLE_AUTH = 'opensearch-enable-auth';
    const INPUT_KEY_ELASTICSEARCH_USERNAME = 'opensearch-username';
    const INPUT_KEY_ELASTICSEARCH_PASSWORD = 'opensearch-password';
    const INPUT_KEY_ELASTICSEARCH_INDEX_PREFIX = 'opensearch-index-prefix';
    const INPUT_KEY_ELASTICSEARCH_TIMEOUT = 'opensearch-timeout';

    /**
     * Get options list for search engine configuration
     *
     * @return AbstractConfigOption[]
     */
    public function getOptionsList(): array
    {
        return [
            new SelectConfigOption(
                self::INPUT_KEY_SEARCH_ENGINE,
                SelectConfigOption::FRONTEND_WIZARD_SELECT,
                array_keys($this->getAvailableSearchEngineList()),
                '',
                'Search engine. Values: ' . implode(', ', array_keys($this->getAvailableSearchEngineList()))
            ),
            new TextConfigOption(
                self::INPUT_KEY_ELASTICSEARCH_HOST,
                TextConfigOption::FRONTEND_WIZARD_TEXT,
                '',
                'Elasticsearch server host.'
            ),
            new TextConfigOption(
                self::INPUT_KEY_ELASTICSEARCH_PORT,
                TextConfigOption::FRONTEND_WIZARD_TEXT,
                '',
                'Elasticsearch server port.'
            ),
            new TextConfigOption(
                self::INPUT_KEY_ELASTICSEARCH_ENABLE_AUTH,
                TextConfigOption::FRONTEND_WIZARD_TEXT,
                '',
                'Set to 1 to enable authentication. (default is 0, disabled)'
            ),
            new TextConfigOption(
                self::INPUT_KEY_ELASTICSEARCH_USERNAME,
                TextConfigOption::FRONTEND_WIZARD_TEXT,
                '',
                'Elasticsearch username. Only applicable if HTTP auth is enabled'
            ),
            new TextConfigOption(
                self::INPUT_KEY_ELASTICSEARCH_PASSWORD,
                TextConfigOption::FRONTEND_WIZARD_PASSWORD,
                '',
                'Elasticsearch password. Only applicable if HTTP auth is enabled'
            ),
            new TextConfigOption(
                self::INPUT_KEY_ELASTICSEARCH_INDEX_PREFIX,
                TextConfigOption::FRONTEND_WIZARD_TEXT,
                '',
                'Elasticsearch index prefix.'
            ),
            new TextConfigOption(
                self::INPUT_KEY_ELASTICSEARCH_TIMEOUT,
                TextConfigOption::FRONTEND_WIZARD_TEXT,
                '',
                'Elasticsearch server timeout.'
            )
        ];
    }

    /**
     * Get UI friendly list of available search engines
     *
     * @return array
     */
    public function getAvailableSearchEngineList(): array
    {
        return [
            'opensearch' => 'OpenSearch',
            'elasticsearch7' => 'Elasticsearch 7.x'
        ];
    }
}
