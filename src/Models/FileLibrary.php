<?php

namespace Thotam\ThotamFileLibrary\Models;

use Wildside\Userstamps\Userstamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

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
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'youtube_data' => 'array',
    ];

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

    /**
     * getViewLinkAttribute
     *
     * @return void
     */
    public function getViewLinkAttribute()
    {
        return route('filelibrary.view', ['id' => $this->id]);
    }

    /**
     * getEmbedLinkAttribute
     *
     * @return void
     */
    public function getEmbedLinkAttribute()
    {
        return route('filelibrary.embed', ['id' => $this->id]);
    }

    /**
     * getDownloadLinkAttribute
     *
     * @return void
     */
    public function getDownloadLinkAttribute()
    {
        return route('filelibrary.download', ['id' => $this->id]);
    }

    /**
     * getStreamLinkAttribute
     *
     * @return void
     */
    public function getStreamLinkAttribute()
    {
        return route('filelibrary.stream', ['id' => $this->id]);
    }

    /**
     * getGoogleapisLinkAttribute
     *
     * @return void
     */
    public function getGoogleapisLinkAttribute()
    {
        return route('filelibrary.googleapis', ['id' => $this->id]);
    }

    /**
     * getVideoLinkAttribute
     *
     * @return void
     */
    public function getVideoLinkAttribute()
    {
        return route('filelibrary.video', ['id' => $this->id]);
    }

    /**
     * getThumbnailAttribute
     *
     * @return void
     */
    public function getThumbnailAttribute()
    {
        return route('filelibrary.thumbnail', ['id' => $this->id]);
    }
}
