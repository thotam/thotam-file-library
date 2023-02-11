<?php

namespace Thotam\ThotamFileLibrary\Jobs;

use Exception;
use Carbon\Carbon;
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

class VimeoReUpload implements ShouldQueue
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

        if (!!$this->fileUpload->vimeo_id) {
            Log::info("ThotamFileLibrary upload to Vimeo: " . $this->fileUpload->id . " - uploaded");
        } else {
            Log::info("ThotamFileLibrary upload to Vimeo: " . $this->fileUpload->id . " - starting");

            $parameters = [
                'name' => mb_substr($this->fileUpload->vimeo_name ?? $this->fileUpload->file_name, 0, 256),
                'description' => mb_substr($this->fileUpload->vimeo_description, 0, 5000),
                'privacy' => [
                    'view' => $this->fileUpload->vimeo_view ?? "disable",
                ],
            ];

            Storage::disk('public')->writeStream($this->fileUpload->local_path, Storage::disk('google')->readStream($this->fileUpload->local_path), ["mimetype" => $this->mime_type, 'visibility' => 'public']);

            $response = Vimeo::upload(Storage::disk('public')->path($this->fileUpload->local_path), $parameters);

            $this->fileUpload->update([
                "vimeo_id" => Str::of($response)->explode('/')->last(),
            ]);

            $check = FileLibrary::find($this->fileUpload->id);
            if (!!$check->google_id) {
                Storage::disk('public')->delete($this->fileUpload->local_path);
            }

            Log::info("ThotamFileLibrary upload to Vimeo: " . $this->fileUpload->id . " - finished");
        }
    }
}
