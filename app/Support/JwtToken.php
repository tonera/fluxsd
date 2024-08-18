<?php
namespace app;
namespace App\Support;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use stdClass;
use Exception;
use Error;

class JwtToken
{
    /**
     * 生成jwt token
     * @param $user array 用户信息  
     */
    public static function signToken($user)
    {
        $nowTime = time();
        $token = [
            'iss' => 'aitezhu', //签发者，可以为空
            'aud' => '',        //面向的用户，可以为空
            'iat' => $nowTime ,    //签发时间
            'nbf' => $nowTime ,    //生效时间
            'exp' => $nowTime + 864000,//十天
            'data' => $user,    //数据
        ];
        //var_dump(json_encode($token));
        $jwt = JWT::encode($token, self::getTokenSalt(), 'HS256');
        return $jwt;
    }

    public static function checkToken($token)
    {
        $decoded = new stdClass;
        try{
            JWT::$leeway    = 60;
            $decoded        = JWT::decode($token, new Key(self::getTokenSalt(), 'HS256'));
            $decoded->code  = 1;
            return $decoded;
        }catch(SignatureInvalidException $e){
            $decoded->code  = 10011;
            $decoded->msg   = '签名不正确';//签名不正确
            return $decoded;
        }catch(BeforeValidException $e){
            $decoded->code  = 10012;
            $decoded->msg = '10012';
            return $decoded;
        }catch(ExpiredException $e){
            $decoded->code  = 10013;
            $decoded->msg = '超时';
            return $decoded;
        }catch(Exception $e){
            $decoded->code  = 10014;
            $decoded->msg = '10014';
            return $decoded;
        }catch (Error $error) {
            $decoded->code  = 10014;
            $decoded->msg = '10014';
            return $decoded;
        }
    }

    public static function updateToken($token){
        $res = new stdClass;
        $ck = self::checkToken($token);
        if($token && $ck->code == 1 && $ck->data->user_id){
            $user = User::find($ck->data->user_id);
            if(!$user){
                $res->code = 20000;
                $res->newtoken = '';
            }else{
                $res->code = 1;
                $res->newtoken = self::generateToken($user);
            }
        }else{
            $res->code = 20000;
            $res->newtoken = '';
        }
        return $res;
    }

    public static function generateToken(object $user){
        $tokenData = [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'credit' => $user->credit,
        ];
        return self::signToken($tokenData);
    }

    public static function getTokenSalt():string
    {
        return env('JwtToken');
    }
}
?>

