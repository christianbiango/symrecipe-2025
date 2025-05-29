<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250529142459 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE ingredient ADD user_ingredient_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE ingredient ADD CONSTRAINT FK_6BAF78707DB14DDC FOREIGN KEY (user_ingredient_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6BAF78707DB14DDC ON ingredient (user_ingredient_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE ingredient DROP CONSTRAINT FK_6BAF78707DB14DDC
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_6BAF78707DB14DDC
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE ingredient DROP user_ingredient_id
        SQL);
    }
}
