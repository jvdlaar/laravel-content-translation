<?php

namespace JvdLaar\ContentTranslation;

class ContentTranslationManager {

  protected $translations = [];
  protected $using_fallback = FALSE;
  protected $bindings_ct_to_model = [];
  protected $bindings_model_to_ct = [];
  protected $ct_options = [];

  /**
   * Bind a content_type to a model class.
   */
  public function bind($content_type, $model_class, $options) {
    $this->bindings_ct_to_model[$content_type] = $model_class;
    $this->bindings_model_to_ct[$model_class] = $content_type;
    $this->ct_options[$content_type] = $options;
  }

  /**
   * Get a list of translatable types, translated.
   */
  public function getTypes() {
    $options = [];
    foreach ($this->bindings_ct_to_model as $type => $foo) {
      $options[$type] = trans("content_translation.$type.object_type");
    }

    natcasesort($options);
    return $options;
  }

  /**
   * Return the supported locales.
   */
  public function getSupportedLocales() {
    return ContentTranslation::getLocales();
  }

  /**
   * Return the locale ContentTranslation will use right now, if none was specifically provided.
   */
  public function getDefaultLocale() {
    return \App::getLocale();
  }

  /**
   * Get the model associated to a translation.
   */
  public function getContentModel($content_type, $content_id) {
    if (isset($this->bindings_ct_to_model[$content_type])) {
      $class_name = $this->bindings_ct_to_model[$content_type];
      return $class_name::findOrFailStatic($content_id);
    }
  }

  /**
   * Get the model class for a content type.
   */
  public function getModelClass($content_type) {
    return @$this->bindings_ct_to_model[$content_type];
  }

  /**
   * Get the content type associated to a model.
   */
  public function getContentType(TranslatableContract $translatable) {
    $class_name = get_class($translatable);
    if (isset($this->bindings_model_to_ct[$class_name])) {
      return $this->bindings_model_to_ct[$class_name];
    }

    $class = new \ReflectionClass($translatable);
    while ($parent = $class->getParentClass()) {
      $class_name = $parent->getName();
      if (isset($this->bindings_model_to_ct[$class_name])) {
        return $this->bindings_model_to_ct[$class_name];
      }
      $class = $parent;
    }
  }

  /**
   * Get the options for a content type.
   */
  public function getContentTypeOptions($content_type) {
    return $this->ct_options[$content_type];
  }

  /**
   * Get an options for a content type.
   */
  public function getContentTypeOption($content_type, $option) {
    return $this->ct_options[$content_type][$option];
  }

  /**
   * Get a translation from the database.
   */
  public function getTranslation($content_type, $content_id, $content_property, $locale = NULL) {
    if (!$locale) {
      $locale = $this->getDefaultLocale();
    }

    // Fetch from db.
    if (!isset($this->translations[$locale][$content_type][$content_id])) {
      $this->translations[$locale][$content_type][$content_id] = ContentTranslation::toBase()
        ->where('content_type', $content_type)
        ->where('content_id', $content_id)
        ->where('locale', $locale)
        ->pluck('translation', 'content_property');
    }

    $object_translations = $this->translations[$locale][$content_type][$content_id];
    return @$object_translations[$content_property];
  }

  /**
   * Get translations from the database grouped by locale.
   */
  public function getTranslationsGroupedByLocale($content_type, $content_id, $content_property) {
    return ContentTranslation::where('content_type', $content_type)
      ->where('content_id', $content_id)
      ->where('content_property', $content_property)
      ->get()
      ->keyBy('locale');
  }

  /**
   * Get translations from the database.
   */
  public function getTranslations($content_type, $ids, $property, $locale) {
    $query = ContentTranslation::where('content_type', $content_type);
    $query->whereIn('content_id', $ids);
    $query->where('content_property', $property);
    $query->where('locale', $locale);

    return $query->pluck('translation', 'content_id');
  }

  /**
   * Search through translations in the database.
   */
  public function searchTranslationsForContentIds($content_type, array $content_properties, $search) {
    $query = ContentTranslation::toBase();
    return $query
      ->where('content_type', $content_type)
      ->whereIn('content_property', $content_properties)
      ->where('translation', 'LIKE', '%' . $query->escapeLike($search) . '%')
      ->lists('content_translations.content_id');
  }

  /**
   * Save a single translation in a single locale.
   */
  public function saveTranslation($type, $id, $property, $locale, $translation) {
    $conditions = [
      'content_type' => $type,
      'content_property' => $property,
      'content_id' => $id,
      'locale' => $locale,
    ];

    if (trim($translation) == '') {
      return ContentTranslation::wheres($conditions)->delete();
    }

    return ContentTranslation::updateOrCreate($conditions, $conditions + [
      'translation' => $translation,
    ]);
  }

  /**
   * Count existing translations (properties) for this locale.
   */
  public function countTranslations($type, $id, $locale) {
    return ContentTranslation::wheres([
      'content_type' => $type,
      'content_id' => $id,
      'locale' => $locale,
    ])->count();
  }

  /**
   * Remember we're using a EN fallback on this page, instead of the preferred locale.
   */
  public function useFallback($type, $id, $property) {
    $this->using_fallback = TRUE;

    // @todo Do something useful with this information, like sending an e-mail or creating a log.
  }

  /**
   * Tell the caller IF we're using a EN fallback on this page.
   */
  public function isUsingFallback() {
    return $this->using_fallback;
  }

}
