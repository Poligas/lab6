<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

// Читаємо всі збережені відповіді з папки survey
$files = is_dir(SURVEY_DIR) ? glob(SURVEY_DIR . '/*.json') : [];
rsort($files); // найновіші зверху

$responses = [];
foreach ($files as $file) {
    $json = json_decode(file_get_contents($file), true);
    if (is_array($json)) {
        $json['_file'] = basename($file);
        $responses[] = $json;
    }
}

$pageTitle = 'Відповіді — ' . SITE_TITLE;
include __DIR__ . '/templates/header.php';
?>

<section class="card">
    <h2>Отримані відповіді (<?= count($responses) ?>)</h2>

    <?php if (!$responses): ?>
        <p class="muted">Поки що ніхто не заповнив анкету.</p>
    <?php endif; ?>

    <?php foreach ($responses as $r): ?>
        <article class="response">
            <h3><?= e($r['name']) ?> <span class="muted">&lt;<?= e($r['email']) ?>&gt;</span></h3>
            <p class="muted"><?= e($r['submitted_at']) ?> · <?= e($r['_file']) ?></p>
            <dl>
                <?php foreach ($r['answers'] as $a): ?>
                    <dt><?= e($a['question']) ?></dt>
                    <dd><?= e(is_array($a['answer']) ? implode(', ', $a['answer']) : ($a['answer'] !== '' ? $a['answer'] : '—')) ?></dd>
                <?php endforeach; ?>
            </dl>
        </article>
    <?php endforeach; ?>
</section>

<?php include __DIR__ . '/templates/footer.php'; ?>
