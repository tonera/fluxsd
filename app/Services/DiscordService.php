<?php
namespace App\Services;

use App\Support\Alogd;
use App\Support\IDGenerator;
use App\Support\GlobalCode;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Config;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Exception;
use stdClass;

class DiscordService {
    private static $apiUrl = 'https://discord.com/api/v9';
    private static $channelId = '';
    private static $guildId = '';
    private static $userId = '';
    private static $appId = '';
    private static $authtoken = '';
    private static $botToken = '';
    private static $clientId = '';
    private static $clientSecret = '';
    private static $client = null;
    private static $accessToken = '';

    public function __construct()
    {
        self::$authtoken = Common::getConfigKeyValue('engine.mj.token');
        self::$botToken = Env::get('BotToken');
        self::$clientId = Env::get('ClientId');
        self::$clientSecret = Env::get('ClientSecret');
        self::$channelId = Common::getConfigKeyValue('engine.mj.channel_id');
        self::$guildId = Common::getConfigKeyValue('engine.mj.guild_id');
        self::$userId = Common::getConfigKeyValue('engine.mj.user_id');
        self::$appId = Common::getConfigKeyValue('engine.mj.app_id');
        self::$client = new Client([
            'base_uri' => self::$apiUrl,
            'headers' => [
                'Authorization' => self::$authtoken,
                'Content-Type' => 'application/json',
                //'Authorization' => 'Bot '.self::$botToken
            ]
        ]);
    }

    /**
     *
     * @param array $params
     * @return 
     */
    public function generate(String $prompt){
        $dataId = '938956540159881230';
        $iid = IDGenerator::id();
        $sessionId = md5($iid);
        $versionId = '1237876415471554623';
        $payload = [
            "type"=>2,
            "application_id"=>self::$appId,
            "guild_id"=>self::$guildId,
            "channel_id"=>self::$channelId,
            "session_id"=>$sessionId,
            "data"=>[
                "version"=> $versionId,//$versionId,
                "id"=>$dataId,
                "name"=>"imagine",
                "type"=>1,
                "options"=>[
                    [
                        "type"=>3,
                        "name"=>"prompt",
                        "value"=>$prompt
                    ]
                ],
                "application_command"=>[
                    "id"=>$dataId,
                    "type"=>1,
                    "application_id"=>self::$appId,
                    "version"=> $versionId,
                    "name" => "imagine",
                    // "default_member_permissions"=>null,
                    "description"=>"Create images with Midjourney",
                    "options"=>[
                        [
                            "type"=>3,
                            "name"=>"prompt",
                            "description"=>"The prompt to imagine",
                            "required"=>true,
                            "description_localized"=>"The prompt to imagine",
                            "name_localized"=>"prompt"
                        ]
                    ],
                    'dm_permission'=> true,
                    'contexts' => [
                        0,1,2,
                    ],
                    "integration_types"=>[
                        0,
                        1
                    ],
                    "global_popularity_rank"=>1,
                    "description_localized"=>"Create images with Midjourney",
                    "name_localized"=>"imagine",

                    
                    // "nsfw"=>false,
                    // "name"=>"imagine",
                    
                    // "dm_permission"=>true,
                    // "contexts"=>null,
                    
                    
                ],
                "attachments"=>[]
            ],
            "nonce"=>IDGenerator::id(),
        ];
        Alogd::write(GlobalCode::MJ, $payload);
        self::$client->post('interactions', [
            RequestOptions::JSON => $payload
        ]);
    }

    /**
     *
     * @return array
     */
    public function getMessages($limit){
        $res = self::$client->get('channels/' . self::$channelId . '/messages', [
            'query' => [
                'limit'=>$limit,
            ],
        ]);
        $response = json_decode((string) $res->getBody());
        $items = [];
        foreach($response as $val){
            $items[] = [
                'id' => $val->id,
                'content' => $val->content,
                'attachments' => $val->attachments,
                'components' => $val->components,
                'message_reference' => $val->message_reference??null,
            ];
        }
        return $items;
    }

