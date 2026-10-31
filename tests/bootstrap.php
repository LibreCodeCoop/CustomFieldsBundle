<?php
require __DIR__ . '/../vendor/autoload.php';
spl_autoload_register(static function (string $class): void {
    $prefix = 'KimaiPlugin\\CustomFieldsBundle\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $source = __DIR__ . '/../' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    if (is_file($source)) {
        require $source;
    }
});
