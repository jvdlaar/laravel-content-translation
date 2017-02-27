<?php

namespace JvdLaar\ContentTranslation;

trait HasTranslatables {

  /**
   * Returns the content type used for content translations.
   */
  static public function getTranslationContentType() {
    return \ContentTranslation::getContentType(new static);
  }

  /**
   * Returns the content properties used for content translations.
   */
  static public function getTranslationContentProperties() {
    return \ContentTranslation::getContentTypeOption(static::getTranslationContentType(), 'properties');
  }

  /**
   * Returns the content property that provides the translated label for a model.
   */
  static public function getTranslationContentLabelProperty() {
    return \ContentTranslation::getContentTypeOption(static::getTranslationContentType(), 'label_property');
  }

  /**
   * Do a normal translate (with fallback), and return an ugly default if still empty.
   */
  public function translateWithDefault($property, $locale = NULL, $fallback = TRUE) {
    return $this->translate($property, $locale, $fallback) ?: $this->getTranslationDefault($property);
  }

  /**
   * Translate a property for this object, including EN fallback.
   */
  public function translate($property, $locale = NULL, $fallback = TRUE) {
    $content_type = $this->getTranslationContentType();
    $content_id = $this->getKey();

    // Preferred translation from provided locale.
    if ($translation = \ContentTranslation::getTranslation($content_type, $content_id, $property, $locale)) {
      return $translation;
    }

    // Fallback translation from EN.
    if ($locale != 'en' && $fallback) {
      if ($translation = \ContentTranslation::getTranslation($content_type, $content_id, $property, 'en')) {
        // If EN does have a translation, tell ContentTranslation we're using a fallback, so it can notify the user.
        \ContentTranslation::useFallback($content_type, $content_id, $property);

        return $translation;
      }
    }

    return '';
  }

  /**
   * Translate the label property for this object, including EN fallback.
   */
  public function translateLabel($locale = NULL) {
    return $this->translate($this->getTranslationContentLabelProperty(), $locale);
  }

  /**
   * Display a translated property.
   */
  public function displayTranslation($property, $with_default = FALSE, $locale = NULL, $fallback = TRUE) {
    $properties = $this->getTranslationContentProperties();
    $translation = '';
    if ($with_default) {
      $translation = $this->translateWithDefault($property, $locale, $fallback);
    }
    else {
      $translation = $this->translate($property, $locale, $fallback);
    }

    if (!empty($properties[$property]['nl2br'])) {
      $translation = nl2br($translation);
    }
    return $translation;
  }

  /**
   * Return an ugly default for a property in this content.
   */
  protected function getTranslationDefault($property) {
    $content_type = $this->getTranslationContentType();
    $content_id = $this->getKey();

    return $content_type . ' ' . $property . ' # ' . $content_id;
  }

  /**
   * Returns all locales and whether there is a translation for this locales.
   */
  public function getTranslationLocales() {
    $locales = \LaravelLocalization::getSupportedLocales();

    $content_type = $this->getTranslationContentType();
    $content_id = $this->getKey();
    $content_label_property = $this->getTranslationContentLabelProperty();
    $translations = \ContentTranslation::getTranslationsGroupedByLocale($content_type, $content_id, $content_label_property);

    foreach ($locales as $locale_name => $val) {
      $locales[$locale_name]['translated'] = isset($translations[$locale_name]);
      $locales[$locale_name]['content_translation'] = @$translations[$locale_name];
    }

    asort($locales);

    return $locales;
  }

  /**
   * Save property translation for this specific Translatable.
   */
  public function saveTranslations($locale, array $translations) {
    $content_type = $this->getTranslationContentType();
    $content_id = $this->getKey();

    foreach ($translations as $property => $translation) {
      \ContentTranslation::saveTranslation($content_type, $content_id, $property, $locale, $translation);
    }
  }

  /**
   * Count existing translations (properties) for this locale.
   */
  public function countTranslations($locale) {
    $content_type = $this->getTranslationContentType();
    $content_id = $this->getKey();

    return \ContentTranslation::countTranslations($content_type, $content_id, $locale);
  }

  /**
   * STATICS
   */

  /**
   * Display a translated property.
   */
  static public function getTranslations(array $content_ids, $property, $locale = NULL) {
    $content_type = \ContentTranslation::getContentType(new static);
    $content_translations = \ContentTranslation::getTranslations($content_type, $content_ids, $property, $locale);

    return $content_translations->map('nl2br');
  }

}
