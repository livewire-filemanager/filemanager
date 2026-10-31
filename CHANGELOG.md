# Changelog

All notable changes to `livewire-filemanager/filemanager` will be documented in this file.

## [1.2.2] - 2026-10-31

### Fixed

- Show upload validation errors, which were silently dropped, and display the max file size next to the upload button. Contributed by [@cbz1991](https://github.com/cbz1991) (#104)

### Changed

- The `file_too_large` message now includes the max file size, and a new `max_file_size` translation key is available in all locales (#104)
- Document how to raise Livewire's temporary upload limit above 12 MB

## [1.2.1] - 2026-10-29

### Fixed

- Reject dangerous file extensions (`.pht`, `.phtm`, `.phps`, `.htaccess`, …) on the Livewire upload path, completing the fix for GHSA-9g95-48c6-r778 / CVE-2025-14894. Reported by [@0xjunwei](https://github.com/0xjunwei) (#103)
- Prevent stored XSS through crafted file names in the copy-link action (#103)

### Changed

- Require `illuminate/*` `^10.34` for the `extensions` validation rule
- Require `livewire/livewire` `^3.8.3|^4.3.4` to exclude CVE-2026-81887

## [1.2.0] - 2026-10-26

### Added

- Opt-in public file route, configured with `public_path` (`enabled`, `prefix`, `middleware`). Copied file urls include the prefix (#100)

### Changed

- Require `spatie/laravel-medialibrary` `^11.23` to get the fixes for GHSA-3ggm-c5m7-hfv5 and GHSA-fggg-964j-3j7h. Support for medialibrary 10 is dropped
- The public file route isn't registered when the prefix is empty
- With `acl_enabled`, the public file route only serves files to their owner. Guests and other users get a 404 (#99)

## [1.1.1] - 2026-10-26

### Fixed

- Serve public file paths by walking the folder tree: same-named folders under different parents no longer collide, and a file can no longer be served from another folder by name (#98)
- Lazy loading violation when resolving media conversions (#94)
- N+1 queries when rendering the folder list (#95)
- Rename duplicate-name check scoped to the file's own folder (#97)

## [1.1.0] - 2026-03-18

### Added

- Laravel 13 support
- PHPUnit 12 support
- Orchestra Testbench 11 support

### Changed

- Migrated model event registration from `boot()` to `booted()` for Laravel 13 compatibility (backward-compatible with Laravel 10/11/12)
- Updated CI matrix to include Laravel 13 (PHP 8.3+ required for Laravel 13)

## [1.0.0] - 2024-11-23

### Breaking Changes ⚠️

- **Naming unification**: Fixed inconsistent naming throughout the package. All `livewire-fileuploader` references have been renamed to `livewire-filemanager`.
  - Publishing tags: `livewire-fileuploader-*` → `livewire-filemanager-*`
  - Config file: `config/livewire-fileuploader.php` → `config/livewire-filemanager.php`
  - Published views: `resources/views/vendor/livewire-fileuploader/` → `resources/views/vendor/livewire-filemanager/`
  - Published translations: `resources/lang/vendor/livewire-fileuploader/` → `resources/lang/vendor/livewire-filemanager/`
  - Config references: `config('livewire-fileuploader.*')` → `config('livewire-filemanager.*')`

### Added

- New command `filemanager:migrate-config` to automatically migrate legacy v0.x installations to v1.0.0
- UPGRADE.md guide with detailed migration instructions

### Migration

See [UPGRADE.md](UPGRADE.md) for detailed migration instructions. Use `php artisan filemanager:migrate-config` to automatically migrate from v0.x to v1.0.0.

## Release Notes

### Added

* Added fillable properties to Folder model by [@bee-interactive](https://github.com/bee-interactive) in [`6a807a0`](https://github.com/livewire-filemanager/filemanager/commit/6a807a074486299f75a7dd22cab4feb7870ec2a9)
* Create new folder when clicking ousite creating folder by [@bee-interactive](https://github.com/bee-interactive) in [`8ee2054`](https://github.com/livewire-filemanager/filemanager/commit/8ee2054da1485fcb7d1799a83b72a70767d5e646)
* Add file manager translations for Spanish and Portuguese by [@kidiatoliny](https://github.com/kidiatoliny) in [`3103a5f`](https://github.com/livewire-filemanager/filemanager/commit/3103a5fd98a1baa51815363e74814eae6bd1d5f1)
* Added translation for copy/pasted notification by [@bee-interactive](https://github.com/bee-interactive) in [`0861876`](https://github.com/livewire-filemanager/filemanager/commit/0861876b990d7b238089a949cf1838e7f13a3187)
* Added more uniqueness to components by [@bee-interactive](https://github.com/bee-interactive) in [`abf6eaa`](https://github.com/livewire-filemanager/filemanager/commit/abf6eaa2dbabda60dfb041820032534e992e3ffd)
* Added copy to clipboard notification by [@bee-interactive](https://github.com/bee-interactive) in [`ffe61d8`](https://github.com/livewire-filemanager/filemanager/commit/ffe61d8646a544d8342efd082e9fc526542f8ab8)

### Changed

* Removed whole Laravel framework require in composer.json by bee-interactive in [`67978b8`](https://github.com/livewire-filemanager/filemanager/commit/67978b8ba12b6c55b6ffa0a909ba40819803298a)

### Fixed

* Fixed typo by [@bee-interactive](https://github.com/bee-interactive) in [`063bec0`](https://github.com/livewire-filemanager/filemanager/commit/063bec0048983d76ad2ec574acd09ec24961b767)
* Fixed css classes for correct colors in dynamics components by [@bee-interactive](https://github.com/bee-interactive) in [`534cc10`](https://github.com/livewire-filemanager/filemanager/commit/534cc1036600f4dc0a0ec16c7dff6340c3978f89)


**Full Changelog**: https://github.com/livewire-filemanager/filemanager/compare/v0.1.13..v0.2.0
## New Contributors
Special thanks to our first-time contributors: [@kidiatoliny](https://github.com/kidiatoliny)

## 0.1.0 - 2024-06-11

- Initial pre-release
