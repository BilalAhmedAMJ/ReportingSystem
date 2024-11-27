<?php

namespace DoctrineORMModule\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150102184748 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE email_messages (id INT AUTO_INCREMENT NOT NULL, requested_by_user_id INT NOT NULL, sent_to VARCHAR(255) NOT NULL, sent_from VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, text_body LONGTEXT NOT NULL, html_body LONGTEXT NOT NULL, INDEX IDX_D06401DFA2DD2669 (requested_by_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_tokens (id INT AUTO_INCREMENT NOT NULL, issued_user_id INT NOT NULL, token VARCHAR(255) NOT NULL, action VARCHAR(255) NOT NULL, expiry DATETIME NOT NULL, INDEX IDX_CF080AB3A8EA464C (issued_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE email_messages ADD CONSTRAINT FK_D06401DFA2DD2669 FOREIGN KEY (requested_by_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE user_tokens ADD CONSTRAINT FK_CF080AB3A8EA464C FOREIGN KEY (issued_user_id) REFERENCES users (id)');

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE email_messages');
        $this->addSql('DROP TABLE user_tokens');
    }
}
