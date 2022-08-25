<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220809123548 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE dummy_api_header (id INT AUTO_INCREMENT NOT NULL, dummy_api_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', dummy_api_endpoint_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, value VARCHAR(255) DEFAULT NULL, INDEX IDX_1F42BE90D5170DE4 (dummy_api_id), INDEX IDX_1F42BE9043E13D29 (dummy_api_endpoint_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE dummy_api_header ADD CONSTRAINT FK_1F42BE90D5170DE4 FOREIGN KEY (dummy_api_id) REFERENCES dummy_api (id)');
        $this->addSql('ALTER TABLE dummy_api_header ADD CONSTRAINT FK_1F42BE9043E13D29 FOREIGN KEY (dummy_api_endpoint_id) REFERENCES dummy_api_endpoint (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE dummy_api_header');
    }
}
