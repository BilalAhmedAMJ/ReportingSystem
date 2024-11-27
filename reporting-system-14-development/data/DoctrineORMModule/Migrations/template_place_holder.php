<?php


use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class plaec_holder
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('select database()');
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('select database()');
    }
    
    
 public function postUp(Schema $schema)
    {
        /*Add reports config for Local level */
        $sql=" select * from users";
        $this->connection->executeQuery($sql);         
    }

 public function preUp(Schema $schema)
    {
        /*Add reports config for Local level */
        $sql=" select * from users";
        $this->connection->executeQuery($sql);         
    }
}