    /**
     *
     * @param array $params [image_model,index,[session_id]]
     * @return void
     */
    public function  change(Object $imageModel, String $act){
        $actTable = Config::get('discord');
        $sessionId = md5(microtime(true));
        if(!isset($actTable[$act])){
            throw new Exception('Not found the index'. $act, 10006);
        }
        $label = $actTable[$act]['label'];
        $type = 'label';
        if(in_array($act, ['reroll','left','right','up','down'])){
            $type = 'emoji';
            $label = $actTable[$act]['emoji'];
        }else{
            $type = 'label';
            $label = $actTable[$act]['label'];
        }
        $res = self::searchCustomId($imageModel->components, $type,$label);
        if($res == null){
            throw new Exception('Not found the act'. $act, 10006);
        }
   
        $payload = [
            'type' => 3,
            'guild_id' => self::$guildId,
            'channel_id' => self::$channelId,
            'message_flags' => 0,
            'message_id' => $imageModel->message_id,
            'application_id' => self::$appId,
            'session_id' => $sessionId,
            'data' => [
                'component_type' => 2,
                'custom_id' => $res[0]
            ]
        ];
        Alogd::write(GlobalCode::MJ, $payload);
        self::$client->post('interactions', [
            'json' => $payload
        ]);
    }

    public static function searchCustomId(array $components, string $type = 'label', $key)
    {
        foreach($components as $item){
            
            foreach($item->components as $val){
                if($type == 'label'){
                    if(isset($val->label) && ($val->label == $key || $val->label == 'Redo '.$key)){
                        return [$val->custom_id, $val->label];
                    }
                }
                if($type == 'emoji'){
                    if(isset($val->emoji->name) && $val->emoji->name == $key){
                        return [$val->custom_id, $val->emoji->name];
                    }
                }
            }
        }
    }

    public static function makeContentString($actTable , $prompt , $act, $userId, $type = 'ephemeral', $mode = true){
        $prompt = self::clearParams($prompt, $act);
        
        if($act != 'create' && $act != 'MK'){
            $patterns = ['/(\s--seed[\s][\d]+)/i'];
            $prompt = preg_replace($patterns, '', $prompt);
        }
        if($mode === false){
            return $prompt;
        }
        
        $waitStartString = "**{$prompt}** - <@{$userId}> (Waiting to start)";
        $prefix = "";

        $index = isset($actTable[$act])?$actTable[$act]['idx']:0;
        $index+=1;
        if($type === 'ephemeral'){
            switch($act){
                case 'u1':
                case 'u2':
                case 'u3':
                case 'u4':
                    $prefix = "Upscaling image #{$index} with ";
                    break;
                case 'us2':
                case 'us4':
                    $prefix = "Upscaling image #1 with ";
                    break;
                case 'v1':
                case 'v2':
                case 'v3':
                case 'v4':
                    $prefix = "Making variations for image #{$index} with prompt ";
                    break;
                case 'vst':
                case 'vsu':
                    $prefix = "Making variations for image #1 with prompt ";
                    break;
                default:
                    $prefix = "";
            }
            return $prefix.$waitStartString;
        }else{
            $first = "**{$prompt}** -";
            $third = " <@{$userId}>";
            $forth = " (fast)";
            switch($act){
                case 'create':
                    $second = "";
                    break;
                case 'u1':
                case 'u2':
                case 'u3':
                case 'u4':
                    $second = " Image #{$index}";
                    $forth = "";
                    break;
                case 'v1':
                case 'v2':
                case 'v3':
                case 'v4':
                case 'vst':
                    $second = " Variations (Strong) by";
                    break;
                case 'vsu':
                    $second = " Variations (Subtle) by";
                    break;
                case 'left':
                case 'right':
                case 'up':
                case 'down':
                    $second = " Pan ".ucfirst($act)." by";
                    break;
                case 'us2':
                    $second = " Upscaled (2x) by";//** - Upscaled (2x) by <@1128618388508393602> (fast)  
                    break;
                case 'us4':
                    $second = " Upscaled (4x) by";
                    break;
                case 'zo2':
                case 'zo1':
                    $second = " Zoom Out by";
                    break;
                case 'reroll':
                    $second = " Variations (Strong) by";
                    break;
                case 'reroll2':
                    $second = "";
                    break;
                case 'square':
                    $second = " Zoom Out by";
                    break;
                default:
                    $second = "";
            }
            return $first.$second.$third.$forth;
        }
    }

    /**
     *
     * @param [type] $content
     * @return array
     */
    public static function getPrompt($content){
        preg_match('/\*\*(.*?)\*\*/i', $content, $matchs);
        $prompt = $matchs[1]??'';
        $contents = explode('--', $prompt);
        return $contents;
    }

