<?php

namespace App\Utilities;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Document
{
    protected string $disk;

    public function __construct(string $disk = 'public')
    {
        $this->disk = $disk;
    }

    /**
     * Normalize path to use correct directory separator for the OS
     */
    protected function normalizePath(?string $folder, string $filename = ''): string
    {
        if (!$folder) {
            return $filename;
        }

        $folder = trim($folder, '/\\');
        $folder = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $folder);

        return $filename ? $folder . DIRECTORY_SEPARATOR . $filename : $folder;
    }

    public function saveDocs(UploadedFile $file, string $title, ?string $folder = null, $id = null): string
    {
        // Generate unique filename
        $suffix = $id ? '-' . $id : '';
        $fileName = Str::slug($title) . '-' . date('YmdHis') . $suffix . '.' . $file->getClientOriginalExtension();

        // Prepare folder path with proper separator
        $folderPath = $folder ? $this->normalizePath($folder) : '';

        // Ensure folder exists
        if ($folderPath && !Storage::disk($this->disk)->exists($folderPath) && !Storage::disk($this->disk)->makeDirectory($folderPath)) {
            throw new BadRequestHttpException("Failed to create directory: {$folderPath}");
        }

        // Store the file
        $storedPath = Storage::disk($this->disk)->putFileAs($folderPath, $file, $fileName);

        if (!$storedPath) {
            throw new BadRequestHttpException('Failed to store file.');
        }

        // Return only filename (not full path) for consistency
        return $fileName;
    }

    public function deleteDocs(string $filename, ?string $folder = null): bool
    {
        $path = $this->normalizePath($folder, $filename);
        
        if (!Storage::disk($this->disk)->exists($path)) {
            return false;
        }
        
        return Storage::disk($this->disk)->delete($path);
    }

    /**
     * Get full URL of stored document
     */
    public function getDocUrl(string $filename, ?string $folder = null): ?string
    {
        $path = $this->normalizePath($folder, $filename);
        
        if (!Storage::disk($this->disk)->exists($path)) {
            return null;
        }
        
        // For public disk, generate URL using asset helper (always use forward slash for URLs)
        if ($this->disk === 'public') {
            $urlPath = str_replace(DIRECTORY_SEPARATOR, '/', $path);
            return asset('storage/' . $urlPath);
        }
        
        // For other disks, return the relative path
        return $path;
    }

    /**
     * Check if document exists
     */
    public function docExists(string $filename, ?string $folder = null): bool
    {
        $path = $this->normalizePath($folder, $filename);
        return Storage::disk($this->disk)->exists($path);
    }

    /**
     * Save raw binary/string content (e.g. a generated PDF) directly to storage.
     *
     * @param string      $content  Raw file content (binary or string)
     * @param string      $filename Filename including extension
     * @param string|null $folder   Storage folder (relative to disk root)
     * @return string Filename (same as $filename)
     */
    public function saveRawContent(string $content, string $filename, ?string $folder = null): string
    {
        $folderPath = $folder ? $this->normalizePath($folder) : '';

        if ($folderPath && !Storage::disk($this->disk)->exists($folderPath)) {
            Storage::disk($this->disk)->makeDirectory($folderPath);
        }

        $path = $this->normalizePath($folder, $filename);
        Storage::disk($this->disk)->put($path, $content);

        return $filename;
    }

    /**
     * Move document to different folder
     */
    public function moveDocs(string $filename, ?string $fromFolder = null, ?string $toFolder = null): bool
    {
        $fromPath = $this->normalizePath($fromFolder, $filename);
        $toPath = $this->normalizePath($toFolder, $filename);

        if (!Storage::disk($this->disk)->exists($fromPath)) {
            throw new NotFoundHttpException("Source file does not exist: {$fromPath}");
        }

        // Ensure destination folder exists
        if ($toFolder) {
            $toFolderPath = $this->normalizePath($toFolder);
            if (!Storage::disk($this->disk)->exists($toFolderPath)) {
                Storage::disk($this->disk)->makeDirectory($toFolderPath);
            }
        }

        return Storage::disk($this->disk)->move($fromPath, $toPath);
    }

    /**
     * Copy document from one folder to another and return the new filename.
     *
     * @param string $filename
     * @param string|null $fromFolder
     * @param string|null $toFolder
     * @param string|null $newFileName Optional new filename (including extension). If not provided a timestamped name will be generated.
     * @return string New filename
     */
    public function copyDocs(string $filename, ?string $fromFolder = null, ?string $toFolder = null, ?string $newFileName = null): string
    {
        $fromPath = $this->normalizePath($fromFolder, $filename);

        if (!Storage::disk($this->disk)->exists($fromPath)) {
            throw new NotFoundHttpException("Source file does not exist: {$fromPath}");
        }

        // Ensure destination folder exists
        if ($toFolder) {
            $toFolderPath = $this->normalizePath($toFolder);
            if (!Storage::disk($this->disk)->exists($toFolderPath)) {
                Storage::disk($this->disk)->makeDirectory($toFolderPath);
            }
        }

        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $baseName = pathinfo($filename, PATHINFO_FILENAME);

        if ($newFileName) {
            $destFileName = $newFileName;
        } else {
            $destFileName = $baseName . '-' . date('YmdHis') . '.' . $extension;
        }

        $toPath = $this->normalizePath($toFolder, $destFileName);

        $copied = Storage::disk($this->disk)->copy($fromPath, $toPath);

        if (!$copied) {
            throw new BadRequestHttpException('Failed to copy document.');
        }

        return $destFileName;
    }
}
