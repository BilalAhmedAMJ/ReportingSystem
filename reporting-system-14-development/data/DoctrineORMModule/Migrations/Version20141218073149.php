<?php

namespace DoctrineORMModule\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141218073149 extends AbstractMigration
{



    public function preUp(Schema $schema)
    {
        /*Add reports config for Local level */
        $sql=" update users set member_code=case when member_code=0 then id else member_code end ";
        $this->connection->executeQuery($sql);         
    }

    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE users CHANGE displayname display_name VARCHAR(256) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E98B9D1CC4 ON users (member_code)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('DROP INDEX UNIQ_1483A5E98B9D1CC4 ON users');
        $this->addSql('ALTER TABLE users CHANGE display_name displayName VARCHAR(50) DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
