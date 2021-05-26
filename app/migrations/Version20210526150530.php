<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210526150530 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE followers (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, follower_id INT NOT NULL, created_date DATETIME NOT NULL, INDEX IDX_8408FDA7A76ED395 (user_id), INDEX IDX_8408FDA7AC24F853 (follower_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE followers ADD CONSTRAINT FK_8408FDA7A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE followers ADD CONSTRAINT FK_8408FDA7AC24F853 FOREIGN KEY (follower_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE followers');
    }
}
