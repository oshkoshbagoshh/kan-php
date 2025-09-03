<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $this->escape($title) ?>
    </title>
    <link href="<?= $this->asset('css/app.css') ?>" rel="stylesheet">
</head>

<body>
    <div class="container">
        <header>
            <h1>
                <?= $this->escape($title) ?>
            </h1>
            <nav>
                <a href="<?= $this->url('/') ?>">Home</a>
                <a href="<?= $this->url('/about') ?>">About</a>
            </nav>
        </header>

        <main>
            <p>
                <?= $this->escape($message) ?>
            </p>

            <?php if (!empty($users)): ?>
            <h2>Users</h2>
            <ul>
                <?php foreach ($users as $user): ?>
                <li>
                    <a href="<?= $this->url('/users/' . $user['id']) ?>">
                        <?= $this->escape($user['username']) ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </main>
    </div>

    <script src="<?= $this->asset('js/app.js') ?>"></script>
</body>

</html>