<?php

namespace DoctrineORMModule\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141126074029 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE office_assignments (id INT AUTO_INCREMENT NOT NULL, branch_id INT NOT NULL, department_id INT NOT NULL, period_from VARCHAR(128) NOT NULL, user_id INT NOT NULL, status ENUM(\'active\',\'approved\', \'former\',\'pending\',\'deleted\'), INDEX IDX_A60126FDCD6CC49 (branch_id), INDEX IDX_A60126FAE80F5DF (department_id), INDEX IDX_A60126F7F1C2BDE (period_from), INDEX IDX_A60126FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE periods (period_code VARCHAR(128) NOT NULL, year_code VARCHAR(128) NOT NULL, PRIMARY KEY(period_code)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE office_assignments ADD CONSTRAINT FK_A60126FDCD6CC49 FOREIGN KEY (branch_id) REFERENCES branches (id)');
        $this->addSql('ALTER TABLE office_assignments ADD CONSTRAINT FK_A60126FAE80F5DF FOREIGN KEY (department_id) REFERENCES departments (id)');
        $this->addSql('ALTER TABLE office_assignments ADD CONSTRAINT FK_A60126F7F1C2BDE FOREIGN KEY (period_from) REFERENCES periods (period_code)');
        $this->addSql('ALTER TABLE office_assignments ADD CONSTRAINT FK_A60126FA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE office_assignments DROP FOREIGN KEY FK_A60126F7F1C2BDE');
        $this->addSql('DROP TABLE office_assignments');
        $this->addSql('DROP TABLE periods');
    }
    
   public function postUp(Schema $schema){
        $sql="insert into periods values 
            ('Jul-2014','2014-2015'),
            ('Aug-2014','2014-2015'),
            ('Sep-2014','2014-2015'),
            ('OCt-2014','2014-2015'),
            ('Nov-2014','2014-2015'),
            ('Dec-2014','2014-2015'),
            ('Jan-2015','2014-2015'),
            ('Feb-2015','2014-2015'),
            ('Mar-2015','2014-2015'),
            ('Apr-2015','2014-2015'),
            ('May-2015','2014-2015'),
            ('Jun-2015','2014-2015')
       ";
        $this->connection->executeQuery($sql); 
    }    
}
