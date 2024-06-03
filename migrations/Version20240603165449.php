<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240603165449 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE restaurant_drink DROP FOREIGN KEY FK_47A3F30036AA4BB4');
        $this->addSql('ALTER TABLE restaurant_drink DROP FOREIGN KEY FK_47A3F300B1E7706E');
        $this->addSql('ALTER TABLE restaurant_ingredient DROP FOREIGN KEY FK_CE955841933FE08C');
        $this->addSql('ALTER TABLE restaurant_ingredient DROP FOREIGN KEY FK_CE955841B1E7706E');
        $this->addSql('DROP TABLE restaurant_drink');
        $this->addSql('DROP TABLE restaurant_ingredient');
        $this->addSql('ALTER TABLE restaurant ADD delivery_city VARCHAR(255) NOT NULL, DROP address, DROP latitude, DROP longitude, DROP radius');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE restaurant_drink (restaurant_id INT NOT NULL, drink_id INT NOT NULL, INDEX IDX_47A3F30036AA4BB4 (drink_id), INDEX IDX_47A3F300B1E7706E (restaurant_id), PRIMARY KEY(restaurant_id, drink_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE restaurant_ingredient (restaurant_id INT NOT NULL, ingredient_id INT NOT NULL, INDEX IDX_CE955841933FE08C (ingredient_id), INDEX IDX_CE955841B1E7706E (restaurant_id), PRIMARY KEY(restaurant_id, ingredient_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE restaurant_drink ADD CONSTRAINT FK_47A3F30036AA4BB4 FOREIGN KEY (drink_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE restaurant_drink ADD CONSTRAINT FK_47A3F300B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE restaurant_ingredient ADD CONSTRAINT FK_CE955841933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE restaurant_ingredient ADD CONSTRAINT FK_CE955841B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE restaurant ADD latitude VARCHAR(255) NOT NULL, ADD longitude VARCHAR(255) NOT NULL, ADD radius INT NOT NULL, CHANGE delivery_city address VARCHAR(255) NOT NULL');
    }
}
