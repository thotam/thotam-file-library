# Very short description of the package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/thotam/thotam-file-library.svg?style=flat-square)](https://packagist.org/packages/thotam/thotam-file-library)
[![Build Status](https://img.shields.io/travis/thotam/thotam-file-library/master.svg?style=flat-square)](https://travis-ci.org/thotam/thotam-file-library)
[![Quality Score](https://img.shields.io/scrutinizer/g/thotam/thotam-file-library.svg?style=flat-square)](https://scrutinizer-ci.com/g/thotam/thotam-file-library)
[![Total Downloads](https://img.shields.io/packagist/dt/thotam/thotam-file-library.svg?style=flat-square)](https://packagist.org/packages/thotam/thotam-file-library)

This is where your description should go. Try and limit it to a paragraph or two, and maybe throw in a mention of what PSRs you support to avoid any confusion with users and contributors.

## Installation

You can install the package via composer:

```bash
composer require thotam/thotam-file-library
```

## Usage

### Add this to config/filesystems.php

```php
        'google' => [
            'driver' => 'google',
            'clientId' => env('MAIN_GOOGLE_DRIVE_CLIENT_ID'),
            'clientSecret' => env('MAIN_GOOGLE_DRIVE_CLIENT_SECRET'),
            'refreshToken' => env('MAIN_GOOGLE_DRIVE_REFRESH_TOKEN'),
            'folderId' => env('MAIN_GOOGLE_DRIVE_FOLDER_ID'),
        ],
```

### Add this to .env

```php
MAIN_GOOGLE_DRIVE_CLIENT_ID=""
MAIN_GOOGLE_DRIVE_CLIENT_SECRET=""
MAIN_GOOGLE_DRIVE_REFRESH_TOKEN=""
MAIN_GOOGLE_DRIVE_FOLDER_ID=""

MAIN_GOOGLE_API_KEY=""

VIMEO_CLIENT=
VIMEO_SECRET=
VIMEO_ACCESS=

VIMEO_ALT_CLIENT=
VIMEO_ALT_SECRET=
VIMEO_ALT_ACCESS=
```

### Add ThotamGoogleDriveServiceProvider

```php
add Thotam\ThotamFileLibrary\Providers\ThotamGoogleDriveServiceProvider::class to 'providers' in config/app.php
```

### Add FileLibraryTraits to you Model you want to you

```php
use Thotam\ThotamFileLibrary\Traits\FileLibraryTraits;
```

### Add ThotamFileUploadTraits to you Livewire class you want to handle your file

```php
use Thotam\ThotamFileLibrary\Traits\ThotamFileUploadTraits;
```

### add schedule to App\Console\Kernel;

```php
$schedule->command('thotam-file-library:clean-public-disk')->everyTenMinutes();
```


### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email thanhtamtqno1@gmail.com instead of using the issue tracker.

## Credits

-   [thotam](https://github.com/thotam)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
