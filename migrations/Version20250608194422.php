<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250608194422 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE mark_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE mark (id INT NOT NULL, user_mark_id INT NOT NULL, recipe_id INT NOT NULL, mark INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6674F2711A9DEF21 ON mark (user_mark_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6674F27159D8A214 ON mark (recipe_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN mark.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mark ADD CONSTRAINT FK_6674F2711A9DEF21 FOREIGN KEY (user_mark_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mark ADD CONSTRAINT FK_6674F27159D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            DROP SEQUENCE mark_id_seq CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mark DROP CONSTRAINT FK_6674F2711A9DEF21
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mark DROP CONSTRAINT FK_6674F27159D8A214
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE mark
        SQL);
    }
}
