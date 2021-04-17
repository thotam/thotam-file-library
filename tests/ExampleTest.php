<?php

namespace Thotam\ThotamFileLibrary\Tests;

use Orchestra\Testbench\TestCase;
use Thotam\ThotamFileLibrary\ThotamFileLibraryServiceProvider;

class ExampleTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [ThotamFileLibraryServiceProvider::class];
    }
    
    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }
}
