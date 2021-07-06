<?php

namespace Thotam\ThotamFileLibrary\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Thotam\ThotamFileLibrary\Services\Youtube;
use Thotam\ThotamFileLibrary\Models\FileLibrary;

class YoutubeUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $fileUpload;

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
        Log::info("ThotamFileLibrary upload to Youtube: ".$this->fileUpload->id. " - starting");

        $youtube = new Youtube;

        $response = $youtube->upload(Storage::disk('public')->path($this->fileUpload->local_path), $this->fileUpload->youtube_data, $this->fileUpload->youtube_privacy_status);

        $this->fileUpload->update([
            "youtube_id" => $response->videoId,
        ]);

        $check = FileLibrary::find($this->fileUpload->id);
        //if (!!$check->google_id && !!$check->vimeo_id) {
        if (!!$check->google_id) {
            Storage::disk('public')->delete($this->fileUpload->local_path);
        }

        Log::info("ThotamFileLibrary upload to Youtube: ".$this->fileUpload->id. " - finished");
    }
}
