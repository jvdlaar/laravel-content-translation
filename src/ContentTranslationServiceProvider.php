<?php

namespace JvdLaar\ContentTranslation;

use Illuminate\Support\ServiceProvider;

class ContentTranslationServiceProvider extends ServiceProvider {

  protected $defer = TRUE;

  /**
   * Bootstrap the application events.
   */
  public function boot() {
    $this->publishes([
      __DIR__ . '/config/content-translation.php' => config_path('content-translation.php'),
    ]);
    $this->publishes([
      __DIR__ . '/database/migrations/' => database_path('/migrations')
    ], 'migrations');
  }

  /**
   * Register the application services.
   */
  public function register() {
    $this->app->singleton(ContentTranslationManager::class, function ($app) {

      $content_translation_manager = new ContentTranslationManager();

      $content_translation_config = config('content-translation.models');
      foreach ($content_translation_config as $key => $model_content_translation) {
        $content_translation_manager->bind($key, $model_content_translation['class'], $model_content_translation);
      }

      return $content_translation_manager;
    });
  }

  /**
   * Get the services provided by the provider.
   */
  public function provides() {
    return [ContentTranslationManager::class];
  }

}
