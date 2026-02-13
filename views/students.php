<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System</title>
    <style>
        :root { font-family: Inter, Segoe UI, Arial, sans-serif; }
        body { background:#f7f8fa; margin:0; color:#1b1e24; }
        .container { max-width: 900px; margin: 3rem auto; padding: 0 1rem; }
        .card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:1.25rem; box-shadow:0 6px 24px rgba(0,0,0,.05); }
        h1 { margin-top:0; }
        form { display:grid; gap:.75rem; }
        .row { display:grid; gap:.75rem; grid-template-columns:1fr 1fr auto; }
        input { padding:.65rem .75rem; border:1px solid #d1d5db; border-radius:8px; font-size:.95rem; }
        button { border:0; border-radius:8px; padding:.68rem .9rem; cursor:pointer; font-weight:600; }
        .btn-primary { background:#2563eb; color:#fff; }
        .btn-danger { background:#dc2626; color:#fff; }
        table { width:100%; border-collapse:collapse; margin-top:1rem; }
        th,td { text-align:left; border-bottom:1px solid #e5e7eb; padding:.75rem .4rem; }
        .alert { margin-bottom:1rem; padding:.75rem .85rem; border-radius:8px; }
        .alert.success { background:#dcfce7; color:#166534; }
        .alert.error { background:#fee2e2; color:#991b1b; }
        .muted { color:#6b7280; font-size:.9rem; }
        @media (max-width:700px){
            .row { grid-template-columns:1fr; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h1>Student Management System</h1>
        <p class="muted">Add, list, and delete students. Data is saved in a MySQL database.</p>

        <?php if (!empty($flash)): ?>
            <div class="alert <?= htmlspecialchars((string) $flash['type']) ?>">
                <?= htmlspecialchars((string) $flash['message']) ?>
            </div>
        <?php endif; ?>

        <form method="post" action="/">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
            <input type="hidden" name="action" value="add">
            <div class="row">
                <input type="text" name="name" placeholder="Student Name" required>
                <input type="email" name="email" placeholder="Student Email" required>
                <button class="btn-primary" type="submit">Add Student</button>
            </div>
        </form>

        <table>
            <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Created</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($students)): ?>
                <tr>
                    <td colspan="4" class="muted">No students yet.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?= htmlspecialchars((string) ($student['name'] ?? '')) ?></td>
                        <td><?= htmlspecialchars((string) ($student['email'] ?? '')) ?></td>
                        <td><?= htmlspecialchars((string) ($student['created_at'] ?? '')) ?></td>
                        <td>
                            <form method="post" action="/" onsubmit="return confirm('Delete this student?');">
                                <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= htmlspecialchars((string) ($student['id'] ?? '')) ?>">
                                <button class="btn-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
