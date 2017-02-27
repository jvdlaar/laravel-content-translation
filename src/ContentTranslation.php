<?php

namespace JvdLaar\ContentTranslation;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Http\Request;

class ContentTranslation extends Model {

  protected $table = 'content_translations';
  protected $fillable = ['content_type', 'content_id', 'content_property', 'locale', 'translation'];
  protected $dates = ['created_at', 'updated_at'];
  public $timestamps = FALSE;
  public $users = FALSE;

  /**
   * Returns the available locales.
   */
  static public function getLocales() {
    $locales = \LaravelLocalization::getSupportedLocales();
    $locales = array_map(function($locale) {
      return $locale['name'];
    }, $locales);

    natcasesort($locales);

    return $locales;
  }

}
