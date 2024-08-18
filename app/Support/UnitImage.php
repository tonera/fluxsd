<?php
namespace App\Support;
use Error;
use Exception;
use GuzzleHttp\Client;
use Intervention\Image\ImageManager;

//image process unit
class UnitImage{
    public $manager;
    public $image;
    public $meta = [];

    public function __construct($engine = 'imagick')
    {
        if($engine == 'imagick'){
            $this->manager = ImageManager::imagick();
        }else{
            $this->manager = ImageManager::gd();
        }
    }

    public function loadImage($imageObject)
    {
        if (is_string($imageObject) && filter_var($imageObject, FILTER_VALIDATE_URL)){
            $imageObject = self::getBytesContentFromUrl($imageObject);
        }
        $this->image = $this->manager->read($imageObject);
        $size = $this->image->size();
        $this->meta = [
            'width' => $size->width(),
            'height' => $size->height(),
            'ratio' => $size->aspectRatio(),
        ];
        return $this;
    }
    public function getExif()
    {
        return $this->image->exif();
    }

    public function output(string $format = 'jpeg', int $quality = 90)
    {
        switch(strtolower($format)){
            case 'png':
                return $this->image->toPng();
            case 'webp':
                return $this->image->toWebp($quality);
            default:
                return $this->image->toJpeg($quality);
        }
    }

    //scale image
    public function scale(string $format = 'jpeg', int $quality = 90, int $width = null, int $height = null)
    {
        $this->image->scale(width: $width,height: $height);
        return $this->output($format, $quality);
    }

    //resize image for suitable sd
    public function resizeSd(array $dimensions, string $format = 'jpeg', int $quality = 90)
    {
        $orgRatio = $this->image->width() / $this->image->height();
        $minIdx = 0;
        $min = 10;
        foreach($dimensions as $index => $item){  
            $asp = $item['width']/$item['height'];
            $diff = abs($asp - $orgRatio);
            //echo "asp=".$asp ." / "."diff=".$diff." / min=".$min." / minindex=".$minIdx."\n";
            if($diff <= $min) {
                $minIdx = $index;
                $min = $diff;
            }
        }
        $newRatio = $dimensions[$minIdx];
        var_dump($newRatio);
        $this->image->cover($newRatio['width'],$newRatio['height']);
        if($this->image->height() != $newRatio['height'] || $newRatio['width'] != $this->image->width() ){
            $this->image->resize(width: $newRatio['width'],height: $newRatio['height']);
        }
        return $this->output($format, $quality);
    }

    //get bytes from url
    public static function getBytesContentFromUrl(string $url)
    {
        try {
            $client = new Client();
            $imgBinContent = $client->get($url)->getBody()->getContents();
            return $imgBinContent;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }catch (Error $e) {
            throw new Exception($e->getMessage());
        }
        
    }
}


?>