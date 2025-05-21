<?php

namespace App\Helpers;

class TranslatorHelper
{
  public static function translate(string $text, string $targetLang = 'hi'): ?string
  {
    $url = 'https://translate.googleapis.com/translate_a/single';
    $params = http_build_query([
      'client' => 'gtx',
      'sl' => 'auto',
      'tl' => $targetLang,
      'dt' => 't',
      'q' => $text,
    ]);

    $response = @file_get_contents("{$url}?{$params}");

    if (!$response) return null;

    $json = json_decode($response, true);

    return $json[0][0][0] ?? null;
  }
}
