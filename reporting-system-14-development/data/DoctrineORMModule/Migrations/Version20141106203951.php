<?php

namespace DoctrineORMModule\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141106203951 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        // Some how diff always picks ENUM columns as different
        //this is already as per DB version 2101104212952
        //$this->addSql('ALTER TABLE branches CHANGE status status ENUM(\'active\', \'disabled\'), CHANGE branch_type branch_type ENUM(\'Markaz\', \'Jama`at\',\'Region\',\'Halqa\')');
        $this->addSql('ALTER TABLE departments ADD status ENUM(\'active\', \'disabled\')');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        // Some how diff always picks ENUM columns as different
        //$this->addSql('ALTER TABLE branches CHANGE status status VARCHAR(255) DEFAULT NULL, CHANGE branch_type branch_type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE departments DROP status');
    }
	
	 public function postUp(Schema $schema){
        $sql="update departments set status='active'";
		$this->connection->executeQuery($sql);    
	 }
}
