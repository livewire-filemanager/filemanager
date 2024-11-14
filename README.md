<p align="center"><img src="https://github.com/livewire-filemanager/filemanager/raw/master/images/banner.png" alt="Livewire filemanager image"></p>

# A Livewire filemanager for your Laravel applications

A simple, friendly, and practical file manager designed specifically for Laravel applications. This Livewire-powered tool makes it easy to manage files and folders within your project, offering an intuitive interface and seamless integration with Laravel's ecosystem. Perfect for developers seeking an efficient and user-friendly solution for file and folders management.

- Drag & drop files
- Search for files or folders
- Ready to include in any projects
- Multiple languages (en, es, fr, pt)
- Darkmode available

## Project requirements

- PHP 8.2.0 or greater required

## Important composer dependencies

- [livewire/livewire](https://laravel-livewire.com/)
- [spatie/laravel-medialibrary](https://spatie.be/docs/laravel-medialibrary)

## Installation

> [!IMPORTANT]
> This package is still in development and its structure can change until a stable version is released. Use with caution in you projects.

You can install the package via [composer](https://getcomposer.org):

```bash
composer require livewire-filemanager/filemanager
```

Publish the package's migration file:

```bash
php artisan vendor:publish --tag=livewire-fileuploader-migrations
```

This package relies on spatie/medialibrary to handle the medias, so if you haven't already configured the package, don't forget this step:

```bash
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-migrations"
```

If you need for informations about the spatie/medialibrary package, please [visit their documentation](https://spatie.be/docs/laravel-medialibrary)

> [!NOTE]
> **Thumbnails** When you upload images, the package will generate a thumbnail. By defaults, it will be dispatched into the queues. You'll have to launch the workers inside your app or change the **QUEUE_CONNECTION** value in your .env file

After that, you need to run migrations.

```bash
php artisan migrate
```

This will create a `folders` table which will hold all the filemanager structure and a media table if not already present.

### Package configuration

Next, you'll need to use the `<x-livewire-filemanager />` component where you want to place the filemanager.

For the styles and scripts, the package relies on TailwindCSS and AlpineJS. So if you don't already have them installed, you can include the `@filemanagerScripts` and `@filemanagerStyles`.

```html
<!DOCTYPE html>
<html>
<head>
    @filemanagerStyles
</head>

<body>
    <x-livewire-filemanager />

    @filemanagerScripts
</body>
</html>
```

> [!IMPORTANT]
> The @filemanagerStyles will include the PLAY CDN from TailwindCSS which is not recommended for production. Instead, prefer including the path inside your tailwind config file.

```js
module.exports = {
    content: [
        './resources/**/*.blade.php',
        './vendor/livewire-filemanager/filemanager/resources/views/**/*.blade.php',
    ],
}
```

If you intent to give access to your files with the public, you can add this inside your web routes file:.

```
Route::get('{path}', [FileController::class, 'show'])->where('path', '.*')->name('assets.show');
```

And don't forget to import the FileController class:

```
use LivewireFilemanager\Filemanager\Http\Controllers\Files\FileController;
```

This will give you an endpoint where you can reach the files with a direct url.

## The interface

Once everything is installed, the interface should look like this:

<p align="center"><img src="https://github.com/livewire-filemanager/filemanager/raw/master/images/interface.jpg" alt="Livewire filemanager interface"></p>

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

All contributions are welcome and will be fully credited.

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Yves Engetschwiler](https://github.com/bee-interactive)
- All illustrations are made by [Quetzal Graphic Design](https://quetzal.ch/)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
