<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Task;
use App\Transformers\TaskTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\JsonApiSerializer;
use Drp\JsonApiParser\Exceptions\FailedValidationException;

class TasksController extends Controller
{
    public function getAll() {
        $manager = new Manager();
        $manager->setSerializer(new JsonApiSerializer());

        $tasks = Task::orderBy('created_at', 'asc')->get();
    
        $resources = new Collection($tasks, new TaskTransformer(), 'task');

        return $manager->createData($resources)->toArray();
    }

    public function createTask(Request $request) {
        try {
            $jsonApiValidator = app(\Drp\JsonApiParser\JsonApiValidator::class);
            $jsonApiValidator = $jsonApiValidator->validator(
                'tasks',
                \Drp\LaravelJsonApiParser\Validation\Validator::make(
                    ['name' => 'required'],
                    ['name.required' => 'You must provide a name']
                )
            );

            $jsonApiValidator->validate($request->json()->all());

            $result = json_api()->resolver('tasks', function (array $data) {
                $task = new Task;
                $task->name = $data['name'];
                $task->save();

                return $task;
            })->parse($request->json()->all());

            $manager = new Manager();
            $manager->setSerializer(new JsonApiSerializer());
            
            $createdTask = $result['tasks'];

            $resources = new Item($createdTask, new TaskTransformer(), 'task');

            return response($manager->createData($resources)->toArray(), 201);
        } catch(FailedValidationException $ex) {
            return response($ex->getMessages(), 409);
        }
    }

    public function updateTask($id, Request $request) {
        try {
            $jsonApiValidator = app(\Drp\JsonApiParser\JsonApiValidator::class);
            $jsonApiValidator = $jsonApiValidator->validator(
                'tasks',
                \Drp\LaravelJsonApiParser\Validation\Validator::make(
                    ['name' => 'required'],
                    ['name.required' => 'You must provide a name']
                )
            );

            $jsonApiValidator->validate($request->json()->all());

            json_api()->resolver('tasks', function (array $data) use ($id){
                $task = Task::findOrFail($id);
                $task->name = $data['name'];
                $task->completed = $data['completed'];
                $task->save();
            })->parse($request->json()->all());

            return response('', 204);
        } catch(FailedValidationException $ex) {
            return response($ex->getMessages(), 409);
        }
    }

    public function deleteTask($id) {
        Task::findOrFail($id)->delete();

        return response('', 204);
    }
}