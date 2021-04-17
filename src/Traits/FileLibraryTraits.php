<?php

namespace Thotam\ThotamFileLibrary\Traits;

use Thotam\ThotamFileLibrary\Models\FileLibrary;
use Illuminate\Support\Collection;

trait FileLibraryTraits {

    /**
     * Get all of the model's file_libraries.
     */
    public function file_libraries()
    {
        return $this->morphMany(FileLibrary::class, 'file_library_table');
    }

    /**
     * addLibrary
     *
     * @param  mixed $lib
     * @param  mixed $tag
     * @return void
     */
    public function addLibrary(FileLibrary $lib, String $tag = null)
    {
        if (!!$tag) {
            $lib->update(["tag" => $tag]);
        }

        $this->file_libraries()->save($lib);
    }

    /**
     * addLibraries
     *
     * @param  mixed $libs
     * @param  mixed $tag
     * @return void
     */
    public function addLibraries(Collection $libs, String $tag = null)
    {
        foreach ($libs as $lib) {
            $this->addLibrary($lib, $tag);
        }
    }

    /**
     * removeLibrary
     *
     * @param  mixed $lib
     * @return void
     */
    public function removeLibrary(FileLibrary $lib)
    {
        $lib->fileLibrary_table()->delete();
    }

    /**
     * removeLibraries
     *
     * @param  mixed $libs
     * @param  mixed $tag
     * @return void
     */
    public function removeLibraries(Collection $libs, String $tag = null)
    {
        foreach ($libs as $lib) {
            $this->removeLibrary($lib, $tag);
        }
    }
}
