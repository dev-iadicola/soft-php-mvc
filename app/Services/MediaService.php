<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Exception\NotFoundException;
use App\Core\Facade\Storage;
use App\Core\Helpers\ImageHelper;
use App\Model\Media;

class MediaService
{
    /**
     * Attach an uploaded file to an entity.
     *
     * @param string $entityType e.g. 'project', 'article'
     * @param int $entityId
     * @param array{name: string, tmp_name: string, type?: string, size?: int} $uploadedFile
     * @param string $disk
     */
    public static function attach(string $entityType, int $entityId, array $uploadedFile, string $disk = 'public'): Media
    {
        $extension = strtolower(pathinfo($uploadedFile['name'], PATHINFO_EXTENSION));
        $filename = uniqid($entityType . '_') . '.' . $extension;
        $path = $entityType . '/' . $filename;

        $content = file_get_contents($uploadedFile['tmp_name']);

        // Resize and compress images
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            $content = ImageHelper::processFromString($content, $extension);
        }

        $storage = Storage::make($disk);
        $storage->put($path, $content, ['visibility' => $disk === 'public' ? 'public' : 'private']);

        $dbPath = $storage->getPath($path);

        // Get next sort_order
        $maxOrder = Media::query()
            ->where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->count();

        Media::query()->create([
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'path' => $dbPath,
            'disk' => $disk,
            'sort_order' => $maxOrder,
        ]);

        /** @var Media */
        return Media::query()->orderBy('id', 'DESC')->first();
    }

    /**
     * Get all media for a given entity.
     *
     * @return array<int, Media>
     */
    public static function getFor(string $entityType, int $entityId): array
    {
        return Media::query()
            ->where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->orderBy('sort_order')
            ->get();
    }

    public static function findOrFail(int $id): Media
    {
        /** @var Media|null $media */
        $media = Media::query()->find($id);

        if ($media === null) {
            throw new NotFoundException("Media with id {$id} not found");
        }

        return $media;
    }

    public static function delete(int $id): void
    {
        $media = self::findOrFail($id);

        // Delete file from disk
        $storagePath = self::toStoragePath($media->path, $media->disk);
        Storage::make($media->disk)->deleteIfExist($storagePath);

        Media::query()->where('id', $id)->delete();
    }

    /**
     * Delete all media for an entity.
     */
    public static function deleteAllFor(string $entityType, int $entityId): void
    {
        $items = self::getFor($entityType, $entityId);
        foreach ($items as $media) {
            $storagePath = self::toStoragePath($media->path, $media->disk);
            Storage::make($media->disk)->deleteIfExist($storagePath);
        }

        Media::query()
            ->where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->delete();
    }

    public static function reorder(int $id, int $newOrder): void
    {
        Media::query()->where('id', $id)->update(['sort_order' => $newOrder]);
    }

    /**
     * Convert a DB path (e.g. /storage/project/img.jpg) to a storage-relative path.
     */
    private static function toStoragePath(string $dbPath, string $disk): string
    {
        if ($disk === 'public' && str_starts_with($dbPath, '/storage/')) {
            return substr($dbPath, strlen('/storage/'));
        }

        return ltrim($dbPath, '/');
    }
}
