<?php

namespace Thotam\ThotamFileLibrary\Traits;

use Illuminate\Http\UploadedFile;
use Livewire\TemporaryUploadedFile;
use Illuminate\Support\Facades\Storage;
use Thotam\ThotamFileLibrary\Jobs\VimeoUpload;
use Thotam\ThotamFileLibrary\Jobs\YoutubeUpload;
use Thotam\ThotamFileLibrary\Models\FileLibrary;
use Thotam\ThotamFileLibrary\Jobs\GoogleDriveUpload;

trait ThotamFileUploadTraits
{
    protected $file_path, $temp_file, $file_name, $fileUpload, $local_path, $mime_type, $vimeo = false, $vimeo_name, $vimeo_description, $vimeo_view;
    protected $youtube, $youtube_data = [], $youtube_privacy_status;

    public $ThotamFileUploadStep = [], $ThotamFileUploadMethod = NULL, $ThotamFileId = NULL, $ThotamFileSubmit = false;

    /**
     * Update_ThotamFileUploadStep
     *
     * @param  mixed $model
     * @param  mixed $step
     * @return void
     */
    public function Update_ThotamFileUploadStep($model, $step)
    {
        $this->ThotamFileUploadStep[$model] = $step;

        if ($this->ThotamFileSubmit && $step == 4) {
            $this->check_upload();
        }
    }

    /**
     * ThotamFileUploadSubmit
     *
     * @param  mixed $method
     * @param  mixed $id
     * @return void
     */
    public function ThotamFileUploadSubmit($method, $id = NULL)
    {
        $this->ThotamFileUploadMethod = $method;
        $this->ThotamFileId = $id;
        $this->ThotamFileSubmit = true;
        $this->check_upload();
    }

    /**
     * check_upload
     *
     * @return void
     */
    protected function check_upload()
    {
        $array_count = array_count_values($this->ThotamFileUploadStep);
        $count = 0;

        if (array_key_exists(3, $array_count)) {
            $count += $array_count[3];
        }

        if (array_key_exists(4, $array_count)) {
            $count += $array_count[4];
        }

        if (count($this->ThotamFileUploadStep) == $count) {
            $this->emit($this->ThotamFileUploadMethod, $this->ThotamFileId);
            $this->ThotamFileUploadMethod = NULL;
            $this->ThotamFileId = NULL;
            $this->ThotamFileSubmit = false;
            $this->ThotamFileUploadStep = [];
        }
    }

    /**
     * save_to_drive
     *
     * @param  mixed $file
     * @param  mixed $path
     * @param  mixed $file_name
     * @param  mixed $rename
     * @return void
     */
    protected function save_to_drive(TemporaryUploadedFile $file, $path, $file_name, $rename = false)
    {
        $this->temp_file = $file;
        $this->file_path = $path;
        if ($rename) {
            $_clientOriginalName = $this->temp_file->getClientOriginalName();
            $this->file_name = $file_name . mb_substr($_clientOriginalName, mb_strrpos($_clientOriginalName, '.'));
        } else {
            $this->file_name = $file_name . " " . $this->temp_file->getClientOriginalName();
        }
        $this->mime_type = $this->temp_file->getMimeType();
        $this->saveAs();
        $this->put_to_db();
        $this->add_jobs();

        return $this->fileUpload;
    }

    protected function move_to_drive($path, $file_name, $new_path, $mime_type = null)
    {
        $this->temp_file = new UploadedFile(Storage::path($path), $file_name, $mime_type);
        $this->file_path = $new_path;
        $this->file_name = $file_name;
        $this->mime_type = $this->temp_file->getMimeType();
        $this->saveAs();
        $this->put_to_db();
        $this->add_jobs();

        return $this->fileUpload;
    }

    /**
     * save_to_youtube
     *
     * @param  mixed $file
     * @param  mixed $path
     * @param  mixed $file_name
     * @return void
     */
    protected function save_to_youtube(TemporaryUploadedFile $file, $path, $file_name, $youtube_data = [], $youtube_privacy_status = "unlisted")
    {
        $this->youtube = true;
        $this->youtube_data = $youtube_data;
        $this->youtube_privacy_status = $youtube_privacy_status;

        //return $this->save_to_vimeo($file, $path, $file_name, $youtube_data['title']);

        return $this->save_to_drive($file, $path, $file_name);
    }

    /**
     * save_to_vimeo
     *
     * @param  mixed $file
     * @param  mixed $path
     * @param  mixed $file_name
     * @return void
     */
    protected function save_to_vimeo(TemporaryUploadedFile $file, $path, $file_name, $vimeo_name = NULL, $vimeo_description = NULL, $vimeo_view = "disable")
    {
        $this->vimeo = true;
        $this->vimeo_name = $vimeo_name;
        $this->vimeo_description = $vimeo_description;
        $this->vimeo_view = $vimeo_view;

        return $this->save_to_drive($file, $path, $file_name);
    }

    /**
     * saveAs
     *
     * @return void
     */
    protected function saveAs()
    {
        $this->local_path = $this->temp_file->storeAs($this->file_path, $this->file_name, ["disk" => "public"]);
    }

    /**
     * delete_file
     *
     * @param  mixed $file
     * @return void
     */
    protected function delete_file(TemporaryUploadedFile $file)
    {
        $file->delete();
    }

    /**
     * put_to_db
     *
     * @return void
     */
    protected function put_to_db()
    {
        $FileLibrary = FileLibrary::create([
            "drive" => "public",
            "file_name" => $this->file_name,
            "mime_type" => $this->mime_type,
            "active" => true,
            "local_path" => $this->local_path,
        ]);

        if ($this->youtube) {
            $FileLibrary->update([
                "youtube" => true,
                "youtube_data" => $this->youtube_data,
                "youtube_privacy_status" => $this->youtube_privacy_status,
            ]);
        }

        if ($this->vimeo) {
            $FileLibrary->update([
                "vimeo" => true,
                "vimeo_name" => $this->vimeo_name,
                "vimeo_description" => $this->vimeo_description,
                "vimeo_view" => $this->vimeo_view,
            ]);
        }

        $this->fileUpload = $FileLibrary;
    }

    /**
     * add_jobs
     *
     * @return void
     */
    protected function add_jobs()
    {
        if ($this->youtube) {
            YoutubeUpload::dispatch($this->fileUpload);
        }

        if ($this->vimeo) {
            VimeoUpload::dispatch($this->fileUpload);
        }

        GoogleDriveUpload::dispatch($this->fileUpload);
    }

    /**
     * get_url
     *
     * @param  mixed $id
     * @return void
     */
    public function get_url($id)
    {
        $this->fileUpload = FileLibrary::find($id);

        if ($this->fileUpload->drive == "public") {
            $path = $this->fileUpload->local_path;
        } else {
            $path = $this->fileUpload->google_display_path;
        }
        return Storage::disk($this->fileUpload->drive)->url($path);
    }
}
