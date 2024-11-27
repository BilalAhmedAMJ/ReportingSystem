<?php

namespace DoctrineORMModule\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141222084216 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        

        $this->addSql('ALTER TABLE office_assignment_requests CHANGE processed_by_user_id processed_by_user_id INT DEFAULT NULL, CHANGE date_processed date_processed DATETIME DEFAULT NULL, CHANGE processor_comments processor_comments LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE users CHANGE display_name display_name VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE office_assignment_requests CHANGE processed_by_user_id processed_by_user_id INT NOT NULL, CHANGE date_processed date_processed DATETIME NOT NULL, CHANGE processor_comments processor_comments LONGTEXT NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE users CHANGE display_name display_name VARCHAR(256) DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
