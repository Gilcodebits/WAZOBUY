<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250627012053 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, expediteur VARCHAR(255) NOT NULL, destinataire VARCHAR(255) NOT NULL, contenu LONGTEXT NOT NULL, est_lu TINYINT(1) NOT NULL, date_envoi DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', conversation_id VARCHAR(100) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, destinataire VARCHAR(255) NOT NULL, est_lu TINYINT(1) NOT NULL, date_creation DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', lien VARCHAR(255) DEFAULT NULL, type VARCHAR(50) DEFAULT NULL, auteur VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE parametre_general ADD notifications_email TINYINT(1) NOT NULL, ADD notif_commandes TINYINT(1) NOT NULL, ADD notif_litiges TINYINT(1) NOT NULL, ADD notif_vendeurs TINYINT(1) NOT NULL, ADD notif_messages TINYINT(1) NOT NULL, ADD notif_rapport VARCHAR(20) NOT NULL, ADD animations TINYINT(1) NOT NULL, ADD deux_facteurs TINYINT(1) NOT NULL, ADD notif_connexion TINYINT(1) NOT NULL, ADD session_exp INT NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE message
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE notification
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE parametre_general DROP notifications_email, DROP notif_commandes, DROP notif_litiges, DROP notif_vendeurs, DROP notif_messages, DROP notif_rapport, DROP animations, DROP deux_facteurs, DROP notif_connexion, DROP session_exp
        SQL);
    }
}
