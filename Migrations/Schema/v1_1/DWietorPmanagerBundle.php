<?php

namespace DWietor\Bundle\PmanagerBundle\Migrations\Schema\v1_1;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 */
class DWietorPmanagerBundle implements Migration
{
    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        /** Tables generation **/
        $this->addHeaderFooter($schema);
    }


    /**
     * Create d_wietor_pmanager_template table
     *
     * @param Schema $schema
     */
    protected function addHeaderFooter(Schema $schema)
    {
        $table = $schema->getTable('d_wietor_pmanager_template');
        $table->addColumn('hf','boolean', ['default' => false]);

    
    }

}
