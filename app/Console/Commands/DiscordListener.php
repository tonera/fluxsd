<?php

namespace App\Console\Commands;

use App\Jobs\MjProgress;
use App\Models\Task;
use App\Services\Common;
use App\Services\DiscordService;
use App\Support\Alogd;
use App\Support\GlobalCode;
use App\Support\ReverbClient;
use Illuminate\Console\Command;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Intents;
use Discord\WebSockets\Event;
use Illuminate\Support\Carbon;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Config;

use stdClass;

class DiscordListener extends Command
{
    protected $signature = 'DiscordListener';
    protected $description = 'listen discord and forward to user';
    public static $isOutput = true;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $token = Common::getConfigKeyValue('engine.mj.token')??'';
        if(!$token){
            echo "Token is null , wait 5 s!", PHP_EOL;
            sleep(5);
            return;
        }
        $discord = new Discord([
            'token' => $token,
            'intents' => Intents::getDefaultIntents()
        //      | Intents::MESSAGE_CONTENT, // Note: MESSAGE_CONTENT is privileged, see https://dis.gd/mcfaq
        ]);

        $discord->on('ready', function (Discord $discord) {
            echo "Bot is ready!", PHP_EOL;
            // Listen for messages.
            $discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
                Alogd::write(GlobalCode::MJ, "Recived from discord:id={$message->id}", self::$isOutput);
                $package = json_encode($message, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
                // Alogd::write(GlobalCode::MJ, "package={$package}", self::$isOutput);
                // error_log($package, 3, '/Users/zhangtao/website/tuse-aibox/storage/logs/discord.json');

                //for testing
                // Cache::put($message->id, $package);

                $this->matchMessage(json_decode($package));
                // Note: MESSAGE_CONTENT intent must be enabled to get the content if the bot is not mentioned/DMed.
            });

            // $discord->on(Event::INTERACTION_CREATE, function (Interaction $interaction, Discord $discord) {
            //     Log::write('Interaction is created:'.json_encode($interaction));
            // });

        });
        $discord->run();
    }

    //Against?
    public static function getAgainst($message){
        if(empty($message->embeds)){
            return null;
        }
        Alogd::write(GlobalCode::MJ, 'message->content is null .message='.json_encode($message), self::$isOutput);
        $list = is_array($message->embeds)?$message->embeds:get_mangled_object_vars($message->embeds);
        $firstKey = array_key_first($list);
        $embeds = $list[$firstKey];
        if($embeds != null){
            $prompt = trim(str_replace('/imagine', '', $embeds->footer->text));
            $list = Task::where('work_status',0)->where('engine', GlobalCode::MJ)->orderBy('created_at', 'desc')->limit(50)->get();
            foreach($list as $val){
                if(strstr($val->prompt_en, $prompt) === false){
                    continue;
                }else{
                    return $val;
                }
            }
            return null;
        }else{
            return null;
        }
    }
 
    public static function matchMessage($message, bool $isHack = false){
        if($message == null){
            Alogd::write(GlobalCode::MJ, 'message为空,消息处理停止' , self::$isOutput);
            return;
        }
        if(!is_object($message)){
            Alogd::write(GlobalCode::MJ, 'message 不是合法的json对象,消息停止处理', self::$isOutput );
            return;
        }
        if($message->content === ''){
            $against = self::getAgainst($message);
            if($against === null){
                Alogd::write(GlobalCode::MJ, 'GetAgainst返回消息为空,不做任何处理,消息停止处理:msg='.json_encode($message) , self::$isOutput);
                return;
            }else{
                $sendData = [
                    'message_type' => 'failed',
                    'user_id' => $against->user_id,
                    'task_id' => $against->task_id,
                    'msg' => __('Request have been rejected, please check for inappropriate prompt'),
                ];
                Alogd::write(GlobalCode::MJ, '用户提交prompt被拒绝,消息停止处理.embeds='.json_encode($message->embeds) , self::$isOutput);
                //写入缓存，以便于前端再次连接时直接返回结果
                $msgCacheContent = json_encode($sendData);
                $messageKey = Config::get('public.aires_key').$against->task_id;
                cache([$messageKey => $msgCacheContent], now()->addMinutes(30));
                ReverbClient::sendMessage($sendData); 
                return;
            }
        }     
        if(strstr($message->content, '**') === false){
            Alogd::write(GlobalCode::MJ, "非图片消息,消息停止处理.{$message->author->username}: {$message->id}" , self::$isOutput);
            Alogd::write(GlobalCode::MJ, $message, self::$isOutput);
            return;//非图片消息不处理
        }
        
        $type = strstr($message->content, 'Waiting to start') === false?'standing':'ephemeral';
     
        $status = [GlobalCode::TASK_CREATE,GlobalCode::TASK_QUEUED,GlobalCode::TASK_FAILED,GlobalCode::TASK_RUNNING];
        // $limitTime = time() - 3600;//1小时内的消息
        $limitTime = Carbon::now()->subHours(1)->toDateTimeString();
        //从服务端读取最近的100条消息进行匹配
        $list = Task::whereIn('work_status',$status)->where('created_at','>=', $limitTime)->where('engine', GlobalCode::MJ)->orderBy('created_at', 'desc')->limit(100)->get();

        $refId = empty($message->message_reference)?'':$message->message_reference->message_id;
        Alogd::write(GlobalCode::MJ, "数据库里待匹配的记录数:".count($list) , self::$isOutput);

        //数据库数据与接口数据开始匹配
        $task = self::getTaskIdByContent($list, $message->content, $type, $refId);
        if(empty($task)){
            Alogd::write(GlobalCode::MJ, "数据库里没有找到匹配的记录,消息停止处理." , self::$isOutput);
            return;
        }
  
        // $task->message_id = $message->id;
        Alogd::write(GlobalCode::MJ, "消息匹配成功:task_id=".$task->task_id." 当前message_id=".$task->message_id." work_status=".$task->work_status , self::$isOutput);

        $pkg = new stdClass;
        $task->work_status = GlobalCode::TASK_RUNNING;
        if($type == 'standing'){
            $pkg->message_id = $message->id;
            $pkg->content = $message->content;
            $pkg->attachments = $message->attachments;
            $pkg->components = $message->components;
            $pkg->ref_message_id = $refId;
            $task->task_pkg = json_encode($pkg);
            // $task->work_status = GlobalCode::TASK_SUCCESS;
        }
        $task->save();
        MjProgress::dispatch($task, $type );
    }

    /**
     * 将本地消息拼成content与消息比较，返回task
     *
     * @param array $list
     * @param string $content
     */
    public static function getTaskIdByContent($list, $content, $type, $refId = ''){
        $user_id = Common::getConfigKeyValue('engine.mj.user_id');
        //返回的图片url需要删除才能被匹配到 eg.<https://s.mj.run/_A3yW21tPL0> $prompt...
        $clearContent = DiscordService::deleteUrlString($content);
        Alogd::write(GlobalCode::MJ, "Discord content=".$clearContent , self::$isOutput);
        $actTable = Config::get('discord');
        $ret = '';
        //如果有reroll操作，增加一个reroll2检测点(因为dis返回值有时不带vst)
        $rerollList = [];
        foreach($list as $val){
            $act = empty($val->act)?'create': $val->act;
            $matchString = DiscordService::makeContentString($actTable, $val->prompt_en, $act,$user_id,$type);
            Alogd::write(GlobalCode::MJ, "db content=".$matchString , self::$isOutput);
            echo "Current: act={$act} | type={$type}\n";
            if(strstr($clearContent, $matchString) !== false){
                return $val;
            }
            if($val->act == 'reroll'){
                $val->act = 'reroll2';
                $rerollList[] = $val;
            }
        }

        foreach($rerollList as $val){
            $matchString = DiscordService::makeContentString($actTable, $val->prompt_en, 'reroll2',$user_id,$type);
            Alogd::write(GlobalCode::MJ, "当前: act=reroll2 | type={$type} 比较content匹配值=".$matchString , self::$isOutput);
            echo "当前: act=reroll2 | type={$type}\n";
            if(strstr($clearContent, $matchString) !== false){
                return $val;
            }
        }

        foreach($list as $val){
            $act = empty($val->act)?'create': $val->act;
            $matchString = DiscordService::makeContentString($actTable, $val->prompt_en, $act,$user_id,$type, false);
            Alogd::write(GlobalCode::MJ, "模糊匹配模式:db content=".$matchString , self::$isOutput);
            //echo "当前: act={$act} | type={$type}\n";
            if(strstr($clearContent, $matchString) !== false){
                return $val;
            }
        }

        return $ret;
    }

    
}
