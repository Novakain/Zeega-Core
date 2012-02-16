<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20120216215726 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
        
        $this->addSql("ALTER TABLE Item CHANGE media_creator_realname media_creator_realname VARCHAR(80) DEFAULT NULL");
        $this->addSql("ALTER TABLE Metadata CHANGE archive archive VARCHAR(50) DEFAULT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
        
        $this->addSql("ALTER TABLE Item CHANGE media_creator_realname media_creator_realname VARCHAR(80) NOT NULL");
        $this->addSql("ALTER TABLE Metadata CHANGE archive archive VARCHAR(50) NOT NULL");
    }
}
