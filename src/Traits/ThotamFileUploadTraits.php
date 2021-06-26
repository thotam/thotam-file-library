<?php

namespace Thotam\ThotamFileLibrary\Traits;

use Livewire\TemporaryUploadedFile;
use Illuminate\Support\Facades\Storage;
use Thotam\ThotamFileLibrary\Models\FileLibrary;
use Thotam\ThotamFileLibrary\Jobs\GoogleDriveUpload;

trait ThotamFileUploadTraits
{
    protected $file_path, $temp_file, $file_name, $fileUpload, $local_path, $mime_type;

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

        if ($this->ThotamFileSubmit && $step ==4) {
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
     * @return void
     */
    protected function save_to_drive(TemporaryUploadedFile $file, $path, $file_name)
    {
        $this->temp_file = $file;
        $this->file_path = $path;
        $this->file_name = $file_name." ".$this->temp_file->getClientOriginalName();
        $this->mime_type = $this->temp_file->getMimeType();
        $this->saveAs();
        $this->put_to_db();
        $this->add_jobs();

        return $this->fileUpload;
    }

    /**
     * saveAs
     *
     * @return void
     */
    protected function saveAs()
    {
        $this->local_path = $temp_filesss = $this->temp_file->storeAs($this->file_path, $this->file_name, ["disk" => "public"]);
    }

    /**
     * delete
     *
     * @param  mixed $file
     * @return void
     */
    protected function delete(TemporaryUploadedFile $file)
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

        $this->fileUpload = $FileLibrary;
    }

    /**
     * add_jobs
     *
     * @return void
     */
    protected function add_jobs()
    {
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
