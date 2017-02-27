<?php

namespace JvdLaar\ContentTranslation;

interface TranslatableContract {

  /**
   * Do a normal translate (with fallback), and return an ugly default if still empty.
   */
  public function translateWithDefault($property, $locale = NULL);

  /**
   * Translate a property for this object, including EN fallback.
   */
  public function translate($property, $locale = NULL);

  /**
   * Translate the label property for this object, including EN fallback.
   */
  public function translateLabel($locale = NULL);

  /**
   * Save property translation for this specific Translatable.
   */
  public function saveTranslations($locale, array $translations);

  /**
   * Count existing translations (properties) for this locale.
   */
  public function countTranslations($locale);

  /**
   * Returns the content type used for content translations.
   */
  static public function getTranslationContentType();

  /**
   * Returns the content properties used for content translations.
   */
  static public function getTranslationContentProperties();

  /**
   * Returns the content property that provides the translated label for a model.
   */
  static public function getTranslationContentLabelProperty();

  /**
   * Returns all locales and whether there is a translation for this locales.
   */
  public function getTranslationLocales();

}
