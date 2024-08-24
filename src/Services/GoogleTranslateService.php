<?php

namespace Filament\Core\Services;

use Google\Cloud\Translate\V2\TranslateClient;

class GoogleTranslateService
{
  protected $translate;

  public function __construct()
  {
    $this->translate = new TranslateClient([
      'key' => config('filament-core.google_cloud_api_key')
    ]);
  }

  public function translate($text, $targetLanguage = 'en')
  {
    if(is_string($text)) {
      $result = $this->translate->translate($text, [
        'target' => $targetLanguage
      ]);

      return $result['text'];
    }

    return $text;
  }
}