<?php

namespace Thotam\ThotamFileLibrary\Jobs;

use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Vimeo\Laravel\Facades\Vimeo;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Thotam\ThotamFileLibrary\Models\FileLibrary;

class VimeoUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $fileUpload, $mime_type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(FileLibrary $fileUpload)
    {
        $this->fileUpload = $fileUpload;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info("ThotamFileLibrary upload to Vimeo: ".$this->fileUpload->id. " - starting");

        $parameters = [
            'name' => $this->fileUpload->vimeo_name,
            'description' => $this->fileUpload->vimeo_description,
            'privacy' => [
                'view' => $this->fileUpload->vimeo_view,
            ],
        ];

        $response = Vimeo::upload(Storage::disk('public')->path($this->fileUpload->local_path), $parameters);

        $this->fileUpload->update([
            "vimeo_id" => Str::of($response)->explode('/')->last(),
        ]);

        $check = FileLibrary::find($this->fileUpload->id);
        if (!!$check->google_id) {
            Storage::disk('public')->delete($this->fileUpload->local_path);
        }

        Log::info("ThotamFileLibrary upload to Vimeo: ".$this->fileUpload->id. " - finished");
    }
}
