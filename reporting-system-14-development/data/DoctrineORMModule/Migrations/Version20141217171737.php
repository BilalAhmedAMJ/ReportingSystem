<?php

namespace DoctrineORMModule\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141217171737 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE IF NOT EXISTS branch_area_link (branch_id INT NOT NULL, branch_area_id INT NOT NULL, INDEX IDX_F663863DDCD6CC49 (branch_id), UNIQUE INDEX UNIQ_F663863DA6FB8492 (branch_area_id), PRIMARY KEY(branch_id, branch_area_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS office_assignment_requests (id INT AUTO_INCREMENT NOT NULL, office_assignment_id INT DEFAULT NULL, requested_by_user_id INT NOT NULL, processed_by_user_id INT NOT NULL, full_Name VARCHAR(50) DEFAULT NULL, email VARCHAR(255) NOT NULL, member_code INT NOT NULL, phone_primary VARCHAR(255) NOT NULL, phone_alternate VARCHAR(255) DEFAULT NULL, status VARCHAR(255) NOT NULL, request_reason LONGTEXT NOT NULL, date_requested DATETIME NOT NULL, date_processed DATETIME NOT NULL, processor_comments LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_A48E8FEDE7927C74 (email), INDEX IDX_A48E8FED9FB2D494 (office_assignment_id), INDEX IDX_A48E8FEDA2DD2669 (requested_by_user_id), INDEX IDX_A48E8FEDD3492A80 (processed_by_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE branch_area_link ADD CONSTRAINT FK_F663863DDCD6CC49 FOREIGN KEY (branch_id) REFERENCES branches (id)');
        $this->addSql('ALTER TABLE branch_area_link ADD CONSTRAINT FK_F663863DA6FB8492 FOREIGN KEY (branch_area_id) REFERENCES branches (id)');
        $this->addSql('ALTER TABLE office_assignment_requests ADD CONSTRAINT FK_A48E8FED9FB2D494 FOREIGN KEY (office_assignment_id) REFERENCES office_assignments (id)');
        $this->addSql('ALTER TABLE office_assignment_requests ADD CONSTRAINT FK_A48E8FEDA2DD2669 FOREIGN KEY (requested_by_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE office_assignment_requests ADD CONSTRAINT FK_A48E8FEDD3492A80 FOREIGN KEY (processed_by_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE office_assignments DROP FOREIGN KEY FK_A60126FD3492A80');
        $this->addSql('ALTER TABLE office_assignments DROP FOREIGN KEY FK_A60126FA2DD2669');
        $this->addSql('DROP INDEX IDX_A60126FA2DD2669 ON office_assignments');
        $this->addSql('DROP INDEX IDX_A60126FD3492A80 ON office_assignments');
        $this->addSql('ALTER TABLE office_assignments DROP processed_by_user_id, DROP requested_by_user_id, DROP request_reason, DROP processor_comments, DROP date_requested, DROP date_processed, CHANGE user_id user_id INT DEFAULT NULL, CHANGE status status ENUM(\'approved\', \'former\',\'pending\',\'deleted\',\'requested\',\'denied\'), CHANGE assignment_type assignment_type ENUM(\'responsible\',\'supervise\',\'oversee\')');

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('DROP TABLE IF EXISTS branch_area_link');
        $this->addSql('DROP TABLE IF EXISTS documents');
        $this->addSql('DROP TABLE IF EXISTS office_assignment_requests');

        $this->addSql('ALTER TABLE office_assignments ADD processed_by_user_id INT NOT NULL, ADD requested_by_user_id INT NOT NULL, ADD request_reason LONGTEXT NOT NULL COLLATE utf8_unicode_ci, ADD processor_comments LONGTEXT NOT NULL COLLATE utf8_unicode_ci, ADD date_requested DATETIME NOT NULL, ADD date_processed DATETIME NOT NULL, CHANGE user_id user_id INT NOT NULL, CHANGE status status VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE assignment_type assignment_type VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE office_assignments ADD CONSTRAINT FK_A60126FD3492A80 FOREIGN KEY (processed_by_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE office_assignments ADD CONSTRAINT FK_A60126FA2DD2669 FOREIGN KEY (requested_by_user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_A60126FA2DD2669 ON office_assignments (requested_by_user_id)');
        $this->addSql('CREATE INDEX IDX_A60126FD3492A80 ON office_assignments (processed_by_user_id)');

    }
}
