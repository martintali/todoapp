<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TasksController extends Controller
{
    public function getAll() {
        $tasks = Task::orderBy('created_at', 'asc')->get();
    
        return response()->json($tasks);
    }

    public function createTask(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return redirect('api/v1')
                ->withInput()
                ->withErrors($validator);
        }

        $task = new Task;
        $task->name = $request->name;
        $task->save();

        return redirect('api/v1');
    }

    public function updateTask($id, Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'completed' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect('api/v1')
                ->withInput()
                ->withErrors($validator);
        }

        $task = Task::findOrFail($id);
        $task->name = $request->name;
        $task->completed = $request->completed;
        $task->save();

        return redirect('api/v1');
    }

    public function deleteTask($id) {
        Task::findOrFail($id)->delete();

        return redirect('api/v1');
    }
}