<?php

namespace Thotam\ThotamFileLibrary\Traits;

use Thotam\ThotamFileLibrary\Models\FileLibrary;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait FileLibraryTraits
{

    /**
     * Get all of the model's file_libraries.
     */
    public function file_libraries()
    {
        return $this->morphMany(FileLibrary::class, 'file_library');
    }

    /**
     * Get all of the model's file_library.
     */
    public function file_library(): MorphOne
    {
        return $this->morphOne(FileLibrary::class, 'file_library')->ofMany('id', 'max');
    }

    /**
     * addLibrary
     *
     * @param  mixed $lib
     * @param  mixed $tag
     * @return void
     */
    public function addLibrary(FileLibrary $lib, string $tag = null)
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
    public function addLibraries($libs, string $tag = null)
    {
        if (!is_null($libs)) {
            foreach ($libs as $lib) {
                $this->addLibrary($lib, $tag);
            }
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
        $lib->file_library()->dissociate()->save();
    }

    /**
     * removeLibraries
     *
     * @param  mixed $libs
     * @param  mixed $tag
     * @return void
     */
    public function removeLibraries($libs, string $tag = null)
    {
        if (!is_null($libs)) {
            foreach ($libs as $lib) {
                $this->removeLibrary($lib);
            }
        }
    }

    /**
     * getLibrary
     *
     * @param  mixed $tag
     * @return void
     */
    public function getLibrary(string $tag = null)
    {
        $library = $this->file_libraries()->latest();

        if (!!$tag) {
            $library->where("tag", $tag);
        }

        return $library->first();
    }

    /**
     * getLibraries
     *
     * @param  mixed $tag
     * @return void
     */
    public function getLibraries(string $tag = null)
    {
        $library = $this->file_libraries();

        if (!!$tag) {
            $library->where("tag", $tag);
        }

        return $library->get();
    }
}
