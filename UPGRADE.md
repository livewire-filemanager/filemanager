# Upgrade Guide: v0.x → v1.0.0

## Overview

Version 1.0.0 includes a breaking change that fixes a naming inconsistency throughout the package. The package name changes from `livewire-fileuploader` to `livewire-filemanager` for all published assets and configurations.

This guide will help you migrate your application from v0.x to v1.0.0.

## What Changed

In previous versions, the package used inconsistent naming:
- Publishing tags, config files, and directories used `livewire-fileuploader`
- Components, views, and translations used `livewire-filemanager`

**Version 1.0.0 unifies all naming to `livewire-filemanager`**.

## Migration Steps

### Option 1: Automatic Migration (Recommended)

Run the automatic migration command that handles all the file renaming for you:

```bash
php artisan filemanager:migrate-config
```

This command will:
- Rename `config/livewire-fileuploader.php` → `config/livewire-filemanager.php`
- Move `resources/views/vendor/livewire-fileuploader/` → `resources/views/vendor/livewire-filemanager/`
- Move `resources/lang/vendor/livewire-fileuploader/` → `resources/lang/vendor/livewire-filemanager/`

The command will show you a summary of all changes made.

### Option 2: Manual Migration

If you prefer to migrate manually, follow these steps:

#### Step 1: Update Config File

```bash
mv config/livewire-fileuploader.php config/livewire-filemanager.php
```

#### Step 2: Update Published Views

If you've customized views:

```bash
mv resources/views/vendor/livewire-fileuploader resources/views/vendor/livewire-filemanager
```

#### Step 3: Update Published Translations

If you've customized translations:

```bash
mv resources/lang/vendor/livewire-fileuploader resources/lang/vendor/livewire-filemanager
```

#### Step 4: Update Publishing Tags

If you're re-publishing assets, update your scripts to use the new tags:

**Old:**
```bash
php artisan vendor:publish --tag=livewire-fileuploader-config
php artisan vendor:publish --tag=livewire-fileuploader-views
php artisan vendor:publish --tag=livewire-fileuploader-migrations
```

**New:**
```bash
php artisan vendor:publish --tag=livewire-filemanager-config
php artisan vendor:publish --tag=livewire-filemanager-views
php artisan vendor:publish --tag=livewire-filemanager-migrations
```

## Configuration

The package configuration method remains the same, but the file location has changed:

**Old path:**
```php
config('livewire-fileuploader.acl_enabled')
```

**New path:**
```php
config('livewire-filemanager.acl_enabled')
```

The internal code has been updated to use the new path automatically. If you have direct references to the configuration in your application code, update them accordingly.

## Component Usage

No changes needed for component usage:

```blade
<x-livewire-filemanager />
<x-livewire-filemanager-modal />
```

These remain unchanged.

## Livewire Directives

No changes needed for directives:

```blade
@filemanagerStyles
@filemanagerScripts
```

These remain unchanged.

## Testing

After upgrading, test the following:

1. **Configuration**: Verify the config file exists at the new location
2. **Views**: If you've customized views, ensure they're loading correctly
3. **Translations**: If you've customized translations, verify they're being loaded
4. **API**: If using the API, verify it's working correctly
5. **ACL**: If you've enabled ACL, verify permissions work as expected

Run your test suite to ensure everything still works:

```bash
composer test
```

## Troubleshooting

### "Class not found" errors for the migration command

Make sure you've updated to the latest version:

```bash
composer update livewire-filemanager/filemanager
```

### Config file not found

If you get errors about a missing config file:

1. Run the automatic migration command: `php artisan filemanager:migrate-config`
2. Or manually move the file: `mv config/livewire-fileuploader.php config/livewire-filemanager.php`
3. Clear config cache: `php artisan config:clear`

### Views not loading correctly

If your custom views aren't loading:

1. Check that views are in the new directory: `resources/views/vendor/livewire-filemanager/`
2. Clear view cache: `php artisan view:clear`

### Translations not loading

If your custom translations aren't loading:

1. Check that translations are in the new directory: `resources/lang/vendor/livewire-filemanager/`
2. Clear application cache: `php artisan cache:clear`

## Support

If you encounter any issues during the upgrade process, please:

1. Check this guide again
2. Review the [full documentation](docs.md)
3. Open an issue on [GitHub](https://github.com/livewire-filemanager/filemanager/issues)

## Breaking Changes Summary

- **Config file location**: `config/livewire-fileuploader.php` → `config/livewire-filemanager.php`
- **Published views**: `resources/views/vendor/livewire-fileuploader/` → `resources/views/vendor/livewire-filemanager/`
- **Published translations**: `resources/lang/vendor/livewire-fileuploader/` → `resources/lang/vendor/livewire-filemanager/`
- **Publishing tags**: All `livewire-fileuploader-*` tags → `livewire-filemanager-*` tags
- **Internal code**: All config references updated from `livewire-fileuploader.*` to `livewire-filemanager.*`

## No Changes Required For

- Component usage: `<x-livewire-filemanager />` (unchanged)
- Blade directives: `@filemanagerStyles`, `@filemanagerScripts` (unchanged)
- Livewire components: All naming remains the same
- API endpoints: All endpoints remain unchanged
