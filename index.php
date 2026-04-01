<?php
// #region agent log
$entry = [
    'sessionId' => '2db629',
    'runId' => 'initial',
    'hypothesisId' => 'H6',
    'location' => 'index.php:2',
    'message' => 'Root index fallback reached',
    'data' => [
        'request_uri' => $_SERVER['REQUEST_URI'] ?? '',
        'script_name' => $_SERVER['SCRIPT_NAME'] ?? '',
    ],
    'timestamp' => (int) round(microtime(true) * 1000),
];
@file_put_contents(__DIR__ . '/debug-2db629.log', json_encode($entry, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
// #endregion

require __DIR__ . '/public/index.php';
