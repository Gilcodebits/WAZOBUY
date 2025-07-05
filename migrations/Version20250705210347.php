<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250705210347 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE utilisateur_favoris (utilisateur_id INT NOT NULL, produit_id INT NOT NULL, INDEX IDX_F33833A0FB88E14F (utilisateur_id), INDEX IDX_F33833A0F347EFB (produit_id), PRIMARY KEY(utilisateur_id, produit_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur_favoris ADD CONSTRAINT FK_F33833A0FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs (id_utilisateur)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur_favoris ADD CONSTRAINT FK_F33833A0F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE produit DROP en_promotion, CHANGE promotion promotion TINYINT(1) NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur_favoris DROP FOREIGN KEY FK_F33833A0FB88E14F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur_favoris DROP FOREIGN KEY FK_F33833A0F347EFB
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE utilisateur_favoris
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE produit ADD en_promotion TINYINT(1) NOT NULL, CHANGE promotion promotion TINYINT(1) DEFAULT 0 NOT NULL
        SQL);
    }
}
