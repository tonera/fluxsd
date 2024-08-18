<?php

namespace App\Listeners;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
 
class QueryListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(QueryExecuted $event): void
    {
        try{
            if (env('APP_DEBUG') == true) {
                $this->recordLog($event, 'logs/sql/sql.log');
            }
        }catch (\Exception $exception){
            Log::error('log sql error:'.$exception->getMessage());
        }
    }
    protected function recordLog($event, $sqlFile)
    {
        $sql = str_replace("?", "'%s'", $event->sql);
        foreach ($event->bindings as $i => $binding) {
            if ($binding instanceof \DateTime) {
                $event->bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
            } else {
                if (is_string($binding)) {
                    $event->bindings[$i] = "'$binding'";
                }
            }
        }
        $log = vsprintf($sql, $event->bindings);
        $log = $log.'  [ RunTime:'.$event->time.'ms ] ';
        (new Logger('sql'))->pushHandler(new RotatingFileHandler(storage_path($sqlFile)))->info($log);
    }
}
