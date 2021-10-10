<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211010220806 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pictures_category (pictures_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_74333341BC415685 (pictures_id), INDEX IDX_7433334112469DE2 (category_id), PRIMARY KEY(pictures_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pictures_category ADD CONSTRAINT FK_74333341BC415685 FOREIGN KEY (pictures_id) REFERENCES pictures (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pictures_category ADD CONSTRAINT FK_7433334112469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pictures ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE pictures ADD CONSTRAINT FK_8F7C2FC0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_8F7C2FC0A76ED395 ON pictures (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE pictures_category');
        $this->addSql('ALTER TABLE pictures DROP FOREIGN KEY FK_8F7C2FC0A76ED395');
        $this->addSql('DROP INDEX IDX_8F7C2FC0A76ED395 ON pictures');
        $this->addSql('ALTER TABLE pictures DROP user_id');
    }
}
