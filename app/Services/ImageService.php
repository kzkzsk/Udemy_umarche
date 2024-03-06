<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ImageService
{
    public static function upload($imageFile, $folderName)
    {
        //
        // 本来はここにInterventionImageのリサイズ処理を入れて、画像リサイズ処理を汎用化したい。
        //
        $fileName = uniqid((rand().'_'));
        $extension = $imageFile->extension();
        $fileNameToStore = $fileName . '.' . $extension;
        Storage::putFileAs('public/' . $folderName . '/', $imageFile, $fileNameToStore); // リサイズなしの場合

        return $fileNameToStore;
    }
}
