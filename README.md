## Installation

You can install the package via [composer](https://getcomposer.org):

```bash
composer require livewire-filemanager/filemanager
```

Publish the package's migration file:

```bash
php artisan vendor:publish --tag=livewire-fileuploader-migrations
```

### Preparing your template

Next, you'll need to use the `<x-livewire-filemanager />` component where you want to place the filemanager and the @filemanagerScripts directive :

```html
<!DOCTYPE html>
<html>
<head>
    <!-- ... -->
</head>

<body>
    <x-livewire-filemanager />

    @filemanagerScripts
</body>
</html>
```

This package relies on spatie/medialibrary to handle the medias, so if you haven't already configured the package, don't forget this step:

```bash
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-migrations"
```

After that, you need to run migrations.

```bash
php artisan migrate
```

This will create a `folders` table which will hold all the filemanager structure.


## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Yves Engetschwiler](https://github.com/bee-interactive)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
