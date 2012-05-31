<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20120409132700 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
        $this->addSql("CREATE INDEX enabled_index ON item (enabled)");
        $this->addSql("CREATE INDEX enabled_index ON project (enabled)");
        $this->addSql("update item set enabled = true, published = true");
        $this->addSql("update frame set enabled = true");
        $this->addSql("update project set enabled = true");
    }

    public function down(Schema $schema)
    {
    }
}