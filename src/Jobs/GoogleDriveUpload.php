<?php

namespace Thotam\ThotamFileLibrary\Jobs;

use Exception;
use Carbon\Carbon;
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
        $today = now();
        if ($today->dayOfWeek == Carbon::SATURDAY && $today->hour >= 8 && $today->hour <= 11) {
            throw new Exception("Không xử lý file vào sáng thứ 7 để đảm bảo đào tạo");
        }

        $this->fileUpload = FileLibrary::find($this->fileUpload->id);

        if (!!$this->fileUpload->google_id) {
            Log::info("ThotamFileLibrary upload to Google ID: " . $this->fileUpload->id . " - uploaded");
        } else {
            Log::info("ThotamFileLibrary upload to Google ID: " . $this->fileUpload->id . " - starting");
            $disk = Storage::disk('google');

            $this->mime_type = Storage::disk('public')->mimeType($this->fileUpload->local_path);

            $disk->writeStream($this->fileUpload->local_path, Storage::disk('public')->readStream($this->fileUpload->local_path), ["mimetype" => $this->mime_type, 'visibility' => 'public']);

            $adapter = $disk->getAdapter();

            $metadata = $adapter->getMetadata($this->fileUpload->local_path)->extraMetadata();

            if (!(bool)$metadata["id"]) {
                throw new Exception("Không thể lấy Google ID");
            }

            $this->fileUpload->update([
                "drive" => "google",
                "mime_type" => $this->mime_type,
                "google_virtual_path" => $metadata["virtual_path"],
                "google_display_path" => $metadata["display_path"],
                "google_id" => $metadata["id"],
            ]);

            $check = FileLibrary::find($this->fileUpload->id);
            // if (!!$check->vimeo_id || !!$check->youtube_id) {
            if (!(bool)$check->vimeo || (bool)$check->vimeo_id) {
                Storage::disk('public')->delete($this->fileUpload->local_path);
            }

            Log::info("ThotamFileLibrary upload to Google ID: " . $this->fileUpload->id . " - finished");
        }
    }
}
