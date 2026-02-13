<?php

declare(strict_types=1);

namespace App;

final class StudentController
{
    public function __construct(private readonly StudentRepository $repository)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public function handleRequest(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePost();
            return;
        }

        $this->render();
    }

    private function handlePost(): void
    {
        $action = $_POST['action'] ?? '';

        if (!$this->isValidCsrf($_POST['csrf'] ?? null)) {
            $this->flash('error', 'Invalid request token. Please retry.');
            $this->redirect();
        }

        if ($action === 'add') {
            $name = trim((string) ($_POST['name'] ?? ''));
            $email = trim((string) ($_POST['email'] ?? ''));

            if ($name === '' || $email === '') {
                $this->flash('error', 'Name and email are required.');
                $this->redirect();
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->flash('error', 'Please provide a valid email address.');
                $this->redirect();
            }

            $this->repository->add($name, $email);
            $this->flash('success', 'Student added successfully.');
            $this->redirect();
        }

        if ($action === 'delete') {
            $id = (string) ($_POST['id'] ?? '');
            $deleted = $this->repository->delete($id);
            $this->flash($deleted ? 'success' : 'error', $deleted ? 'Student deleted.' : 'Student not found.');
            $this->redirect();
        }

        $this->flash('error', 'Unknown action.');
        $this->redirect();
    }

    private function render(): void
    {
        $students = $this->repository->all();
        $csrf = $this->csrfToken();
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        require __DIR__ . '/../views/students.php';
    }

    private function redirect(): never
    {
        header('Location: /');
        exit;
    }

    private function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    private function csrfToken(): string
    {
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(16));
        }

        return (string) $_SESSION['csrf'];
    }

    private function isValidCsrf(?string $token): bool
    {
        return is_string($token)
            && !empty($_SESSION['csrf'])
            && hash_equals((string) $_SESSION['csrf'], $token);
    }
}
