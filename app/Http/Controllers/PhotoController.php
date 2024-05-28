<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Photo;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;


class PhotoController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:20480', // 20480 KB = 20 MB
        ]);

        $file = $request->file('file');

        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();

        $randomString = Str::random(8);
        $fileName = "{$originalName}_{$randomString}.{$extension}";

        // Ensure directories exist
        Storage::disk('public')->makeDirectory('photos');
        Storage::disk('public')->makeDirectory('photos/thumbnails');

        // Original Image
        $file->storeAs('photos', $fileName, 'public');

        // Get the file path
        $filePath = storage_path('app/public/photos/' . $fileName);

        // Create a thumbnail
        $manager = new ImageManager(new Driver());

        // Thumbnail Image
        $thumbnail = $manager->read($file->getRealPath())->scale(width: 200);


        $thumbnailPath = storage_path('app/public/photos/thumbnails/' . $fileName);
        $thumbnail->save($thumbnailPath);

        // Extract EXIF data if available
        $exifData = $this->sanitizeExifData(exif_read_data($filePath) ?: []);

        // Extract relevant metadata
        $metadata = [
            'make' => $exifData['Make'] ?? null,
            'model' => $exifData['Model'] ?? null,
            'latitude' => $this->getGps($exifData['GPSLatitude'], $exifData['GPSLatitudeRef']),
            'longitude' => $this->getGps($exifData['GPSLongitude'], $exifData['GPSLongitudeRef']),
            'mime_type' => $exifData['MimeType'] ?? null,
            'width' => $exifData['COMPUTED']['Width'] ?? null,
            'height' => $exifData['COMPUTED']['Height'] ?? null,
            'taken_at' => isset($exifData['DateTimeOriginal']) ? date('Y-m-d H:i:s', strtotime($exifData['DateTimeOriginal'])) : null,
            'iso_speed' => $exifData['ISOSpeedRatings'] ?? null,
            'focal_length' => isset($exifData['FocalLength']) ? $this->convertToDecimal($exifData['FocalLength']) : null,
            'software' => $exifData['Software'] ?? null,
            'additional_metadata' => json_encode($exifData),
        ];

        // Create photo record
        $photo = Photo::create([
            'file_path' => $fileName,
            'make' => $metadata['make'],
            'model' => $metadata['model'],
            'latitude' => $metadata['latitude'],
            'longitude' => $metadata['longitude'],
            'mime_type' => $metadata['mime_type'],
            'width' => $metadata['width'],
            'height' => $metadata['height'],
            'taken_at' => $metadata['taken_at'],
            'iso_speed' => $metadata['iso_speed'],
            'focal_length' => $metadata['focal_length'],
            'software' => $metadata['software'],
            'additional_metadata' => $metadata['additional_metadata'],
        ]);

        return response()->json([
            'path' => $fileName,
            'metadata' => $metadata,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    private function sanitizeExifData($data)
    {
        array_walk_recursive($data, function (&$item, $key) {
            if (is_string($item)) {
                $item = mb_convert_encoding($item, 'UTF-8', 'UTF-8');
                if (!mb_check_encoding($item, 'UTF-8')) {
                    $item = utf8_encode($item);
                }
            }
        });

        return $data;
    }

    private function getGps($exifCoord, $hemi)
    {
        if (!$exifCoord) {
            return null;
        }

        $degrees = count($exifCoord) > 0 ? $this->convertToDecimal($exifCoord[0]) : 0;
        $minutes = count($exifCoord) > 1 ? $this->convertToDecimal($exifCoord[1]) : 0;
        $seconds = count($exifCoord) > 2 ? $this->convertToDecimal($exifCoord[2]) : 0;

        $flip = ($hemi == 'W' or $hemi == 'S') ? -1 : 1;

        return $flip * ($degrees + ($minutes / 60) + ($seconds / 3600));
    }

    private function convertToDecimal($value)
    {
        $parts = explode('/', $value);
        if (count($parts) == 1) {
            return floatval($parts[0]);
        } elseif (count($parts) == 2 && $parts[1] != 0) {
            return floatval($parts[0]) / floatval($parts[1]);
        } else {
            return 0;
        }
    }
}