    /**
     *
     * @param string $prompt
     * @param array $params
     * @param array $images
     * @return array
     */
    public static function formatPrompt(String $prompt, Array $params, Array $images = []){
        $aspects = Config::get('aspects.omratio');
        $item = isset($aspects[$params['asp_id']]) ? $aspects[$params['asp_id']] : $aspects[0];

        $imageString = '';
        if(count($images) > 0){
            $imageString = implode(' ', $images);
            //--iw <0–2> Sets image prompt weight relative to text weight. The default value is 1.
            $prompt .= " --iw ".intval($params['denoising_strength']);
        }

        $prompt .= " --ar ".$item['label'];
        //--chaos <number 0–100> Change how varied the results will be. Higher values produce more unusual and unexpected generations.

        if($params['negative_prompt_en'] != ''){
            $prompt .= " --no ".$params['negative_prompt_en'];
        }
        $seed = (int)$params['seed'] < 0 ?0:$params['seed'];
        $seed = $seed > 4294967295 ? 4294967295 :$seed;
        $prompt .= " --seed ".$seed;
 
        if(isset($params['niji']) && !empty($params['niji'])) {
            $prompt .= " --niji";
            //--niji 5 --style cute
            $prompt .= $params['niji']=='normal'?'':' 5 --style '.$params['niji'];
        }
        $prompt .= " --s ".$params['cfg_scale'];

        //--weird <number 0–3000>, or --w <number 0–3000> Explore unusual aesthetics with the experimental --weird parameter.
        if(isset($params['weird']) && !empty($params['weird'])){
            $prompt .= " --weird ".intval($params['weird']);
        }
        return [$imageString, trim($prompt)];
        // return trim($imageString.' '.trim($prompt));
    }

    public static function clearParams(String $prompt, $act = 'create'){
        $prompt = preg_replace('/\s+/', ' ', $prompt);
      
        $tmp = explode(' --', $prompt);
        $newPrompt = isset($tmp[0])?trim($tmp[0]):'';
        if($newPrompt == '') return '';

        //preg_match_all('/(\\s--(.*?))[^--]*/i', $prompt, $matchs, PREG_SET_ORDER);
        $matchs = [];
        $tmp = explode(' --', $prompt);
        foreach($tmp as $val){
            $matchs[] = preg_split('/\s+/', $val,2);
        }

        $commands = [];
        $commandAllowed = ['--aspect','--ar', '--chaos', '--fast', '--iw', '--no', '--quality','--q', '--relax','--repeat', '--seed', '--stop','--style','--tile','--turbo', '--weird','--niji','--version', '--s', '--stylize'];
        $nijiAllowed = ['cute' ,'expressive', 'original', 'scenic'];
        foreach($matchs as $val){
            $command = '--'.$val[0];
            $param = isset($val[1])? trim($val[1]):'';
            if(!in_array($command, $commandAllowed)){
                continue;
            }
            if($command == '--style'){
                if(strstr($prompt , '--niji') === false){
                    if($param != 'raw'){
                        continue;
                    }
                }else{
                    if(!in_array($param , $nijiAllowed)){
                        continue;
                    }
                }
            }

            if($command == '--niji'){
                $param = 5;
            }
            if($param == 100 && in_array($command, ['--s', '--stylize'])){
                continue;
            }
            if($param == 1 && in_array($command, ['--q', '--quality', '--iw'])){
                continue;
            }
            if(!in_array($param, [1,0.25,0.5,'.25','.5','1']) && in_array($command, ['--q', '--quality'])){
                continue;
            }
            if(($act != 'create' && $act != 'MK') && in_array($command, ['--fast', '--seed']) ){
                continue;
            }
            $commands[$command] = $param == ''?$command:$command.' '. $param;
        }
        //echo "\n\n".implode(' ', $commands)."\n\n";
        return count($commands) > 0 ? $newPrompt. ' ' .implode(' ', $commands):$newPrompt;
    }

    public static function deleteUrlString(String $content){
        $patterns = array();
        $patterns[0] = '/<http[^\s]*[\s$]/';
        $patterns[1] = '/http[^\s]*[\s$]/';
        $replacements = array();
        $replacements[1] = '';
        $replacements[0] = '';
        return trim(preg_replace($patterns, '', $content));
    }


    /**
     *
     * @param object $component
     * @return array
     */
    public static function getButtonGroups($components){
        if($components == null){
            return[];
        }
        $discordButtons = Config::get('discord');
        $actTableRules = [];
        foreach($discordButtons as $act => $val){
            $key = !empty($val['label'])?$val['label']:$val['emoji'];
            $actTableRules[$key] = ['act'=> $act, 'index' => $val['row'],'cLabel' => $val['cLabel']];
        }
        $actTableRules['Redo Upscale (2x)'] = ['act'=> 'us2', 'index' => 0];
        $actTableRules['Redo Upscale (4x)'] = ['act'=> 'us4', 'index' => 0];
        $data = [];
        foreach($components as $k => $item){
            foreach($item->components as $val){
                $key = (isset($val->label) && $val->label != null)?$val->label:$val->emoji->name;
                if(!isset($actTableRules[$key])){
                    continue;
                }
                $index = $actTableRules[$key]['index'];
                $cLabel = $actTableRules[$key]['cLabel'];
                $data[$index][] = ['act'=> $actTableRules[$key]['act'], 'label' => $cLabel,'api'=>'upscale'];
            }
        }
        return $data;
    }

