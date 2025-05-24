<?php

return [
  'paths' => ['api/*'],
  'allowed_methods' => ['*'],
  'allowed_origins' => ['*'],  // In production, replace with your React app's domain
  'allowed_origins_patterns' => [],
  'allowed_headers' => ['*'],
  'exposed_headers' => [],
  'max_age' => 0,
  'supports_credentials' => false,
];
