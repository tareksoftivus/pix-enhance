<?php

namespace App\Modules\HomePageSettings\Models;

use Illuminate\Database\Eloquent\Model;

class HomePageSetting extends Model
{
    protected $table = 'home_page_settings';

    protected $fillable = ['key', 'value'];
}