    public function getUserid(){
        try{
            $request = self::$client->get('users/@me');
            $response = json_decode((string) $request->getBody());
            self::$userId = $response->id;
            return $response;
        }catch(Exception $e){
            return null;
        }
    }

    /**
     *
     * @return string eg.{"token_type": "Bearer", "access_token": "", "expires_in": 604800, "scope": "identify connections"}
     */
    public function getBotToken(){
        $data = [
            'grant_type' => 'client_credentials',
            'scope' => 'identify connections'
        ];
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];
        try{
            $res = self::$client->post('oauth2/token', [
                'headers' => $headers,
                'form_params' => $data,
                'auth' => [self::$clientId, self::$clientSecret],
            ]);
            $json = json_decode($res->getBody()->getContents());
            self::$accessToken = $json->access_token;
            return $json;
        }catch(Exception $e){
            return null;
        }
    }

    public function getChannelMessage($messageId){
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bot '. self::$botToken
        ];
        try{
            $res = self::$client->get('channels/' . self::$channelId . '/messages/'.$messageId, [
                'headers' => $headers,
            ]);
            $message = json_decode((string) $res->getBody()->getContents());
            Alogd::write(GlobalCode::MJ, "get message from discord:".json_encode($message));
            $items = new stdClass;
            $items->id = $message->id;
            $items->content = $message->content;
            $items->attachments = $message->attachments;
            $items->components = $message->components;
            return $items;
        }catch(Exception $e){
            Alogd::write(GlobalCode::MJ, "get message from discord failed:".$e->getMessage());
            return null;
        }
    }

    public function getOriginalMessage($messageId){
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bot '. self::$botToken
        ];
        try{
            $res = self::$client->get('channels/' . self::$channelId . '/messages/'.$messageId, [
                'headers' => $headers,
            ]);
            $message = json_decode((string) $res->getBody()->getContents());
            Alogd::write(GlobalCode::MJ, "get message from discord:".json_encode($message));
            return $message;
        }catch(Exception $e){
            Alogd::write(GlobalCode::MJ, "get message from discord failed:".$e->getMessage());
            return null;
        }
    }

    protected static function firstWhere(Array $list, String $prompt, String $userIdInfo){
        foreach($list as $item){
            $matchs = [];
            preg_match('/\*\*[{]?(.+?)[}]?\*\*/i', $item->content, $matchs);
            if(!isset($matchs[1])) continue;
            if(strstr($prompt, $matchs[1]) != false && strstr($item->content, $userIdInfo)) {
                return $item;
            }
        }
        return null;
    }

    public static function parseProgressInfo(String $content, Array $attachments){
        if (str_contains($content, '%') || count($attachments) == 0) {
            preg_match('/([0-9]+)\%/i', $content, $matchs);
            $progress = isset($matchs[1]) ? $matchs[1] : 0;
        } else {
            $progress = 50;
        }
        return $progress;
    }

    protected static function firstParent(Array $list, String $messageId){
        foreach ($list as $item){
            if (isset($item->message_reference) && $item->message_reference->message_id == $messageId){
                return $item;
            }
        }
        return null;
    }

    /**
     * discord uploadImage https://discord.com/developers/docs/reference#uploading-files
     * @param string binary $imgData
     * @return void
     */
    public function uploadImage($imgData){
        $formData = array('files[0]' => new \CURLFile('/Users/zhangtao/fotiaoqiang/imagestext.jpeg', 'image/jpeg', 'files[0].jpeg'));
        $formData = [
            'files[0]' => new \CURLStringFile($imgData, 'me.jpeg', 'image/jpeg'),
        ];
        $apiHost = self::$apiUrl .'/channels/'. self::$channelId .'/messages';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $apiHost);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // stop verifying certificate
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($curl, CURLOPT_POST, true); // enable posting
        curl_setopt($curl, CURLOPT_POSTFIELDS, $formData); // post images 
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); // if any redirection after upload
        curl_setopt($curl, CURLOPT_HTTPHEADER, [ 
            "Content-Type: multipart/form-data",
            "Authorization: ".self::$authtoken,
        ]); 
        $r = curl_exec($curl); 
        curl_close($curl);
        return json_decode($r, true);
    }
    
}
