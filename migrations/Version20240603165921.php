<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240603165921 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE restaurant_drink (id INT AUTO_INCREMENT NOT NULL, restaurant_id_id INT NOT NULL, drink_id_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_47A3F30035592D86 (restaurant_id_id), INDEX IDX_47A3F30081984FF2 (drink_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE restaurant_ingredient (id INT AUTO_INCREMENT NOT NULL, restaurant_id_id INT NOT NULL, ingredient_id_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_CE95584135592D86 (restaurant_id_id), INDEX IDX_CE9558416676F996 (ingredient_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE restaurant_drink ADD CONSTRAINT FK_47A3F30035592D86 FOREIGN KEY (restaurant_id_id) REFERENCES restaurant (id)');
        $this->addSql('ALTER TABLE restaurant_drink ADD CONSTRAINT FK_47A3F30081984FF2 FOREIGN KEY (drink_id_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE restaurant_ingredient ADD CONSTRAINT FK_CE95584135592D86 FOREIGN KEY (restaurant_id_id) REFERENCES restaurant (id)');
        $this->addSql('ALTER TABLE restaurant_ingredient ADD CONSTRAINT FK_CE9558416676F996 FOREIGN KEY (ingredient_id_id) REFERENCES product (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE restaurant_drink DROP FOREIGN KEY FK_47A3F30035592D86');
        $this->addSql('ALTER TABLE restaurant_drink DROP FOREIGN KEY FK_47A3F30081984FF2');
        $this->addSql('ALTER TABLE restaurant_ingredient DROP FOREIGN KEY FK_CE95584135592D86');
        $this->addSql('ALTER TABLE restaurant_ingredient DROP FOREIGN KEY FK_CE9558416676F996');
        $this->addSql('DROP TABLE restaurant_drink');
        $this->addSql('DROP TABLE restaurant_ingredient');
    }
}
