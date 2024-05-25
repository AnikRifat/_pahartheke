<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App;

class Announcement extends Model
{
  public function getTranslation($field = '', $lang = false){
      $lang = $lang == false ? App::getLocale() : $lang;
      $announcement_translation = $this->hasMany(AnnouncementTranslation::class)->where('lang', $lang)->first();
      return $announcement_translation != null ? $announcement_translation->$field : $this->$field;
  }

  public function announcement_translations(){
    return $this->hasMany(AnnouncementTranslation::class);
  }

}
