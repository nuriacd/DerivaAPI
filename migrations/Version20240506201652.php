<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240506201652 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE order_product (order_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_2530ADE68D9F6D38 (order_id), INDEX IDX_2530ADE64584665A (product_id), PRIMARY KEY(order_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dish_ingredient (dish_id INT NOT NULL, ingredient_id INT NOT NULL, INDEX IDX_77196056148EB0CB (dish_id), INDEX IDX_77196056933FE08C (ingredient_id), PRIMARY KEY(dish_id, ingredient_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE restaurant_drink (restaurant_id INT NOT NULL, drink_id INT NOT NULL, INDEX IDX_47A3F300B1E7706E (restaurant_id), INDEX IDX_47A3F30036AA4BB4 (drink_id), PRIMARY KEY(restaurant_id, drink_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE restaurant_ingredient (restaurant_id INT NOT NULL, ingredient_id INT NOT NULL, INDEX IDX_CE955841B1E7706E (restaurant_id), INDEX IDX_CE955841933FE08C (ingredient_id), PRIMARY KEY(restaurant_id, ingredient_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE order_product ADD CONSTRAINT FK_2530ADE68D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_product ADD CONSTRAINT FK_2530ADE64584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dish_ingredient ADD CONSTRAINT FK_77196056148EB0CB FOREIGN KEY (dish_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dish_ingredient ADD CONSTRAINT FK_77196056933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE restaurant_drink ADD CONSTRAINT FK_47A3F300B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE restaurant_drink ADD CONSTRAINT FK_47A3F30036AA4BB4 FOREIGN KEY (drink_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE restaurant_ingredient ADD CONSTRAINT FK_CE955841B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE restaurant_ingredient ADD CONSTRAINT FK_CE955841933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_product DROP FOREIGN KEY FK_2530ADE68D9F6D38');
        $this->addSql('ALTER TABLE order_product DROP FOREIGN KEY FK_2530ADE64584665A');
        $this->addSql('ALTER TABLE dish_ingredient DROP FOREIGN KEY FK_77196056148EB0CB');
        $this->addSql('ALTER TABLE dish_ingredient DROP FOREIGN KEY FK_77196056933FE08C');
        $this->addSql('ALTER TABLE restaurant_drink DROP FOREIGN KEY FK_47A3F300B1E7706E');
        $this->addSql('ALTER TABLE restaurant_drink DROP FOREIGN KEY FK_47A3F30036AA4BB4');
        $this->addSql('ALTER TABLE restaurant_ingredient DROP FOREIGN KEY FK_CE955841B1E7706E');
        $this->addSql('ALTER TABLE restaurant_ingredient DROP FOREIGN KEY FK_CE955841933FE08C');
        $this->addSql('DROP TABLE order_product');
        $this->addSql('DROP TABLE dish_ingredient');
        $this->addSql('DROP TABLE restaurant_drink');
        $this->addSql('DROP TABLE restaurant_ingredient');
    }
}
