<?php
namespace App\Support;

class GlobalCode {

    const SUCCESS = 1;          
    const REPEATED = 10108;     //repeate operation
    const RELOGIN = 20000;      //token invilid
    const ERROR = 30000;        //error
    const QUEUE_CMD = 'atz.cmd';//redis command channel
    const CLIENT_HEARTBEAT_KEY = 'C_heartbeat'; //record ai generator last online time

    const MJ = 'mj';
    const SD = 'sd';
    const ATZ = 'atz';
    const LC = 'lc';
    public static function getEngingList(){
        return [
            self::MJ,
            self::SD,
            self::ATZ,
            self::LC,
        ];
    }

    const EXP_DOWNLOAD = 'DL';
    const EXP_MAKE = 'MK';
    const EXP_SR = 'SR';
    const EXP_RBG = 'RBG';
    const EXP_PT = 'PT';
    const EXP_APT = 'APT';
    const EXP_FS = 'FS';
    const EXP_VD = 'VD';
    
    const CHANNEL_LIST = [
        self::EXP_SR => 'atz.sr',
        self::EXP_RBG => 'atz.rbg',
        self::EXP_PT  => 'atz.pt',
        self::EXP_MAKE  => 'atz.mk',
        self::EXP_APT  => 'atz.mk',
        self::EXP_FS => 'atz.fs',
        self::EXP_VD => 'atz.vd',
        'ATZ-RES' => 'atz.res',
        'ATZ-ERROR' => 'atz.error',
    ];

    CONST TASK_CREATE = 0;//generated
    CONST TASK_QUEUED = 10;//quened
    CONST TASK_RUNNING = 20; //running
    CONST TASK_FAILED = 25;//failed
    CONST TASK_SUCCESS = 30;//success

    CONST MODEL_SYNC = 0;//cant download
    CONST MODEL_CANLOAD = 3;//can download
    CONST MODEL_LOADING = 5;//downloading
    CONST MODEL_FAILED = 7;//download failed
    CONST MODEL_DOWNLOADED = 10;//downloaded

    CONST WS = 'WS';
    CONST COMMON = 'common';

    public static function getChannel($jobType){
        return isset(self::CHANNEL_LIST[$jobType]) ? self::CHANNEL_LIST[$jobType] : 'error';
    }
}
