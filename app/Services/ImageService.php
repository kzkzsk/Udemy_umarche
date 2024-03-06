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

        Storage::putFile('public/' . $folderName . '/', $imageFile); // リサイズなしの場合

        return $imageFile;
    }
}
