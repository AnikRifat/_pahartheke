<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Announcement
 *
 * @property int $id
 * @property string $name
 * @property string|null $logo
 * @property string|null $type
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property int $status
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Announcement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Announcement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Announcement query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Announcement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Announcement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Announcement whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Announcement whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Announcement whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Announcement whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Announcement whereStatus($value)
 * @mixin \Eloquent
 */

class Announcement extends Model
{
    //
}
