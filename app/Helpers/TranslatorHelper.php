<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\App;

class TranslatorHelper
{
  /**
   * Simple translation for text without HTML
   */
  public static function translate(string $text, string $targetLang = 'hi'): ?string
  {
    try {
      if (empty(trim($text))) {
        return '';
      }

      // First try with Google Translate API
      try {
        $response = Http::withoutVerifying()
          ->withOptions([
            'verify' => false,
            'curl' => [
              CURLOPT_SSL_VERIFYPEER => false,
              CURLOPT_SSL_VERIFYHOST => false,
              CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
            ]
          ])
          ->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Accept' => '*/*',
            'Accept-Language' => 'en-US,en;q=0.9',
            'Origin' => 'https://translate.google.com',
            'Referer' => 'https://translate.google.com/',
          ])
          ->get('https://translate.googleapis.com/translate_a/single', [
            'client' => 'gtx',
            'sl' => 'en',
            'tl' => $targetLang,
            'dt' => 't',
            'q' => $text,
          ]);

        if ($response->successful()) {
          $result = $response->json();
          if (isset($result[0]) && is_array($result[0])) {
            $translated = '';
            foreach ($result[0] as $segment) {
              if (isset($segment[0])) {
                $translated .= $segment[0];
              }
            }
            return $translated ?: null;
          }
        }
      } catch (\Exception $e) {
        Log::error('Primary translation failed, trying fallback...', [
          'error' => $e->getMessage()
        ]);
      }

      // Fallback to alternative endpoint
      try {
        $response = Http::withoutVerifying()
          ->withOptions([
            'verify' => false,
            'curl' => [
              CURLOPT_SSL_VERIFYPEER => false,
              CURLOPT_SSL_VERIFYHOST => false,
              CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
            ]
          ])
          ->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Accept' => '*/*',
            'Accept-Language' => 'en-US,en;q=0.9',
          ])
          ->get('https://clients5.google.com/translate_a/t', [
            'client' => 'dict-chrome-ex',
            'sl' => 'en',
            'tl' => $targetLang,
            'q' => $text,
          ]);

        if ($response->successful()) {
          $result = $response->json();
          if (isset($result[0])) {
            return $result[0];
          }
        }
      } catch (\Exception $e) {
        Log::error('Fallback translation failed, trying last resort...', [
          'error' => $e->getMessage()
        ]);
      }

      // Last resort - try direct translation API
      try {
        $response = Http::withoutVerifying()
          ->withOptions([
            'verify' => false,
            'curl' => [
              CURLOPT_SSL_VERIFYPEER => false,
              CURLOPT_SSL_VERIFYHOST => false,
              CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
            ]
          ])
          ->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Accept' => '*/*',
          ])
          ->get('https://translate.google.com/translate_a/t', [
            'client' => 'webapp',
            'sl' => 'en',
            'tl' => $targetLang,
            'hl' => 'en',
            'dt' => ['at', 'bd', 'ex', 'ld', 'md', 'qca', 'rw', 'rm', 'ss', 't'],
            'ie' => 'UTF-8',
            'oe' => 'UTF-8',
            'source' => 'bh',
            'ssel' => '0',
            'tsel' => '0',
            'kc' => '1',
            'q' => $text,
          ]);

        if ($response->successful()) {
          $result = $response->json();
          if (isset($result[0])) {
            return $result[0];
          }
        }
      } catch (\Exception $e) {
        Log::error('All translation attempts failed', [
          'error' => $e->getMessage()
        ]);
      }

      return null;
    } catch (\Exception $e) {
      Log::error('Translation error: ' . $e->getMessage(), [
        'text' => $text,
        'error' => $e->getMessage()
      ]);
      return null;
    }
  }

  /**
   * Specialized method for translating editor content
   */
  public static function translateEditor(string $content, string $targetLang = 'hi'): ?string
  {
    try {
      if (empty(trim($content))) {
        return '';
      }

      // Clean the content first
      $content = preg_replace('/<\?xml[^>]+\?>/', '', $content);
      $content = preg_replace('/<!DOCTYPE[^>]+>/', '', $content);

      // Extract text content while preserving HTML structure
      $dom = new \DOMDocument();
      $dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

      $xpath = new \DOMXPath($dom);
      $textNodes = $xpath->query('//text()');

      // Store original text and their positions
      $translations = [];
      foreach ($textNodes as $node) {
        $text = trim($node->nodeValue);
        if (!empty($text)) {
          $translated = self::translate($text, $targetLang);
          if ($translated !== null) {
            $translations[$text] = $translated;
          }
        }
      }

      // Replace original text with translations
      foreach ($translations as $original => $translated) {
        $content = str_replace($original, $translated, $content);
      }

      // Clean up any artifacts
      $content = str_replace(['<html>', '</html>', '<body>', '</body>'], '', $content);
      $content = preg_replace('/\s+/', ' ', $content);
      $content = trim($content);

      return $content;
    } catch (\Exception $e) {
      Log::error('Editor translation error: ' . $e->getMessage(), [
        'content' => $content,
        'error' => $e->getMessage()
      ]);
      return null;
    }
  }

  /**
   * Translate rich editor content while preserving HTML
   */
  public static function translateRichContent(string $content, string $targetLang = 'hi'): ?string
  {
    try {
      if (empty(trim($content))) {
        return '';
      }

      // Store markers for HTML and special characters
      $markers = [];
      $counter = 0;

      // Preserve emojis
      $content = preg_replace_callback('/([🕉💧🎉📍])/', function ($m) use (&$markers, &$counter) {
        $marker = "___EMOJI_{$counter}___";
        $markers[$marker] = $m[1];
        $counter++;
        return " $marker ";
      }, $content);

      // Preserve HTML tags
      $content = preg_replace_callback('/<[^>]+>/', function ($m) use (&$markers, &$counter) {
        $marker = "___HTML_{$counter}___";
        $markers[$marker] = $m[0];
        $counter++;
        return " $marker ";
      }, $content);

      // Split content into smaller chunks to avoid length limits
      $chunks = str_split($content, 500); // Reduced chunk size for better reliability
      $translatedParts = [];

      foreach ($chunks as $chunk) {
        $translated = self::translate($chunk, $targetLang);
        if ($translated !== null) {
          $translatedParts[] = $translated;
        } else {
          // If translation fails, keep original chunk
          $translatedParts[] = $chunk;
        }

        // Add small delay between chunks
        usleep(200000); // 200ms delay
      }

      // Combine translated parts
      $translatedContent = implode('', $translatedParts);

      // Restore markers
      foreach ($markers as $marker => $original) {
        $translatedContent = str_replace(" $marker ", $original, $translatedContent);
        $translatedContent = str_replace($marker, $original, $translatedContent);
      }

      // Clean up extra spaces
      $translatedContent = preg_replace('/\s+/', ' ', $translatedContent);
      $translatedContent = str_replace(' .', '.', $translatedContent);
      $translatedContent = str_replace(' ,', ',', $translatedContent);

      return $translatedContent;
    } catch (\Exception $e) {
      Log::error('Rich content translation error: ' . $e->getMessage(), [
        'content' => $content,
        'error' => $e->getMessage()
      ]);
      return null;
    }
  }
}
