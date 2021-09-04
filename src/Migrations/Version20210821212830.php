<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210821212830 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create orders';
    }

    public function up(Schema $schema): void
    {
        $sql = <<<SQL
        CREATE TABLE orders (
            id INT UNSIGNED AUTO_INCREMENT NOT NULL,
            product_id INT UNSIGNED NOT NULL,
            quantity INT UNSIGNED NOT NULL,
            PRIMARY KEY (id),
            FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE ON UPDATE CASCADE
        )
        SQL;

        $this->addSql($sql);
    }

    public function down(Schema $schema): void
    {
       $this->addSql('DROP TABLE orders');
    }
}
