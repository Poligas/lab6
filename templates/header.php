<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle ?? SITE_TITLE) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="site-header">
    <div class="container">
        <h1><?= e(SITE_TITLE) ?></h1>
        <nav>
            <a href="index.php">Анкета</a>
            <a href="results.php">Відповіді</a>
        </nav>
    </div>
</header>
<main class="container">
