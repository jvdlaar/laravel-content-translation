<?php

namespace JvdLaar\ContentTranslation;

use Illuminate\Support\Facades\Facade;

class ContentTranslationFacade extends Facade {

  /**
   * Get the registered name of the component.
   */
  protected static function getFacadeAccessor() {
    return ContentTranslationManager::class;
  }

}
