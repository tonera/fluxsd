<?php

use App\Models\Task;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

Broadcast::channel('channel_for_everyone', function ($user) {
    return true;
});
Broadcast::channel('channel_task.{id}', function ($user, $id) {
    // return (int) $user->id === (int) $id;
    // Log::write("info", "channel_task task_id = {$id} user id = {$user->id}");
    $task = Task::where('task_id', $id)->where('user_id', $user->id)->first();
    return $task != null;
    // return (int) $user->id === (int) $id;
    // return true;

});