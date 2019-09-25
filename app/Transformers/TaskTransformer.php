<?php
namespace App\Transformers;

use App\Task;
use League\Fractal\TransformerAbstract;

class TaskTransformer extends TransformerAbstract {
    public function transform(Task $task) {
        return [
            'id' => $task->id,
            'name' => $task->name, 
            'completed' => $task->completed
        ];
    }
}