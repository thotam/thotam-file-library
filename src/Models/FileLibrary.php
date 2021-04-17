<?php

namespace Thotam\ThotamFileLibrary\Models;

use Wildside\Userstamps\Userstamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FileLibrary extends Model
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

    /**
     * Get the parent file_library model FileLibrary.
     */
    public function file_library()
    {
        return $this->morphTo();
    }
}
