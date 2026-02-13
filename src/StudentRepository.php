<?php

declare(strict_types=1);

namespace App;

final class StudentRepository
{
    public function __construct(private readonly string $storagePath)
    {
        $this->ensureStorageExists();
    }

    /** @return array<int, array{id:string,name:string,email:string,created_at:string}> */
    public function all(): array
    {
        $content = file_get_contents($this->storagePath);
        if ($content === false || trim($content) === '') {
            return [];
        }

        $data = json_decode($content, true);
        if (!is_array($data)) {
            return [];
        }

        return array_values(array_filter($data, fn ($row) => is_array($row)));
    }

    public function add(string $name, string $email): void
    {
        $students = $this->all();

        $students[] = [
            'id' => bin2hex(random_bytes(8)),
            'name' => $name,
            'email' => $email,
            'created_at' => (new \DateTimeImmutable())->format(DATE_ATOM),
        ];

        $this->write($students);
    }

    public function delete(string $id): bool
    {
        $students = $this->all();
        $filtered = array_values(array_filter(
            $students,
            static fn (array $student): bool => ($student['id'] ?? '') !== $id
        ));

        if (count($filtered) === count($students)) {
            return false;
        }

        $this->write($filtered);

        return true;
    }

    private function ensureStorageExists(): void
    {
        $dir = dirname($this->storagePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        if (!file_exists($this->storagePath)) {
            file_put_contents($this->storagePath, json_encode([], JSON_PRETTY_PRINT));
        }
    }

    /** @param array<int, array{id:string,name:string,email:string,created_at:string}> $students */
    private function write(array $students): void
    {
        file_put_contents(
            $this->storagePath,
            json_encode($students, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            LOCK_EX
        );
    }
}
