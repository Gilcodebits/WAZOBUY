<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250630031018 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, emoji VARCHAR(10) DEFAULT NULL, UNIQUE INDEX UNIQ_497DD634989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message ADD CONSTRAINT FK_B6BD307F10335F61 FOREIGN KEY (expediteur_id) REFERENCES utilisateurs (id_utilisateur)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message ADD CONSTRAINT FK_B6BD307FA4F84F6E FOREIGN KEY (destinataire_id) REFERENCES utilisateurs (id_utilisateur)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B6BD307F10335F61 ON message (expediteur_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B6BD307FA4F84F6E ON message (destinataire_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ADD destinataire_id INT NOT NULL, ADD auteur_id INT DEFAULT NULL, DROP destinataire, DROP auteur, CHANGE est_lu est_lue TINYINT(1) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA4F84F6E FOREIGN KEY (destinataire_id) REFERENCES utilisateurs (id_utilisateur)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA60BB6FE6 FOREIGN KEY (auteur_id) REFERENCES utilisateurs (id_utilisateur)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_BF5476CAA4F84F6E ON notification (destinataire_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_BF5476CA60BB6FE6 ON notification (auteur_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE categorie
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F10335F61
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FA4F84F6E
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_B6BD307F10335F61 ON message
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_B6BD307FA4F84F6E ON message
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAA4F84F6E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA60BB6FE6
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_BF5476CAA4F84F6E ON notification
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_BF5476CA60BB6FE6 ON notification
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ADD destinataire VARCHAR(255) NOT NULL, ADD auteur VARCHAR(255) DEFAULT NULL, DROP destinataire_id, DROP auteur_id, CHANGE est_lue est_lu TINYINT(1) NOT NULL
        SQL);
    }
}
