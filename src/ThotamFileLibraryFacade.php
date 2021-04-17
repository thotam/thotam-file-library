<?php

namespace Thotam\ThotamFileLibrary;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Thotam\ThotamFileLibrary\Skeleton\SkeletonClass
 */
class ThotamFileLibraryFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'thotam-file-library';
    }
}
