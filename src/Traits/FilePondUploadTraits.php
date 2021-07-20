<?php

namespace Thotam\ThotamFileLibrary\Traits;

use Livewire\TemporaryUploadedFile;
use Thotam\ThotamFileLibrary\Models\FilePondUpload;

trait FilePondUploadTraits
{
    public $FilePondFormSubmit =  false;
    public $FilePondHasUpload =  false;

    /**
     * FilePondUploadDone
     *
     * @param  mixed $name
     * @param  mixed $serverID
     * @param  mixed $isMultiple
     * @return void
     */
    public function FilePondUploadDone($name, $serverID, $isMultiple, $method = null)
    {
        $this->cleanupOldUploads();

        $tmpPath = FilePondUpload::whereIn('id', $serverID)->get()->pluck('livewire_patch')->toArray();

        if ($isMultiple) {
            $file = collect($tmpPath)->map(function ($i) {
                return TemporaryUploadedFile::createFromLivewire($i);
            })->toArray();
        } else {
            $file = TemporaryUploadedFile::createFromLivewire($tmpPath[0]);
            // If the property is an array, but the upload ISNT set to "multiple"
            // then APPEND the upload to the array, rather than replacing it.
            if (is_array($value = $this->getPropertyValue($name))) {
                $file = array_merge($value, [$file]);
            }
        }

        $this->syncInput($name, $file);

        $this->FilePondHasUpload = false;

        if ($this->FilePondFormSubmit && !!$method) {
            $this->emit($method);
            $this->FilePondFormSubmit = false;
        }
    }

    /**
     * FilePondUploadSubmit
     *
     * @return void
     */
    public function FilePondUploadSubmit($method = null)
    {
        if ($this->FilePondHasUpload) {
            $this->FilePondFormSubmit = true;
        } else {
            if (!!$method) {
                $this->emit($method);
            }
            $this->dispatchBrowserEvent('unblockUI');
        }
    }
}
