<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250526121408 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE active_spell (id INT AUTO_INCREMENT NOT NULL, active_card_id INT DEFAULT NULL, spell_id INT DEFAULT NULL, current_cooldown INT NOT NULL, INDEX IDX_56027FCBC980263E (active_card_id), INDEX IDX_56027FCB479EC90D (spell_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE active_spell ADD CONSTRAINT FK_56027FCBC980263E FOREIGN KEY (active_card_id) REFERENCES active_cards (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE active_spell ADD CONSTRAINT FK_56027FCB479EC90D FOREIGN KEY (spell_id) REFERENCES spells (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE active_cards_spells DROP FOREIGN KEY FK_697D725A26D920A8
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE active_cards_spells DROP FOREIGN KEY FK_697D725A99875F51
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE active_cards_spells
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE spells ADD cooldown INT NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE active_cards_spells (active_cards_id INT NOT NULL, spells_id INT NOT NULL, INDEX IDX_697D725A26D920A8 (active_cards_id), INDEX IDX_697D725A99875F51 (spells_id), PRIMARY KEY(active_cards_id, spells_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE active_cards_spells ADD CONSTRAINT FK_697D725A26D920A8 FOREIGN KEY (active_cards_id) REFERENCES active_cards (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE active_cards_spells ADD CONSTRAINT FK_697D725A99875F51 FOREIGN KEY (spells_id) REFERENCES spells (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE active_spell DROP FOREIGN KEY FK_56027FCBC980263E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE active_spell DROP FOREIGN KEY FK_56027FCB479EC90D
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE active_spell
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE spells DROP cooldown
        SQL);
    }
}
