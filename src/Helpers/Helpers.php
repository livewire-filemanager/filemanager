<?php

use LivewireFilemanager\Filemanager\Models\Folder;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/*
|--------------------------------------------------------------------------
| TrimString
|--------------------------------------------------------------------------
|
*/

if (!function_exists('trimString')) {
    function trimString($string, $maxLength)
    {
        $extension = pathinfo($string, PATHINFO_EXTENSION);
        $baseLength = $maxLength - 8; // 4 for the dots and 4 for the last part of the filename

        if (strlen($string) <= $maxLength) {
            return $string;
        }

        $trimmedBase = substr($string, 0, $baseLength);
        $end = substr($string, -4); // Get last 4 characters

        return $trimmedBase . "...." . $end;
    }
}

/*
|--------------------------------------------------------------------------
| Get the file mime type
|--------------------------------------------------------------------------
|
*/

if (!function_exists('getFileType')) {
    function getFileType(string|null $mimeType): string|null
    {
        if(!$mimeType) {
            return null;
        }

        switch ($mimeType) {
            case 'application/pdf':
                return 'pdf';
            case 'application/zip':
            case 'application/x-zip-compressed':
                return 'zip';
            case 'application/msword':
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                return 'docx';
            case 'application/vnd.ms-excel':
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                return 'xlsx';
            case 'application/vnd.ms-powerpoint':
            case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
                return 'pptx';
            case 'video/mp4':
                return 'video';
            case 'video/webm':
                return 'video';
            case 'video/ogg':
                return 'video';
            case 'audio/mpeg':
                return 'audio';
            case 'audio/ogg':
                return 'audio';
            case 'audio/wav':
                return 'audio';
            default:
                return 'file';
        }
    }
}

/*
|--------------------------------------------------------------------------
| Build the folder path
|--------------------------------------------------------------------------
|
*/

if (!function_exists('getMediaFullPath')) {
    function getMediaFullPath(Media $media)
    {
        $folder = Folder::where('id', $media->model_id)->first();

        // Initialize the path with the media file name
        $path = [$media->file_name];

        // Traverse up the folder hierarchy
        while ($folder) {
            array_unshift($path, $folder->slug);

            $folder = $folder->parentWithoutRootFolder;
        }

        // Return the full path as a string
        return config('app.url') . '/' . implode('/', $path);
    }
}

/*
|--------------------------------------------------------------------------
| Build the folder path
|--------------------------------------------------------------------------
|
*/

if (!function_exists('buildFolderPath')) {
    function buildFolderPath($folderId)
    {
        $folder = Folder::find($folderId);

        if ($folder && $folder->parentWithoutRootFolder) {
            return buildFolderPath($folder->parentWithoutRootFolder->id) . '/' . $folder->slug;
        } else {
            return $folder ? $folder->slug : '';
        }
    }
}
