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
use Thotam\ThotamFileLibrary\Models\FileLibrary;

class GoogleDriveUpload implements ShouldQueue
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
        Log::info("ThotamFileLibrary upload to Google ID: ".$this->fileUpload->id. " - starting");
        $disk = Storage::disk('google');

        $disk->putStream($this->fileUpload->local_path, fopen(Storage::disk('public')->path($this->fileUpload->local_path), 'r+'), ["mimetype" => Storage::disk('public')->mimeType($this->fileUpload->local_path)]);

        $adapter = $disk->getDriver()->getAdapter();

        $metadata = $adapter->getMetadata($this->fileUpload->local_path);

        $disk->setVisibility($metadata["display_path"], 'public');

        $getFileObject = $adapter->getFileObject($metadata["virtual_path"]);

        $this->fileUpload->update([
            "drive" => "google",
            "google_virtual_path" => $metadata["virtual_path"],
            "google_display_path" => $metadata["display_path"],
            "google_id" => $getFileObject->id,
        ]);

        Storage::disk('public')->delete($this->fileUpload->local_path);
        Log::info("ThotamFileLibrary upload to Google ID: ".$this->fileUpload->id. " - finished");
    }
}
