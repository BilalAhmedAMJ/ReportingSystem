<?php

namespace DoctrineORMModule\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150408083329 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE email_messages');
        $this->addSql("CREATE TABLE `messages` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `requested_by_user_id` int(11) NOT NULL,
                      `sent_as` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
                      `status` enum('DRAFT','SENT','UNSENT') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'DRAFT',
                      `message_type` enum('internal','email') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'internal',
                      `subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                      `text_body` longtext COLLATE utf8_unicode_ci NOT NULL,
                      `html_body` longtext COLLATE utf8_unicode_ci NOT NULL,
                      `date_modified` datetime NOT NULL,
                      `date_sent` datetime DEFAULT NULL,
                      PRIMARY KEY (`id`),
                      KEY `IDX_D06401DFA2DD2669` (`requested_by_user_id`),
                      CONSTRAINT `FK_D06401DFA2DD2669` FOREIGN KEY (`requested_by_user_id`) REFERENCES `users` (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
        $this->addSql('CREATE TABLE `user_messages` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `user_id` int(11) DEFAULT NULL,
                      `message_id` int(11) DEFAULT NULL,
                      `date_read` datetime DEFAULT NULL,
                      `date_deleted` datetime DEFAULT NULL,
                      PRIMARY KEY (`id`),
                      KEY `FK_D06401DFA2DD39871` (`user_id`),
                      KEY `FK_D06401DFA2DD39872` (`message_id`),
                      CONSTRAINT `FK_D06401DFA2DD39871` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
                      CONSTRAINT `FK_D06401DFA2DD39872` FOREIGN KEY (`message_id`) REFERENCES `messages` (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;');
        
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user_messages');                
        $this->addSql('DROP TABLE messages');
        $this->addSql('CREATE TABLE `email_messages` (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          `requested_by_user_id` int(11) NOT NULL,
                          `sent_to` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                          `sent_from` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                          `subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                          `text_body` longtext COLLATE utf8_unicode_ci NOT NULL,
                          `html_body` longtext COLLATE utf8_unicode_ci NOT NULL,
                          PRIMARY KEY (`id`),
                          KEY `IDX_D06401DFA2DD2669` (`requested_by_user_id`),
                          CONSTRAINT `FK_D06401DFA2DD2669` FOREIGN KEY (`requested_by_user_id`) REFERENCES `users` (`id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;');
        
        
    }
}
