<?php
namespace App\Support;

use App\Events\TaskMessage;


// ReverbClient 
class ReverbClient
{
    public static function sendMessage($message){
        TaskMessage::dispatch($message);
    }
    
}
?>

