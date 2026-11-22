<?php
// Допоміжні функції

/** Екранування виводу для захисту від XSS */
function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/** Довжина UTF-8 рядка (без залежності від розширення mbstring) */
function u_len(string $s): int
{
    return (int)preg_match_all('/./us', $s);
}

/** Обрізання UTF-8 рядка до $max символів */
function u_cut(string $s, int $max): string
{
    preg_match('/^.{0,' . $max . '}/us', $s, $m);
    return $m[0] ?? '';
}

/** Перевірка даних форми. Повертає [чисті_дані, помилки] */
function validate_survey(array $post, array $questions): array
{
    $errors = [];
    $data = [
        'name'  => trim($post['name'] ?? ''),
        'email' => trim($post['email'] ?? ''),
    ];

    if ($data['name'] === '') {
        $errors['name'] = 'Вкажіть ваше ім’я.';
    } elseif (u_len($data['name']) > 100) {
        $errors['name'] = 'Ім’я занадто довге.';
    }

    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Вкажіть коректний email.';
    }

    foreach ($questions as $key => $q) {
        $value = $post[$key] ?? ($q['type'] === 'checkbox' ? [] : '');

        if ($q['type'] === 'checkbox') {
            $value = array_values(array_intersect((array)$value, $q['options']));
            $empty = count($value) === 0;
        } elseif ($q['type'] === 'textarea') {
            $value = u_cut(trim((string)$value), 1000);
            $empty = $value === '';
        } else {
            $value = in_array($value, $q['options'], true) ? $value : '';
            $empty = $value === '';
        }

        if ($q['required'] && $empty) {
            $errors[$key] = 'Дайте відповідь на це питання.';
        }
        $data['answers'][$key] = $value;
    }

    return [$data, $errors];
}

/** Збереження відповіді у файл survey/<дата_час>_<id>.json */
function save_survey(array $data, array $questions, DateTimeInterface $time): string
{
    if (!is_dir(SURVEY_DIR)) {
        mkdir(SURVEY_DIR, 0775, true);
    }

    $record = [
        'submitted_at' => $time->format('Y-m-d H:i:s'),
        'name'         => $data['name'],
        'email'        => $data['email'],
        'answers'      => [],
    ];
    foreach ($questions as $key => $q) {
        $record['answers'][] = [
            'question' => $q['text'],
            'answer'   => $data['answers'][$key],
        ];
    }

    $filename = $time->format('Y-m-d_H-i-s') . '_' . bin2hex(random_bytes(3)) . '.json';
    file_put_contents(
        SURVEY_DIR . DIRECTORY_SEPARATOR . $filename,
        json_encode($record, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );

    return $filename;
}

/** Чи позначений варіант (для збереження введених значень при помилці) */
function is_checked(array $answers, string $key, string $option): bool
{
    $v = $answers[$key] ?? null;
    return is_array($v) ? in_array($option, $v, true) : $v === $option;
}
