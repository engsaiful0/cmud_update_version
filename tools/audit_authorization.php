<?php
// CLI-only inventory. The generated candidate rules must be reviewed before use.
if (PHP_SAPI !== 'cli') { exit(1); }
$root = dirname(__DIR__);
$rules = array();
$missing = array();
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/application/controllers', FilesystemIterator::SKIP_DOTS));
foreach ($files as $entry) {
    if ($entry->getExtension() !== 'php' || strpos($entry->getFilename(), ' ') !== false) { continue; }
    $file = $entry->getPathname();
    $source = file_get_contents($file);
    if (!preg_match('/class\s+(\w+)\s+extends\s+Admin_Controller/', $source, $class)) { continue; }
    $directory = str_replace('\\', '/', substr(dirname($file), strlen($root . '/application/controllers')));
    $controller = trim(strtolower($directory . '/' . $class[1]), '/');
    $tokens = token_get_all($source);
    $methods = array();
    for ($i = 0, $length = count($tokens); $i < $length; $i++) {
        if (!is_array($tokens[$i]) || $tokens[$i][0] !== T_FUNCTION) { continue; }
        do { $i++; } while (is_array($tokens[$i]) && $tokens[$i][0] === T_WHITESPACE);
        if (!is_array($tokens[$i]) || $tokens[$i][0] !== T_STRING) { continue; }
        $method = strtolower($tokens[$i][1]);
        while ($i < $length && $tokens[$i] !== '{') { $i++; }
        $depth = 1; $body = '';
        while (++$i < $length && $depth > 0) {
            $token = $tokens[$i];
            if ($token === '{' || (is_array($token) && in_array($token[0], array(T_CURLY_OPEN, T_DOLLAR_OPEN_CURLY_BRACES)))) { $depth++; }
            if ($token === '}') { $depth--; }
            if (is_array($token) && in_array($token[0], array(T_COMMENT, T_DOC_COMMENT))) { continue; }
            $body .= is_array($token) ? $token[1] : $token;
        }
        preg_match_all('/get_permission\(\s*[\'\"]([^\'\"]+)[\'\"]\s*,\s*[\'\"](is_\w+)[\'\"]\s*\)/', $body, $matches, PREG_SET_ORDER);
        $permissions = array();
        foreach ($matches as $match) { $permissions[$match[1] . ':' . $match[2]] = array($match[1], $match[2]); }
        $methods[$method] = array_values($permissions);
    }
    foreach ($methods as $method => $permissions) {
        if ($method === '__construct') { continue; }
        $permissions = $permissions ?: ($methods['__construct'] ?? array());
        if ($permissions) { $rules[$controller . '/' . $method] = $permissions; }
        else { $missing[] = $controller . '/' . $method; }
    }
}
ksort($rules); sort($missing);
$output = "<?php\ndefined('BASEPATH') or exit('No direct script access allowed');\n\n// Existing route checks; controllers retain their action-specific checks.\n\$config['authorization_routes'] = array(\n";
foreach ($rules as $route => $permissions) {
    $items = array();
    foreach ($permissions as $pair) { $items[] = 'array(' . var_export($pair[0], true) . ', ' . var_export($pair[1], true) . ')'; }
    $output .= '    ' . var_export($route, true) . ' => array(' . implode(', ', $items) . "),\n";
}
file_put_contents($root . '/application/config/authorization_routes.php', $output . ");\n");
file_put_contents($root . '/tools/authorization_unmapped.txt', implode("\n", $missing) . "\n");
echo count($rules) . ' guarded routes, ' . count($missing) . " routes needing explicit policy.\n";
