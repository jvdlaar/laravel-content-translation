# Laravel content translation

This package makes properties of your models translatable. For example when you have a Country model then you can make
the country name translatable.

## Todo
- Add user interface to add translations.

## Installation

This package can be installed through Composer.

``` bash
composer require jvdlaar/laravel-content-translation
```
You must install this service provider.

```php
// config/app.php
'providers' => [
    ...
    JvdLaar\ContentTranslation\ContentTranslationServiceProvider::class,
    ...
];
```

This package also comes with a facade, which provides an easy way to call the the class.

```php
// config/app.php
'aliases' => [
    ...
    'ContentTranslation' => JvdLaar\ContentTranslation\ContentTranslationFacade::class,
    ...
];
```

You can publish the config file of this package with this command:

``` bash
php artisan vendor:publish --provider="JvdLaar\ContentTranslation\ContentTranslationServiceProvider"
```

The following config file will be published in `config/content-translation.php`

```php

return [

  'country' => [
    'class' => \App\Models\Country::class,
    'label_property' => 'name',
    'properties' => [
      'name' => ['required' => TRUE],
      'nationality' => ['required' => TRUE],
    ],
  ],

  'page' => [
    'class' => \App\Models\Test::class,
    'label_property' => 'title',
    'properties' => [
      'title' => ['required' => TRUE],
      'body' => ['nl2br' => TRUE],
    ],
  ],
];

```

The array key is they key with which the translations are stored in the database, "class" refers to the model class.
"label_property" is used to determine the translatable label of this model. E.g. the country name in the country model.
"properties" is an array with the translatable properties and whether they are required and their output needs to be
nl2br.

## Usage

After installation and configuration you need to make models translatable by implementing the TranslatableContract. The
HasTranslatables trait helps with this.

### Example model

```php
namespace App\Models;

use App\Base\Model;
use App\Contracts\TranslatableContract;
use App\Models\Traits\HasTranslatables;

class Country extends Model implements TranslatableContract {

  use HasTranslatables;

  protected $table = 'countries';
  protected $fillable = ['code', 'admin_name'];
  public $timestamps = FALSE;
  public $users = FALSE;



  /**
   * ATTRIBUTES
   */

  /**
   * Getter for 'name'.
   */
  public function getNameAttribute() {
    return $this->displayTranslation('name', TRUE);
  }

  /**
   * Getter for 'nationality'.
   */
  public function getNationalityAttribute() {
    return $this->displayTranslation('nationality', TRUE);
  }

  /**
   * OVERRIDES
   */

  /**
   * Return a nicely formatted, translated name for this country.
   */
  public function displayLabel($locale = NULL) {
    return $this->displayTranslation('name', TRUE, $locale);
  }

  /**
   * Return an default for a property in this content.
   */
  protected function getTranslationDefault($property) {
    return $this->admin_name;
  }

}

```

In above example $country->name and $country->nationality are translated. When there is no translation in the database
the admin_name property is used as fallback.

== Saving translations
You can add a translation to the database by using the facade:

```php
\ContentTranslation::saveTranslation('country', $country->id, 'name', 'nl', 'Nederland');
```

Or by using a method on the model:

```php
$country->saveTranslation('nl', ['name' => 'Nederland', 'nationality' => 'Nederlander']);
```

## Security

If you discover any security related issues, please email johnny@ezcompany.nl instead of using the issue tracker.

## About ezCompany
ezCompany is a webdevelopment agency in the Netherlands located in Tilburg, Breda and Utrecht. For more information see
[our website](https://ezcompany.nl).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.