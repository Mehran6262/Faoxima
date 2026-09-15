<?php
header('Content-Type: text/plain; charset=utf-8');

$files = [
  'eval_trace.log',
  'bootstrap_trace.log',
];

foreach ($files as $f) {
  $p = __DIR__ . '/' . $f;
  echo "===== $f =====\n";
  if (!file_exists($p)) {
    echo "NOT_FOUND: $p\n\n";
    continue;
  }
  echo file_get_contents($p) . "\n\n";
}
