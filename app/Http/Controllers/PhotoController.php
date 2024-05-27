<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Http\Request;

class PhotoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:20480', // 20480 KB = 20 MB
        ]);

        $file = $request->file('file');
        $path = $file->store('photos', 'public');

        // Get the file path
        $filePath = storage_path('app/public/' . $path);

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
            'file_path' => $path,
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
            'path' => $path,
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
