<?php

namespace DoctrineORMModule\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141212171052 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE documents (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, modified_by_id INT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, file_name VARCHAR(255) NOT NULL, expiry_date DATETIME NOT NULL, document_ext VARCHAR(255) NOT NULL, access_rules VARCHAR(255) NOT NULL, document_type VARCHAR(255) NOT NULL, document_date DATETIME NOT NULL, date_created DATETIME NOT NULL, date_modified DATETIME NOT NULL, INDEX IDX_A2B07288B03A8386 (created_by_id), INDEX IDX_A2B0728899049ECE (modified_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE documents ADD CONSTRAINT FK_A2B07288B03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE documents ADD CONSTRAINT FK_A2B0728899049ECE FOREIGN KEY (modified_by_id) REFERENCES users (id)');

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('DROP TABLE documents');
    }

    public function postUp(Schema $schema)
    {
        /*Add reports config for Local level */
        $sql="replace into config values 
            ('document_rules',\"[
                                {id:'all_users',label:'All Users',download_roles:['user']},
                                {id:'nma_users',label:'NMA Users',download_roles:['national-secretary','national-general-secretary','national_amir']},
                                {id:'president_gs_users',label:'Presidents/General Secretares',download_roles:['president','local_amir','general_secretary']},
                                {id:'president_gs_nma_users',label:'Presidents/General Secretares and NMA Users',download_roles:['president','local_amir','general_secretary','national-secretary','national-general-secretary','national_amir']},
                               ]\")
        ";
        $this->connection->executeQuery($sql);         
    }


}
