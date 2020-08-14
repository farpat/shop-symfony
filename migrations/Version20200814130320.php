<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200814130320 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE address_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE image_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE module_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE module_parameter_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE order_item_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE orderable_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE product_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE product_field_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE product_reference_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE reset_password_request_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tag_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tax_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE visit_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE address (id INT NOT NULL, user_id INT NOT NULL, text TEXT NOT NULL, line1 TEXT NOT NULL, line2 TEXT DEFAULT NULL, postal_code VARCHAR(50) NOT NULL, city VARCHAR(100) NOT NULL, country VARCHAR(100) NOT NULL, country_code VARCHAR(10) NOT NULL, latitude NUMERIC(9, 6) NOT NULL, longitude NUMERIC(9, 6) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D4E6F81A76ED395 ON address (user_id)');
        $this->addSql('CREATE TABLE billing (id INT NOT NULL, user_id INT NOT NULL, number VARCHAR(191) NOT NULL, status VARCHAR(50) NOT NULL, address_text TEXT NOT NULL, address_line1 TEXT NOT NULL, address_line2 TEXT DEFAULT NULL, address_postal_code VARCHAR(50) NOT NULL, address_city VARCHAR(100) NOT NULL, address_country VARCHAR(100) NOT NULL, address_country_code VARCHAR(10) NOT NULL, address_latitude NUMERIC(9, 6) NOT NULL, address_longitude NUMERIC(9, 6) NOT NULL, user_email VARCHAR(180) NOT NULL, user_name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EC224CAA96901F54 ON billing (number)');
        $this->addSql('CREATE INDEX IDX_EC224CAAA76ED395 ON billing (user_id)');
        $this->addSql('CREATE INDEX number_index ON billing (number)');
        $this->addSql('CREATE TABLE cart (id INT NOT NULL, user_id INT NOT NULL, webhook_payment_id VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BA388B7A76ED395 ON cart (user_id)');
        $this->addSql('CREATE TABLE category (id INT NOT NULL, image_id INT DEFAULT NULL, label VARCHAR(255) NOT NULL, nomenclature TEXT NOT NULL, slug VARCHAR(255) NOT NULL, description TEXT NOT NULL, is_last BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_64C19C13DA5256D ON category (image_id)');
        $this->addSql('CREATE TABLE category_product_field (category_id INT NOT NULL, product_field_id INT NOT NULL, PRIMARY KEY(category_id, product_field_id))');
        $this->addSql('CREATE INDEX IDX_E4721B4F12469DE2 ON category_product_field (category_id)');
        $this->addSql('CREATE INDEX IDX_E4721B4F8F876D27 ON category_product_field (product_field_id)');
        $this->addSql('CREATE TABLE image (id INT NOT NULL, url VARCHAR(255) NOT NULL, alt VARCHAR(255) NOT NULL, url_thumbnail VARCHAR(255) DEFAULT NULL, alt_thumbnail VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE module (id INT NOT NULL, label VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, is_active BOOLEAN DEFAULT \'false\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE module_parameter (id INT NOT NULL, module_id INT NOT NULL, label TEXT NOT NULL, description TEXT DEFAULT NULL, value TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_70F68ED8AFC2B591 ON module_parameter (module_id)');
        $this->addSql('COMMENT ON COLUMN module_parameter.value IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE order_item (id INT NOT NULL, product_reference_id INT NOT NULL, orderable_id INT NOT NULL, quantity INT NOT NULL, amount_excluding_taxes NUMERIC(10, 2) NOT NULL, amount_including_taxes NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_52EA1F099BE1FCC2 ON order_item (product_reference_id)');
        $this->addSql('CREATE INDEX IDX_52EA1F096174077 ON order_item (orderable_id)');
        $this->addSql('CREATE TABLE orderable (id INT NOT NULL, delivery_address_id INT DEFAULT NULL, comment TEXT DEFAULT NULL, items_count INT DEFAULT 0 NOT NULL, total_amount_excluding_taxes NUMERIC(10, 2) NOT NULL, total_amount_including_taxes NUMERIC(10, 2) NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_132DB7D2EBF23851 ON orderable (delivery_address_id)');
        $this->addSql('CREATE TABLE product (id INT NOT NULL, main_image_id INT DEFAULT NULL, category_id INT DEFAULT NULL, label VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, excerpt TEXT DEFAULT NULL, description TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D34A04ADE4873418 ON product (main_image_id)');
        $this->addSql('CREATE INDEX IDX_D34A04AD12469DE2 ON product (category_id)');
        $this->addSql('CREATE TABLE product_tag (product_id INT NOT NULL, tag_id INT NOT NULL, PRIMARY KEY(product_id, tag_id))');
        $this->addSql('CREATE INDEX IDX_E3A6E39C4584665A ON product_tag (product_id)');
        $this->addSql('CREATE INDEX IDX_E3A6E39CBAD26311 ON product_tag (tag_id)');
        $this->addSql('CREATE TABLE product_tax (product_id INT NOT NULL, tax_id INT NOT NULL, PRIMARY KEY(product_id, tax_id))');
        $this->addSql('CREATE INDEX IDX_6EAEEE694584665A ON product_tax (product_id)');
        $this->addSql('CREATE INDEX IDX_6EAEEE69B2A824D8 ON product_tax (tax_id)');
        $this->addSql('CREATE TABLE product_field (id INT NOT NULL, type TEXT NOT NULL, label VARCHAR(255) NOT NULL, is_required BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE product_reference (id INT NOT NULL, product_id INT NOT NULL, main_image_id INT DEFAULT NULL, label VARCHAR(255) NOT NULL, unit_price_excluding_taxes NUMERIC(10, 2) NOT NULL, unit_price_including_taxes NUMERIC(10, 2) NOT NULL, filled_product_fields JSON NOT NULL, available_stock INT DEFAULT NULL, is_available BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C003FF9E4584665A ON product_reference (product_id)');
        $this->addSql('CREATE INDEX IDX_C003FF9EE4873418 ON product_reference (main_image_id)');
        $this->addSql('CREATE TABLE product_reference_image (product_reference_id INT NOT NULL, image_id INT NOT NULL, PRIMARY KEY(product_reference_id, image_id))');
        $this->addSql('CREATE INDEX IDX_211761D29BE1FCC2 ON product_reference_image (product_reference_id)');
        $this->addSql('CREATE INDEX IDX_211761D23DA5256D ON product_reference_image (image_id)');
        $this->addSql('CREATE TABLE reset_password_request (id INT NOT NULL, user_id INT DEFAULT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7CE748AA76ED395 ON reset_password_request (user_id)');
        $this->addSql('COMMENT ON COLUMN reset_password_request.requested_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN reset_password_request.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE tag (id INT NOT NULL, label VARCHAR(255) NOT NULL, type VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE tax (id INT NOT NULL, label VARCHAR(255) NOT NULL, type VARCHAR(50) NOT NULL, value NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, delivery_address_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, email_verified_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, remember_token VARCHAR(100) DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649EBF23851 ON "user" (delivery_address_id)');
        $this->addSql('CREATE TABLE visit (id INT NOT NULL, user_id INT DEFAULT NULL, ip_address VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, route VARCHAR(255) NOT NULL, route_parameters TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_437EE939A76ED395 ON visit (user_id)');
        $this->addSql('COMMENT ON COLUMN visit.route_parameters IS \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F81A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE billing ADD CONSTRAINT FK_EC224CAAA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE billing ADD CONSTRAINT FK_EC224CAABF396750 FOREIGN KEY (id) REFERENCES orderable (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7BF396750 FOREIGN KEY (id) REFERENCES orderable (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C13DA5256D FOREIGN KEY (image_id) REFERENCES image (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE category_product_field ADD CONSTRAINT FK_E4721B4F12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE category_product_field ADD CONSTRAINT FK_E4721B4F8F876D27 FOREIGN KEY (product_field_id) REFERENCES product_field (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE module_parameter ADD CONSTRAINT FK_70F68ED8AFC2B591 FOREIGN KEY (module_id) REFERENCES module (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F099BE1FCC2 FOREIGN KEY (product_reference_id) REFERENCES product_reference (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F096174077 FOREIGN KEY (orderable_id) REFERENCES orderable (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE orderable ADD CONSTRAINT FK_132DB7D2EBF23851 FOREIGN KEY (delivery_address_id) REFERENCES address (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADE4873418 FOREIGN KEY (main_image_id) REFERENCES image (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_tag ADD CONSTRAINT FK_E3A6E39C4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_tag ADD CONSTRAINT FK_E3A6E39CBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_tax ADD CONSTRAINT FK_6EAEEE694584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_tax ADD CONSTRAINT FK_6EAEEE69B2A824D8 FOREIGN KEY (tax_id) REFERENCES tax (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_reference ADD CONSTRAINT FK_C003FF9E4584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_reference ADD CONSTRAINT FK_C003FF9EE4873418 FOREIGN KEY (main_image_id) REFERENCES image (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_reference_image ADD CONSTRAINT FK_211761D29BE1FCC2 FOREIGN KEY (product_reference_id) REFERENCES product_reference (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_reference_image ADD CONSTRAINT FK_211761D23DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D649EBF23851 FOREIGN KEY (delivery_address_id) REFERENCES address (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE visit ADD CONSTRAINT FK_437EE939A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE orderable DROP CONSTRAINT FK_132DB7D2EBF23851');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D649EBF23851');
        $this->addSql('ALTER TABLE category_product_field DROP CONSTRAINT FK_E4721B4F12469DE2');
        $this->addSql('ALTER TABLE product DROP CONSTRAINT FK_D34A04AD12469DE2');
        $this->addSql('ALTER TABLE category DROP CONSTRAINT FK_64C19C13DA5256D');
        $this->addSql('ALTER TABLE product DROP CONSTRAINT FK_D34A04ADE4873418');
        $this->addSql('ALTER TABLE product_reference DROP CONSTRAINT FK_C003FF9EE4873418');
        $this->addSql('ALTER TABLE product_reference_image DROP CONSTRAINT FK_211761D23DA5256D');
        $this->addSql('ALTER TABLE module_parameter DROP CONSTRAINT FK_70F68ED8AFC2B591');
        $this->addSql('ALTER TABLE billing DROP CONSTRAINT FK_EC224CAABF396750');
        $this->addSql('ALTER TABLE cart DROP CONSTRAINT FK_BA388B7BF396750');
        $this->addSql('ALTER TABLE order_item DROP CONSTRAINT FK_52EA1F096174077');
        $this->addSql('ALTER TABLE product_tag DROP CONSTRAINT FK_E3A6E39C4584665A');
        $this->addSql('ALTER TABLE product_tax DROP CONSTRAINT FK_6EAEEE694584665A');
        $this->addSql('ALTER TABLE product_reference DROP CONSTRAINT FK_C003FF9E4584665A');
        $this->addSql('ALTER TABLE category_product_field DROP CONSTRAINT FK_E4721B4F8F876D27');
        $this->addSql('ALTER TABLE order_item DROP CONSTRAINT FK_52EA1F099BE1FCC2');
        $this->addSql('ALTER TABLE product_reference_image DROP CONSTRAINT FK_211761D29BE1FCC2');
        $this->addSql('ALTER TABLE product_tag DROP CONSTRAINT FK_E3A6E39CBAD26311');
        $this->addSql('ALTER TABLE product_tax DROP CONSTRAINT FK_6EAEEE69B2A824D8');
        $this->addSql('ALTER TABLE address DROP CONSTRAINT FK_D4E6F81A76ED395');
        $this->addSql('ALTER TABLE billing DROP CONSTRAINT FK_EC224CAAA76ED395');
        $this->addSql('ALTER TABLE cart DROP CONSTRAINT FK_BA388B7A76ED395');
        $this->addSql('ALTER TABLE reset_password_request DROP CONSTRAINT FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE visit DROP CONSTRAINT FK_437EE939A76ED395');
        $this->addSql('DROP SEQUENCE address_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE category_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE image_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE module_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE module_parameter_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE order_item_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE orderable_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE product_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE product_field_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE product_reference_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE reset_password_request_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tag_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tax_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE visit_id_seq CASCADE');
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
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE visit');
    }
}
