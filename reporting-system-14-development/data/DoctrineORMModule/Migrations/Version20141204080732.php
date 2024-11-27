<?php

namespace DoctrineORMModule\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141204080732 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE office_assignments ADD requested_by_user_id INT NOT NULL, ADD processed_by_user_id INT NOT NULL, ADD assignment_type ENUM(\'responsible\',\'supervise\',\'oversee\'), ADD request_reason LONGTEXT NOT NULL, ADD processor_comments LONGTEXT NOT NULL, ADD date_requested DATETIME NOT NULL, ADD date_processed DATETIME NOT NULL, CHANGE status status ENUM(\'approved\', \'former\',\'pending\',\'deleted\',\'requested\',\'denied\')');
        $this->addSql('ALTER TABLE office_assignments ADD CONSTRAINT FK_A60126FA2DD2669 FOREIGN KEY (requested_by_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE office_assignments ADD CONSTRAINT FK_A60126FD3492A80 FOREIGN KEY (processed_by_user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_A60126FA2DD2669 ON office_assignments (requested_by_user_id)');
        $this->addSql('CREATE INDEX IDX_A60126FD3492A80 ON office_assignments (processed_by_user_id)');
        $this->addSql('ALTER TABLE periods ADD period_start DATETIME NOT NULL, ADD period_end DATETIME NOT NULL');
        $this->addSql('ALTER TABLE reports CHANGE status status ENUM(\'draft\', \'completed\',\'verified\',\'received\',\'deleted\')');
        $this->addSql('ALTER TABLE report_config CHANGE status status ENUM(\'active\', \'disabled\')');
        $this->addSql('ALTER TABLE users ADD status ENUM(\'active\',\'locked\',\'inactive\',\'deleted\',\'expired\'), ADD password_expiry_date DATE NOT NULL, ADD password_last_reset DATE NOT NULL, ADD member_code INT NOT NULL, ADD phone_primary VARCHAR(255) NOT NULL, ADD phone_alternate VARCHAR(255) DEFAULT NULL');

        $this->addSql('CREATE TABLE config (config_item VARCHAR(255) NOT NULL, config_value LONGTEXT NOT NULL, PRIMARY KEY(config_item)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');


    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE config');
        
        $this->addSql('ALTER TABLE office_assignments DROP FOREIGN KEY FK_A60126FA2DD2669');
        $this->addSql('ALTER TABLE office_assignments DROP FOREIGN KEY FK_A60126FD3492A80');
        $this->addSql('DROP INDEX IDX_A60126FA2DD2669 ON office_assignments');
        $this->addSql('DROP INDEX IDX_A60126FD3492A80 ON office_assignments');
        $this->addSql('ALTER TABLE office_assignments DROP requested_by_user_id, DROP processed_by_user_id, DROP assignment_type, DROP request_reason, DROP processor_comments, DROP date_requested, DROP date_processed, CHANGE status status VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE periods DROP period_start, DROP period_end');
        $this->addSql('ALTER TABLE report_config CHANGE status status VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE reports CHANGE status status VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE users DROP status, DROP password_expiry_date, DROP password_last_reset, DROP member_code, DROP phone_primary, DROP phone_alternate');
    }

    public function preUp(Schema $schema)
    {
        $sql="create table periods_201412040732 
                select period_code,year_code,
                str_to_date(concat('01-',period_code),'%d-%M-%Y') as period_stat,
                str_to_date(concat('01-',period_code),'%d-%M-%Y')+interval 1 month - interval 1 second as period_end
                 from periods";
        $this->connection->executeQuery($sql);   
        
        $sql="delete from periods";
        $this->connection->executeQuery($sql);   
                                  
    }
    public function postUp(Schema $schema)
    {
       $sql="insert into periods select * from periods_201412040732";
       $this->connection->executeQuery($sql);          
       $sql="drop table periods_201412040732";
       $this->connection->executeQuery($sql);          
               
        /*Add reports config for Local level */
        $sql="replace into config values ('default_dept_assignments',
                \"{
                    'PT' : {
                            'responsible':'PR',
                            'supervise':['SM','DT','FE','GS','IT','JD','MB','RA','SB','ST','TM','TH','TJ','TT','UA','UK','WJ','WS','WN','WA','ZT','TI','IA']
                        }
                    'GS' : {
                            'responsible':'GS',
                            'supervise':['SM','DT','FE','IT','JD','MB','RA','SB','ST','TM','TH','TJ','TT','UA','UK','WJ','WS','WN','WA','ZT','TI','IA']
                        }
                     'VP' : {
                            'responsible':'VP',
                            'oversee':['SM','DT','FE','IT','JD','MB','RA','SB','ST','TM','TH','TJ','TT','UA','UK','WJ','WS','WN','WA','ZT','TI','IA']
                     }
                
                }\")
            ";
        $this->connection->executeQuery($sql);   
    }
}
