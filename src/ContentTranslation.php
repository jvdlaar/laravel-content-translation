<?php

namespace JvdLaar\ContentTranslation\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Http\Request;

class ContentTranslation extends Model {

  protected $table = 'content_translations';
  protected $fillable = ['content_type', 'content_id', 'content_property', 'locale', 'translation'];
  protected $dates = ['created_at', 'updated_at'];
  public $timestamps = FALSE;
  public $users = FALSE;

}
