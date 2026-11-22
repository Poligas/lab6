<?php
// require_once — без конфігурації та функцій сайт працювати не може,
// тому при відсутності файлу краще зупинити виконання (Fatal error).
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

$errors    = [];
$data      = ['name' => '', 'email' => '', 'answers' => []];
$submitted = null; // DateTime успішного надсилання

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$data, $errors] = validate_survey($_POST, $questions);

    if (!$errors) {
        $submitted = new DateTime();
        $savedFile = save_survey($data, $questions, $submitted);
    }
}

$pageTitle = 'Анкета — ' . SITE_TITLE;
// include — шаблон шапки; якщо його не буде, сторінка все одно виконається (лише Warning).
include __DIR__ . '/templates/header.php';
?>

<?php if ($submitted): ?>
    <section class="card success">
        <h2>Дякуємо, <?= e($data['name']) ?>!</h2>
        <p>Вашу відповідь збережено.</p>
        <p class="time">
            Дата та час заповнення форми:
            <strong><?= $submitted->format('d.m.Y') ?></strong>
            о <strong><?= $submitted->format('H:i:s') ?></strong>
        </p>
        <p class="muted">Файл: survey/<?= e($savedFile) ?></p>
        <a class="btn" href="index.php">Заповнити ще раз</a>
    </section>
<?php else: ?>
    <section class="card">
        <p class="intro">
            Будь ласка, дайте відповідь на кілька питань про те, як ви користуєтеся
            гаджетами та інтернетом. Опитування займе 1–2 хвилини.
        </p>

        <?php if ($errors): ?>
            <div class="alert">Будь ласка, виправте помилки у формі.</div>
        <?php endif; ?>

        <form method="POST" action="index.php" novalidate>
            <div class="field">
                <label for="name">Ваше ім’я *</label>
                <input type="text" id="name" name="name" maxlength="100"
                       value="<?= e($data['name']) ?>" required>
                <?php if (isset($errors['name'])): ?><span class="error"><?= e($errors['name']) ?></span><?php endif; ?>
            </div>

            <div class="field">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email"
                       value="<?= e($data['email']) ?>" required>
                <?php if (isset($errors['email'])): ?><span class="error"><?= e($errors['email']) ?></span><?php endif; ?>
            </div>

            <?php $n = 1; foreach ($questions as $key => $q): ?>
                <fieldset class="field">
                    <legend><?= $n++ ?>. <?= e($q['text']) ?><?= $q['required'] ? ' *' : '' ?></legend>

                    <?php if ($q['type'] === 'radio' || $q['type'] === 'checkbox'): ?>
                        <?php foreach ($q['options'] as $opt): ?>
                            <label class="option">
                                <input type="<?= $q['type'] ?>"
                                       name="<?= $key . ($q['type'] === 'checkbox' ? '[]' : '') ?>"
                                       value="<?= e($opt) ?>"
                                       <?= is_checked($data['answers'], $key, $opt) ? 'checked' : '' ?>>
                                <?= e($opt) ?>
                            </label>
                        <?php endforeach; ?>

                    <?php elseif ($q['type'] === 'select'): ?>
                        <select name="<?= $key ?>">
                            <option value="">— оберіть —</option>
                            <?php foreach ($q['options'] as $opt): ?>
                                <option value="<?= e($opt) ?>" <?= is_checked($data['answers'], $key, $opt) ? 'selected' : '' ?>>
                                    <?= e($opt) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                    <?php else: ?>
                        <textarea name="<?= $key ?>" rows="4" maxlength="1000"><?= e($data['answers'][$key] ?? '') ?></textarea>
                    <?php endif; ?>

                    <?php if (isset($errors[$key])): ?><span class="error"><?= e($errors[$key]) ?></span><?php endif; ?>
                </fieldset>
            <?php endforeach; ?>

            <button type="submit" class="btn">Надіслати відповідь</button>
        </form>
    </section>
<?php endif; ?>

<?php include __DIR__ . '/templates/footer.php'; ?>
