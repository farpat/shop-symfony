<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201230171545 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE address (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, text LONGTEXT NOT NULL, line1 LONGTEXT NOT NULL, line2 LONGTEXT DEFAULT NULL, postal_code VARCHAR(50) NOT NULL, city VARCHAR(100) NOT NULL, country VARCHAR(100) NOT NULL, country_code VARCHAR(10) NOT NULL, latitude NUMERIC(9, 6) NOT NULL, longitude NUMERIC(9, 6) NOT NULL, INDEX IDX_D4E6F81A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE billing (id INT NOT NULL, user_id INT NOT NULL, number VARCHAR(191) NOT NULL, status VARCHAR(50) NOT NULL, address_text LONGTEXT NOT NULL, address_line1 LONGTEXT NOT NULL, address_line2 LONGTEXT DEFAULT NULL, address_postal_code VARCHAR(50) NOT NULL, address_city VARCHAR(100) NOT NULL, address_country VARCHAR(100) NOT NULL, address_country_code VARCHAR(10) NOT NULL, address_latitude NUMERIC(9, 6) NOT NULL, address_longitude NUMERIC(9, 6) NOT NULL, user_email VARCHAR(180) NOT NULL, user_name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_EC224CAA96901F54 (number), INDEX IDX_EC224CAAA76ED395 (user_id), INDEX number_index (number), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cart (id INT NOT NULL, user_id INT NOT NULL, webhook_payment_id VARCHAR(255) DEFAULT NULL, INDEX IDX_BA388B7A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, label VARCHAR(255) NOT NULL, nomenclature LONGTEXT NOT NULL, slug VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, INDEX IDX_64C19C13DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category_product_field (category_id INT NOT NULL, product_field_id INT NOT NULL, INDEX IDX_E4721B4F12469DE2 (category_id), INDEX IDX_E4721B4F8F876D27 (product_field_id), PRIMARY KEY(category_id, product_field_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, alt VARCHAR(255) NOT NULL, url_thumbnail VARCHAR(255) DEFAULT NULL, alt_thumbnail VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE module (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, is_active TINYINT(1) DEFAULT \'0\' NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE module_parameter (id INT AUTO_INCREMENT NOT NULL, module_id INT NOT NULL, label LONGTEXT NOT NULL, description LONGTEXT DEFAULT NULL, value LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_70F68ED8AFC2B591 (module_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_item (id INT AUTO_INCREMENT NOT NULL, product_reference_id INT NOT NULL, orderable_id INT NOT NULL, quantity INT NOT NULL, amount_excluding_taxes NUMERIC(10, 2) NOT NULL, amount_including_taxes NUMERIC(10, 2) NOT NULL, INDEX IDX_52EA1F099BE1FCC2 (product_reference_id), INDEX IDX_52EA1F096174077 (orderable_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE orderable (id INT AUTO_INCREMENT NOT NULL, delivery_address_id INT DEFAULT NULL, comment LONGTEXT DEFAULT NULL, items_count INT DEFAULT 0 NOT NULL, total_amount_excluding_taxes NUMERIC(10, 2) NOT NULL, total_amount_including_taxes NUMERIC(10, 2) NOT NULL, updated_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_132DB7D2EBF23851 (delivery_address_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, main_image_id INT DEFAULT NULL, category_id INT DEFAULT NULL, label VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, excerpt LONGTEXT DEFAULT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_D34A04ADE4873418 (main_image_id), INDEX IDX_D34A04AD12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_tag (product_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_E3A6E39C4584665A (product_id), INDEX IDX_E3A6E39CBAD26311 (tag_id), PRIMARY KEY(product_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_tax (product_id INT NOT NULL, tax_id INT NOT NULL, INDEX IDX_6EAEEE694584665A (product_id), INDEX IDX_6EAEEE69B2A824D8 (tax_id), PRIMARY KEY(product_id, tax_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_field (id INT AUTO_INCREMENT NOT NULL, type LONGTEXT NOT NULL, label VARCHAR(255) NOT NULL, is_required TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_reference (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, main_image_id INT DEFAULT NULL, label VARCHAR(255) NOT NULL, unit_price_excluding_taxes NUMERIC(10, 2) NOT NULL, unit_price_including_taxes NUMERIC(10, 2) NOT NULL, filled_product_fields LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', available_stock INT DEFAULT NULL, is_available TINYINT(1) NOT NULL, INDEX IDX_C003FF9E4584665A (product_id), INDEX IDX_C003FF9EE4873418 (main_image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_reference_image (product_reference_id INT NOT NULL, image_id INT NOT NULL, INDEX IDX_211761D29BE1FCC2 (product_reference_id), INDEX IDX_211761D23DA5256D (image_id), PRIMARY KEY(product_reference_id, image_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, type VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tax (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, type VARCHAR(50) NOT NULL, value NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, delivery_address_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', password VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, email_verified_at DATETIME DEFAULT NULL, remember_token VARCHAR(100) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649EBF23851 (delivery_address_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE visit (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, ip_address VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, route VARCHAR(255) NOT NULL, route_parameters LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME NOT NULL, INDEX IDX_437EE939A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F81A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE billing ADD CONSTRAINT FK_EC224CAAA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE billing ADD CONSTRAINT FK_EC224CAABF396750 FOREIGN KEY (id) REFERENCES orderable (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7BF396750 FOREIGN KEY (id) REFERENCES orderable (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C13DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE category_product_field ADD CONSTRAINT FK_E4721B4F12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_product_field ADD CONSTRAINT FK_E4721B4F8F876D27 FOREIGN KEY (product_field_id) REFERENCES product_field (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE module_parameter ADD CONSTRAINT FK_70F68ED8AFC2B591 FOREIGN KEY (module_id) REFERENCES module (id)');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F099BE1FCC2 FOREIGN KEY (product_reference_id) REFERENCES product_reference (id)');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F096174077 FOREIGN KEY (orderable_id) REFERENCES orderable (id)');
        $this->addSql('ALTER TABLE orderable ADD CONSTRAINT FK_132DB7D2EBF23851 FOREIGN KEY (delivery_address_id) REFERENCES address (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADE4873418 FOREIGN KEY (main_image_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE product_tag ADD CONSTRAINT FK_E3A6E39C4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_tag ADD CONSTRAINT FK_E3A6E39CBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_tax ADD CONSTRAINT FK_6EAEEE694584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_tax ADD CONSTRAINT FK_6EAEEE69B2A824D8 FOREIGN KEY (tax_id) REFERENCES tax (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_reference ADD CONSTRAINT FK_C003FF9E4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product_reference ADD CONSTRAINT FK_C003FF9EE4873418 FOREIGN KEY (main_image_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE product_reference_image ADD CONSTRAINT FK_211761D29BE1FCC2 FOREIGN KEY (product_reference_id) REFERENCES product_reference (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_reference_image ADD CONSTRAINT FK_211761D23DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649EBF23851 FOREIGN KEY (delivery_address_id) REFERENCES address (id)');
        $this->addSql('ALTER TABLE visit ADD CONSTRAINT FK_437EE939A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE orderable DROP FOREIGN KEY FK_132DB7D2EBF23851');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649EBF23851');
        $this->addSql('ALTER TABLE category_product_field DROP FOREIGN KEY FK_E4721B4F12469DE2');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD12469DE2');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C13DA5256D');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADE4873418');
        $this->addSql('ALTER TABLE product_reference DROP FOREIGN KEY FK_C003FF9EE4873418');
        $this->addSql('ALTER TABLE product_reference_image DROP FOREIGN KEY FK_211761D23DA5256D');
        $this->addSql('ALTER TABLE module_parameter DROP FOREIGN KEY FK_70F68ED8AFC2B591');
        $this->addSql('ALTER TABLE billing DROP FOREIGN KEY FK_EC224CAABF396750');
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B7BF396750');
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F096174077');
        $this->addSql('ALTER TABLE product_tag DROP FOREIGN KEY FK_E3A6E39C4584665A');
        $this->addSql('ALTER TABLE product_tax DROP FOREIGN KEY FK_6EAEEE694584665A');
        $this->addSql('ALTER TABLE product_reference DROP FOREIGN KEY FK_C003FF9E4584665A');
        $this->addSql('ALTER TABLE category_product_field DROP FOREIGN KEY FK_E4721B4F8F876D27');
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F099BE1FCC2');
        $this->addSql('ALTER TABLE product_reference_image DROP FOREIGN KEY FK_211761D29BE1FCC2');
        $this->addSql('ALTER TABLE product_tag DROP FOREIGN KEY FK_E3A6E39CBAD26311');
        $this->addSql('ALTER TABLE product_tax DROP FOREIGN KEY FK_6EAEEE69B2A824D8');
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F81A76ED395');
        $this->addSql('ALTER TABLE billing DROP FOREIGN KEY FK_EC224CAAA76ED395');
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B7A76ED395');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE visit DROP FOREIGN KEY FK_437EE939A76ED395');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE billing');
        $this->addSql('DROP TABLE cart');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE category_product_field');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE module');
        $this->addSql('DROP TABLE module_parameter');
        $this->addSql('DROP TABLE order_item');
        $this->addSql('DROP TABLE orderable');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_tag');
        $this->addSql('DROP TABLE product_tax');
        $this->addSql('DROP TABLE product_field');
        $this->addSql('DROP TABLE product_reference');
        $this->addSql('DROP TABLE product_reference_image');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE tax');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE visit');
    }
}
