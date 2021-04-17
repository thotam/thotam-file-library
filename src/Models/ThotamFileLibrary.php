<?php

namespace Thotam\ThotamFileLibrary\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThotamFileLibrary extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Userstamps;

    /**
     * Disable Laravel's mass assignment protection
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'thotam_file_libraries';
}
