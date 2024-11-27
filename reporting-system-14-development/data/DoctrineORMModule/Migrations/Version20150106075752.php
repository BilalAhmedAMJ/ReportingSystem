<?php

namespace DoctrineORMModule\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150106075752 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE departments ADD reportable TINYINT(1) NOT NULL default 1');
        $this->addSql("ALTER TABLE office_assignments ADD supervise_departments TEXT NOT NULL COMMENT '(DC2Type:simple_array)', ADD oversee_departments TEXT NOT NULL COMMENT '(DC2Type:simple_array)', DROP assignment_type");
        
        $this->addSql("ALTER TABLE reports drop column report_type");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE departments DROP reportable');
        $this->addSql('ALTER TABLE office_assignments ADD assignment_type VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, DROP supervise_departments, DROP oversee_departments');
        $this->addSql("ALTER TABLE reports ADD report_type varchar(255)");
    }
}
