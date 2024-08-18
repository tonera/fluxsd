<?php
namespace App\Support;

use Exception;
use Illuminate\Support\Env;
use OSS\Core\OssException;
use OSS\OssClient;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

//storage
class UnitStorage{
    public $client;
    public $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        switch($config['storage']){
            case 'local':
                $this->client = Storage::disk('public');
                break;
            case 'oss':
                $this->client = new OssClient($config['access_key'], $config['access_secret'], $config['endpoint']);
                break;
            case 's3':
                $config = [
                    'version'     => 'latest',
                    'region'      => $config['region'],
                    'credentials' => [
                        'key'    => $config['access_key'],
                        'secret' => $config['access_secret'],
                    ],
                ];
                $this->client = new S3Client($config);  
                break;
            default:
            throw new Exception("Not found the storage:".$config['storage']);
        }
    }

    //bytes to storage
    public function putObject(string $object , string $content)
    {
        try{
            switch($this->config['storage']){
                case 'local':
                    $this->client->put( $object, $content);
                    break;
                case 'oss':
                    $this->client->putObject($this->config['bucket'] , $object, $content );
                    break;
                case 's3':
                    $obj = [
                        'Key' => $object,
                        'Body' => $content,
                        'Bucket' => $this->config['bucket'],
                    ];
                    $this->client->putObject($obj);
                    break;
            }
            return [
                'file_size' => strlen($content),
                'file_path' => $object,
            ];
        } catch(Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    //file to storage
    public function putFile(string $object , string $filePath)
    {
        try{
            switch($this->config['storage']){
                case 'local':
                    $path = $this->client->putFileAs('', new File($filePath), $object);
                    // var_dump($path);
                    break;
                case 'oss':
                    $this->client->uploadFile($this->config['bucket'] , $object, $filePath );
                    break;
                case 's3':
                    $obj = [
                        'Key' => $object,
                        'SourceFile' => $filePath,
                        'Bucket' => $this->config['bucket'],
                    ];
                    $this->client->putObject($obj);
                    break;
            }
            return [
                'file_size' => filesize($filePath),
                'file_path' => $object,
            ];
        } catch(Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    //oss
    public function downloadFile(string $object , string $localfile){
        $options = array(
                OssClient::OSS_FILE_DOWNLOAD => $localfile
            );

        try{
            $this->client->getObject($this->config['bucket'], $object, $options);
            return true;
        } catch(Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function deleteObject(string $object){
        try{
            switch($this->config['storage']){
                case 'local':
                    $this->client->delete($object);

                    break;
                case 'oss':
                    $this->client->deleteObject($this->config['bucket'], $object);
                    break;
                case 's3':
                    $this->client->deleteObject([
                        'Bucket' => $this->config['bucket'],
                        'Key' => $object,
                    ]);
                    break;
            }
            return true;
        } catch(Exception $e) {
            throw new Exception($e->getMessage());
        }
    }



  
}


?>