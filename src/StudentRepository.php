<?php

declare(strict_types=1);

namespace App;

use PDO;

final class StudentRepository
{
    public function __construct(private readonly PDO $pdo)
    {
        $this->ensureTableExists();
    }

    /** @return array<int, array{id:string,name:string,email:string,created_at:string}> */
    public function all(): array
    {
        $statement = $this->pdo->query(
            'SELECT id, name, email, DATE_FORMAT(created_at, "%Y-%m-%d %H:%i:%s") AS created_at FROM students ORDER BY created_at DESC'
        );

        /** @var array<int, array{id:string,name:string,email:string,created_at:string}> $rows */
        $rows = $statement->fetchAll();

        return $rows;
    }

    public function add(string $name, string $email): void
    {
        $id = bin2hex(random_bytes(8));

        $statement = $this->pdo->prepare(
            'INSERT INTO students (id, name, email, created_at) VALUES (:id, :name, :email, NOW())'
        );

        $statement->execute([
            ':id' => $id,
            ':name' => $name,
            ':email' => $email,
        ]);
    }

    public function delete(string $id): bool
    {
        $statement = $this->pdo->prepare('DELETE FROM students WHERE id = :id');
        $statement->execute([':id' => $id]);

        return $statement->rowCount() > 0;
    }

    private function ensureTableExists(): void
    {
        $this->pdo->exec(
            'CREATE TABLE IF NOT EXISTS students (
                id VARCHAR(16) PRIMARY KEY,
                name VARCHAR(150) NOT NULL,
                email VARCHAR(190) NOT NULL UNIQUE,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
        );
    }
}
