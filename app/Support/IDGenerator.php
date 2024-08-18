<?php

/*
 * This file is part of the godruoyi/php-snowflake.
 *
 * (c) Godruoyi <g@godruoyi.com>
 *  update by tonera
 *
 * This source file is subject to the MIT license that is bundled.
 */
namespace App\Support;

class IDGenerator
{
    const MAX_TIMESTAMP_LENGTH = 41;

    const MAX_DATACENTER_LENGTH = 5;

    const MAX_WORKID_LENGTH = 5;

    const MAX_SEQUENCE_LENGTH = 12;

    const MAX_FIRST_LENGTH = 1;

    /**
     * The data center id.
     *
     * @var int
     */
    protected static $datacenter;

    /**
     * The worker id.
     *
     * @var int
     */
    protected static $workerid;

    /**
     * The Sequence Resolver instance.
     *
     * @var \Godruoyi\Snowflake\SequenceResolver|null
     */
    protected static $sequence;

    /**
     * The start timestamp.
     *
     * @var int
     */
    protected static $startTime;

    /**
     * The las ttimestamp.
     *
     * @var null
     */
    protected static $lastTimeStamp = -1;

    /**
     * The sequence.
     *
     * @var int
     */
    protected static $sequenceInt = 0;

    /**
     * Build Snowflake Instance.
     *
     * @param int $datacenter
     * @param int $workerid
     */
    public function __construct(int $datacenter = null, int $workerid = null)
    {
        $maxDataCenter = -1 ^ (-1 << self::MAX_DATACENTER_LENGTH);
        $maxWorkId = -1 ^ (-1 << self::MAX_WORKID_LENGTH);

        // If not set datacenter or workid, we will set a default value to use.
        self::$datacenter = self::$datacenter > $maxDataCenter || self::$datacenter < 0 ? mt_rand(0, 31) : self::$datacenter;
        self::$workerid = self::$workerid > $maxWorkId || $workerid < 0 ? mt_rand(0, 31) : self::$workerid;
    }

    /**
     * Get snowflake id. 生成18位数字
     *
     * @return string
     */
    public static function id()
    {
        $currentTime = self::getCurrentMicrotime();
        while (($sequence = self::callResolver($currentTime)) > (-1 ^ (-1 << self::MAX_SEQUENCE_LENGTH))) {
            usleep(1);
            $currentTime = self::getCurrentMicrotime();
        }

        $workerLeftMoveLength = self::MAX_SEQUENCE_LENGTH;
        $datacenterLeftMoveLength = self::MAX_WORKID_LENGTH + $workerLeftMoveLength;
        $timestampLeftMoveLength = self::MAX_DATACENTER_LENGTH + $datacenterLeftMoveLength;

        $id = (string) ((($currentTime - self::getStartTimeStamp()) << $timestampLeftMoveLength)
            | (self::$datacenter << $datacenterLeftMoveLength)
            | (self::$workerid << $workerLeftMoveLength)
            | ($sequence));
        return str_pad(substr($id, -12), 18, date("ymd"), STR_PAD_LEFT);
    }

    /**
     * Parse snowflake id.
     *
     * @param string $id
     *
     * @return array
     */
    public function parseId(string $id, $transform = false): array
    {
        $id = decbin($id);

        $data = [
            'timestamp' => substr($id, 0, -22),
            'sequence' => substr($id, -12),
            'workerid' => substr($id, -17, 5),
            'datacenter' => substr($id, -22, 5),
        ];

        return $transform ? array_map(function ($value) {
            return bindec($value);
        }, $data) : $data;
    }

    /**
     * Get current microtime timestamp.
     *
     * @return int
     */
    public static function getCurrentMicrotime()
    {
        return floor(microtime(true) * 1000) | 0;
    }

    /**
     * Set start time (millisecond).
     *
     * @param int $startTime
     */
    public static function setStartTimeStamp(int $startTime)
    {
        $missTime = self::getCurrentMicrotime() - $startTime;
        if ($missTime < 0 || $missTime > ($maxTimeDiff = ((1 << self::MAX_TIMESTAMP_LENGTH) - 1))) {
            throw new \Exception('The starttime cannot be greater than current time and the maximum time difference is ' . $maxTimeDiff);
        }

        self::$startTime = $startTime;

        return self::class;
    }

    /**
     * Get start timestamp (millisecond), If not set default to 2019-08-08 08:08:08.
     *
     * @return int
     */
    public static function getStartTimeStamp()
    {
        if (self::$startTime > 0) {
            return self::$startTime;
        }

        // We set a default start time if you not set.
        $defaultTime = '2019-07-02 08:08:08';

        return strtotime($defaultTime) * 1000;
    }

    /**
     * Set Sequence Resolver.
     *
     * @param SequenceResolver|callable $sequence
     */
    public function setSequenceResolver($sequence)
    {
        $this->sequence = $sequence;

        return $this;
    }

    /**
     * Get Sequence Resolver.
     *
     * @return \Godruoyi\Snowflake\SequenceResolver|callable|null
     */
    public function getSequenceResolver()
    {
        return $this->sequence;
    }

    /**
     * Call resolver.
     *
     * @param callable|\Godruoyi\Snowflake\SequenceResolver $resolver
     * @param int                                           $maxSequence
     *
     * @return int
     */
    protected static function callResolver($currentTime)
    {
        return self::sequence($currentTime);
    }

    public static function sequence(int $currentTime)
    {
        if (self::$lastTimeStamp === $currentTime) {
            ++self::$sequenceInt;
            self::$lastTimeStamp = $currentTime;

            return self::$sequenceInt;
        }

        self::$sequenceInt = 0;
        self::$lastTimeStamp = $currentTime;

        return 0;
    }

    //生成12位字符串
    public static function ids($mode = '')
    {
        $res = strtoupper(substr(base_convert(self::id(), 10, 36), 0, 12));
        switch ($mode) {
            case 'sku':
                $res = 'K'.$res;
                break;
            case 'spu':
                $res = 'P'.$res;
            break;
            default:
                break;
        }
        return $res;
    }
}
