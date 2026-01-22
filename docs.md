# Livewire Filemanager Documentation

## Table of Contents

1. [Introduction](#introduction)
2. [Requirements](#requirements)
3. [Installation](#installation)
4. [Configuration](#configuration)
5. [Architecture](#architecture)
6. [API Reference](#api-reference)
7. [Access Control (ACL)](#access-control-acl)
8. [Internationalization](#internationalization)
9. [Security](#security)
10. [Testing](#testing)
11. [Troubleshooting](#troubleshooting)

## Introduction

Livewire Filemanager is a comprehensive file management package for Laravel applications. Built with Livewire 3, it provides a modern, user-friendly interface for managing files and folders, complete with API endpoints for programmatic access.

### Key Features

- **Drag & Drop Support**: Intuitive file upload interface
- **Real-time Search**: Find files and folders instantly
- **Multi-language Support**: Available in 11 languages
- **Dark Mode**: Built-in dark/light theme support
- **RESTful API**: Complete API for programmatic access
- **ACL Support**: Optional access control for multi-user environments
- **Thumbnail Generation**: Automatic thumbnail creation for images
- **Laravel Integration**: Seamless integration with Laravel ecosystem

## Requirements

- **PHP**: 8.2.0 or greater
- **Laravel**: 10.x, 11.x, or 12.x
- **Livewire**: 3.5.4 or greater
- **Dependencies**:
  - livewire/livewire
  - spatie/laravel-medialibrary

## Installation

### Step 1: Install via Composer

```bash
composer require livewire-filemanager/filemanager
```

### Step 2: Publish Migrations

```bash
# Publish filemanager migrations
php artisan vendor:publish --tag=livewire-filemanager-migrations

# Publish Spatie Media Library migrations (if not already done)
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-migrations"
```

### Step 3: Run Migrations

```bash
php artisan migrate
```

This creates:
- `folders` table: Stores folder hierarchy
- `media` table: Stores file information (via Spatie Media Library)

### Step 4: Basic Implementation

Add the filemanager component to your Blade template:

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

### Step 5: Configure Tailwind CSS

For production environments, include the package views in your Tailwind configuration:

**Tailwind v4** (app.css):
```css
@source '../../vendor/livewire-filemanager/filemanager/resources/views/**/*.blade.php';
```

**Tailwind v3** (tailwind.config.js):
```javascript
module.exports = {
    content: [
        './resources/**/*.blade.php',
        './vendor/livewire-filemanager/filemanager/resources/views/**/*.blade.php',
    ],
}
```

### Step 6: Optional - Direct File Access

To enable direct file access via URL:

```php
// routes/web.php
use LivewireFilemanager\Filemanager\Http\Controllers\Files\FileController;

Route::get('{path}', [FileController::class, 'show'])
    ->where('path', '.*')
    ->name('assets.show');
```

## Configuration

### Publishing Configuration

```bash
php artisan vendor:publish --tag=livewire-filemanager-config
```

### Configuration Options

The `config/livewire-filemanager.php` file includes:

```php
return [
    // Access Control
    'acl_enabled' => false,

    // API Configuration
    'api' => [
        'enabled' => true,
        'prefix' => 'filemanager/v1',
        'middleware' => ['api', 'auth:sanctum'],
        'rate_limit' => '100,1',
        'max_file_size' => 10240,
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'txt', 'zip'],
        'chunk_size' => 1048576,
    ],

    // Callbacks
    'callbacks' => [
        'before_upload' => null,
        'after_upload' => null,
        'before_delete' => null,
        'after_delete' => null,
    ],
];
```

## Architecture

### Directory Structure

```
filemanager/
├── config/
│   └── livewire-filemanager.stub      # Configuration template
├── database/
│   └── migrations/                     # Database migrations
├── resources/
│   ├── lang/                          # Language files (11 languages)
│   └── views/                         # Blade templates
│       ├── components/                # UI components
│       ├── livewire/                  # Livewire component views
│       └── partials/                  # Shared partials
├── src/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/                   # API controllers
│   │   │   └── Files/                 # File access controller
│   │   └── Middleware/                # Custom middleware
│   ├── Livewire/                      # Livewire components
│   ├── Models/                        # Eloquent models
│   ├── Policies/                      # Authorization policies
│   ├── Traits/                        # Reusable traits
│   └── FilemanagerServiceProvider.php # Service provider
└── tests/                             # Test suite
```

### Database Schema

#### Folders Table
```sql
- id (primary key)
- name (string)
- parent_id (nullable, foreign key)
- user_id (nullable, for ACL)
- created_at
- updated_at
```

#### Media Table (Spatie Media Library)
```sql
- id (primary key)
- model_type
- model_id
- collection_name
- name
- file_name
- mime_type
- disk
- size
- custom_properties
- generated_conversions
- responsive_images
- manipulations
- created_at
- updated_at
```

## API Reference

### Authentication

The API uses Laravel Sanctum for authentication. Include the bearer token in the Authorization header:

```
Authorization: Bearer YOUR_API_TOKEN
```

### Base URL

```
https://your-domain.com/api/filemanager/v1
```

### Endpoints

#### Folders

**List Folders**
```http
GET /folders
```

Response:
```json
{
    "data": [
        {
            "id": 1,
            "name": "Documents",
            "parent_id": null,
            "created_at": "2024-01-01T00:00:00Z",
            "updated_at": "2024-01-01T00:00:00Z"
        }
    ]
}
```

**Create Folder**
```http
POST /folders
Content-Type: application/json

{
    "name": "New Folder",
    "parent_id": 1
}
```

**Update Folder**
```http
PUT /folders/{id}
Content-Type: application/json

{
    "name": "Renamed Folder"
}
```

**Delete Folder**
```http
DELETE /folders/{id}
```

#### Files

**List Files**
```http
GET /files
```

Parameters:
- `folder_id` (optional): Filter files by folder
- `search` (optional): Search files by name
- `page` (optional): Page number for pagination
- `per_page` (optional): Items per page (default: 20)

**Upload File**
```http
POST /files
Content-Type: multipart/form-data

{
    "file": <binary>,
    "folder_id": 1
}
```

**Upload to Specific Folder**
```http
POST /folders/{folder}/upload
Content-Type: multipart/form-data

{
    "file": <binary>
}
```

**Bulk Upload**
```http
POST /files/bulk
Content-Type: multipart/form-data

{
    "files[]": <binary>,
    "folder_id": 1
}
```

**Update File**
```http
PUT /files/{id}
Content-Type: application/json

{
    "name": "renamed-file.pdf"
}
```

**Delete File**
```http
DELETE /files/{id}
```

### Error Responses

```json
{
    "message": "Validation error",
    "errors": {
        "name": ["The name field is required."]
    }
}
```

Status Codes:
- `200`: Success
- `201`: Created
- `400`: Bad Request
- `401`: Unauthorized
- `403`: Forbidden
- `404`: Not Found
- `422`: Validation Error
- `429`: Too Many Requests

## Access Control (ACL)

### Enabling ACL

1. Publish configuration:
```bash
php artisan vendor:publish --tag=livewire-filemanager-config
```

2. Enable ACL in config:
```php
'acl_enabled' => true,
```

### How ACL Works

When ACL is enabled:
- Files and folders are scoped to the creating user
- Users can only see and manage their own files
- The `user_id` field is automatically populated
- Global scopes ensure data isolation

## Internationalization

### Supported Languages

- Arabic (ar)
- English (en)
- Spanish (es)
- Persian (fa)
- French (fr)
- Hebrew (he)
- Italian (it)
- Portuguese - Brazil (pt_BR)
- Portuguese - Portugal (pt_PT)
- Romanian (ro)
- Turkish (tr)

### Setting Language

The package automatically uses Laravel's locale:

```php
// Set application locale
App::setLocale('fr');
```

### Adding Translations

1. Create language file in `resources/lang/{locale}/filemanager.php`
2. Override specific translations:

```php
// resources/lang/es/filemanager.php
return [
    'upload' => 'Subir archivo',
    'create_folder' => 'Crear carpeta',
    // ... other translations
];
```

## Customization

### Custom Views

Override package views:

```bash
php artisan vendor:publish --tag=livewire-filemanager-views
```

Then modify views in `resources/views/vendor/livewire-filemanager/`.

### Styling

The package uses TailwindCSS classes. Customize by:

1. Using CSS variables:
```css
:root {
    --filemanager-primary: #3B82F6;
    --filemanager-secondary: #6B7280;
}
```

2. Overriding Tailwind classes in your CSS:
```css
.livewire-filemanager-container {
    @apply bg-gray-50 dark:bg-gray-900;
}
```

## Security

### File Upload Validation

Both the API endpoints and Livewire component validate file uploads against the configured allowed extensions and maximum file size.

**Configuration:**
```php
// config/livewire-filemanager.php
'api' => [
    'max_file_size' => 10240, // KB
    'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'txt', 'zip'],
],
```

**Blocked by default:**
- PHP files (.php, .phtml, .php5, etc.)
- Executable files (.exe, .sh, .bat, etc.)
- Server-side scripts

### Important Considerations

> **Warning**: While this package validates file types, you are still responsible for:
> - Implementing proper access control (see ACL section)
> - Scanning uploads for malware in sensitive environments
> - Configuring your web server to prevent PHP execution in storage directories

### Web Server Hardening

**Prevent PHP execution in storage directory:**

**Apache (.htaccess in storage/app/public/):**
```apache
<FilesMatch "\.php$">
    Deny from all
</FilesMatch>
```

**Nginx:**
```nginx
location ~* /storage/.*\.php$ {
    deny all;
}
```

### Best Practices

1. **File Validation**
   - Configure allowed extensions to only what your application needs
   - Set appropriate maximum file sizes
   - Both API and Livewire component enforce the same validation rules

2. **Access Control**
   - Enable ACL for multi-user environments
   - Implement custom policies for sensitive files
   - Use middleware for route protection

3. **Storage Security**
   - Consider storing files on a private disk
   - Use signed URLs for temporary access
   - Implement download authorization
   - Configure web server to prevent script execution in storage

4. **API Security**
   - Use Sanctum authentication
   - Implement rate limiting
   - Validate all inputs

## Testing

### Running Tests

```bash
# Run all tests
composer test
```

## Troubleshooting

### Common Issues

#### Thumbnails Not Generating

**Problem**: Image thumbnails are not appearing.

**Solution**: Ensure queue workers are running:
```bash
php artisan queue:work
```

Or disable queued processing:
```env
QUEUE_CONNECTION=sync
```

#### Tailwind Classes Not Applied

**Problem**: UI appears broken or unstyled.

**Solution**: Ensure package views are included in Tailwind config and rebuild:
```bash
npm run build
```

#### Files Not Uploading

**Problem**: File uploads fail silently.

**Solution**: Check:
1. PHP upload limits in `php.ini`:
   ```ini
   upload_max_filesize = 20M
   post_max_size = 25M
   ```
2. Laravel validation rules
3. Storage permissions:
   ```bash
   chmod -R 775 storage
   chown -R www-data:www-data storage
   ```

#### ACL Not Working

**Problem**: Users can see all files despite ACL being enabled.

**Solution**:
1. Clear cache: `php artisan cache:clear`
2. Verify Media model is correct in config
3. Check user_id is being set on uploads

#### API Returns 401

**Problem**: API requests return unauthorized.

**Solution**:
1. Ensure Sanctum is configured correctly
2. Generate API token for user
3. Include token in Authorization header

### Getting Help

1. Check the [GitHub Issues](https://github.com/livewire-filemanager/filemanager/issues)
2. Review the [Changelog](CHANGELOG.md) for recent changes
3. Join the community discussions
4. Contact support with:
   - Laravel version
   - Package version
   - Error messages
   - Steps to reproduce

## Conclusion

Livewire Filemanager provides a complete solution for file management in Laravel applications. With its intuitive interface, comprehensive API, and extensive customization options, it can be adapted to meet various project requirements while maintaining clean architecture and Laravel best practices.

For the latest updates and contributions, visit the [GitHub repository](https://github.com/livewire-filemanager/filemanager).
