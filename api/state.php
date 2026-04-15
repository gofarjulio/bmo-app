<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$allowedMethods = ['GET', 'POST'];
if (!in_array($_SERVER['REQUEST_METHOD'], $allowedMethods, true)) {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

$rootDir = dirname(__DIR__);
$dataDir = $rootDir . DIRECTORY_SEPARATOR . 'data';
$stateFile = $dataDir . DIRECTORY_SEPARATOR . 'state.json';

$defaultState = [
    'dbLine' => [],
    'lineProcessData' => new stdClass(),
    'dbPCS' => [],
    'orders' => new stdClass(),
    'capacity' => new stdClass(),
    'workCalendar' => new stdClass(),
    'mpParams' => new stdClass(),
    'pendingUpload' => null,
    'pendingPeriod' => null,
];

if (!is_dir($dataDir)) {
    mkdir($dataDir, 0775, true);
}

if (!file_exists($stateFile)) {
    file_put_contents(
        $stateFile,
        json_encode(['state' => $defaultState], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        LOCK_EX
    );
}

function jsonResponse(array $payload, int $statusCode = 200): void
{
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function isListArrayCompat(array $arr): bool
{
    if (function_exists('array_is_list')) {
        return array_is_list($arr);
    }
    $i = 0;
    foreach ($arr as $k => $_) {
        if ($k !== $i++) return false;
    }
    return true;
}

function normalizeStateTypes(array $state, array $defaultState): array
{
    $state = array_merge($defaultState, $state);

    $listKeys = ['dbLine', 'dbPCS'];
    foreach ($listKeys as $k) {
        if (!isset($state[$k]) || !is_array($state[$k]) || !isListArrayCompat($state[$k])) {
            $state[$k] = [];
        }
    }

    $mapKeys = ['lineProcessData', 'orders', 'capacity', 'workCalendar', 'mpParams'];
    foreach ($mapKeys as $k) {
        $v = $state[$k] ?? null;
        if (!is_array($v)) {
            $state[$k] = new stdClass();
            continue;
        }
        // Empty or list array means invalid shape for map/object keys.
        if (count($v) === 0 || isListArrayCompat($v)) {
            $state[$k] = new stdClass();
        }
    }

    return $state;
}

function readStateFile(string $stateFile, array $defaultState): array
{
    $raw = @file_get_contents($stateFile);
    if ($raw === false || trim($raw) === '') {
        return normalizeStateTypes($defaultState, $defaultState);
    }

    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        return normalizeStateTypes($defaultState, $defaultState);
    }

    $state = $decoded['state'] ?? $decoded;
    if (!is_array($state)) {
        return normalizeStateTypes($defaultState, $defaultState);
    }

    return normalizeStateTypes($state, $defaultState);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $state = readStateFile($stateFile, $defaultState);
    jsonResponse(['ok' => true, 'state' => $state]);
}

$rawInput = file_get_contents('php://input');
if ($rawInput === false || trim($rawInput) === '') {
    jsonResponse(['ok' => false, 'error' => 'Request body kosong'], 400);
}

$payload = json_decode($rawInput, true);
if (!is_array($payload) || !isset($payload['state']) || !is_array($payload['state'])) {
    jsonResponse(['ok' => false, 'error' => 'Format payload tidak valid'], 400);
}

$nextState = normalizeStateTypes($payload['state'], $defaultState);
$writeData = [
    'updatedAt' => gmdate('c'),
    'state' => $nextState,
];

$ok = @file_put_contents(
    $stateFile,
    json_encode($writeData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
    LOCK_EX
);

if ($ok === false) {
    jsonResponse(['ok' => false, 'error' => 'Gagal menulis file state'], 500);
}

jsonResponse(['ok' => true, 'savedAt' => gmdate('c')]);
