<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220722071940 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE dummy_api (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, INDEX IDX_68D5F604A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dummy_api_endpoint (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', dummy_api_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', path VARCHAR(255) NOT NULL, allowed_methods LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', content_type VARCHAR(255) NOT NULL, INDEX IDX_9336A70FD5170DE4 (dummy_api_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE dummy_api ADD CONSTRAINT FK_68D5F604A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE dummy_api_endpoint ADD CONSTRAINT FK_9336A70FD5170DE4 FOREIGN KEY (dummy_api_id) REFERENCES dummy_api (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dummy_api_endpoint DROP FOREIGN KEY FK_9336A70FD5170DE4');
        $this->addSql('DROP TABLE dummy_api');
        $this->addSql('DROP TABLE dummy_api_endpoint');
    }
}
