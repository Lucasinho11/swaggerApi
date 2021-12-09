<?php

namespace App\Http\Controllers;
use App\Models\Task;
use Illuminate\Http\Request;

class TasksController extends Controller
{
    public function tasks(Request $request){

        if(!$request->completed){
            $tasks = Task::where('user_id', $request->user()->id)->get();
            return response()->json([
                $tasks
            ], 200);

        }
        else if($request->completed == 'false' || $request->completed == 'true'){
            
            $tasks = Task::where('user_id', $request->user()->id)->where('completed', $request->completed)->get();
            return response()->json([
                $tasks
            ], 200);
        }
        else{
            return response()->json([
                'page not found'
            ], 404);
        }
    /**
     * @OA\Get(path="/api/tasks",
     *   tags={"tasks"},
     *   summary="Tasks of user",
     *   description="Tasks of user",
     *   operationId="allTasksUser",
     *   security={ {"bearerAuth": {}} },
     *  @OA\Response(
    *    response=200,
    *    description="Success",
    *    @OA\JsonContent(
    *       @OA\Property(property="name", type="string", ref="#/components/schemas/Task"),  
    *        )
    *     ),
    *    @OA\Response(
    *    response=401,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="msg", type="string", example="Unauthorized"),
    *        )
    *     ),
     * )
     */
    /**
     * @OA\Get(path="/api/tasks?completed={completed}",
     *   tags={"tasks"},
     *   summary="Tasks of user",
     *   description="Tasks of user",
     *   operationId="allTasksUser",
     *   security={ {"bearerAuth": {}} },
     *   @OA\Parameter(
    *    description="True or false completed",
    *    in="path",
    *    name="completed",
    *    example="true",
    *    @OA\Schema(
    *       type="boolean"
    *    )
    * ),
     *  @OA\Response(
    *    response=200,
    *    description="Success",
    *    @OA\JsonContent(
    *       @OA\Property(property="name", type="string", ref="#/components/schemas/Task"),  
    *        )
    *     ),
    *   @OA\Response(
    *    response=404,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="msg", type="string", example="Page not found"),
    *        )
    *     ),
    *    @OA\Response(
    *    response=401,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="msg", type="string", example="Unauthorized"),
    *        )
    *     ),
     * )
     */
    }
    public function createTask(Request $request){
        $request->validate([
            'body' => 'required'
        ]);

        $task = Task::create([
            'body' => $request->body,
            'user_id'=>$request->user()->id,
        ]);
        if(!$request->body){
            return response()->json([
                "success"=> false,
                "msg"=> "Veuillez remplir tous les champs"
            ], 400);
        }
        return response()->json([
            $task
        ], 201);
    /**
     * @OA\Post(path="/api/createTask",
     *   summary="Create task",
     *   tags={"tasks"},
     *   description="Create task",
     *   operationId="createTask",
     *   security={ {"bearerAuth": {}} },
     * @OA\RequestBody(
    *    required=true,
    *    description="Body",
    *    @OA\JsonContent(
    *       required={"body"},
    *       @OA\Property(property="body", type="string", ref="#/components/schemas/Task/properties/body"),
    *    ),
    * ),
     *  @OA\Response(
    *    response=201,
    *    description="Success",
    *    @OA\JsonContent(
    *       @OA\Property(property="name", type="string", ref="#/components/schemas/Task"), 
    *        )
    *     ),
    *   @OA\Response(
    *    response=400,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="msg", type="string", example="Veuillez remplir tous les champs"),
    *        )
    *     ),
    *    @OA\Response(
    *    response=401,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="msg", type="string", example="Unauthorized"),
    *        )
    *     ),
     * )
     */

    }

    public function deleteTask(Request $request, $id)
    {

        $task = Task::find($id);
        if(!$task){
            return response()->json([
                "message"=> "Tache innexistante"
            ], 404);
        }
        if($request->user()->id != $task->user_id){
            return response()->json([
                "message"=> "Accès interdit!"
            ], 403);
        }
        $task_deleted = Task::where('id', $id)->delete();
        return response()->json([
            "success"=>true
        ], 200);
    /**
     * @OA\Delete(path="/api/deleteTask/{id}",
     *   tags={"tasks"},
     *   summary="Delete task of user",
     *   description="Delete task of user",
     *   operationId="deleteTasksUser",
     *   security={ {"bearerAuth": {}} },
     * @OA\Parameter(
    *    description="ID of task",
    *    in="path",
    *    name="id",
    *    required=true,
    *    example="1",
    *    @OA\Schema(
    *       type="integer",
    *       format="int64",
    *       ref="#/components/schemas/Task/properties/id"
    *    )
    * ),
     *  @OA\Response(
    *    response=200,
    *    description="Success",
    *    @OA\JsonContent(
    *       @OA\Property(property="success", type="string", example="true"),
    *        )
    *     ),
    *    @OA\Response(
    *    response=403,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="msg", type="string", example="Accès interdit"),
    *        )
    *     ),
    *   @OA\Response(
    *    response=404,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="msg", type="string", example="Tache inexistante"),
    *        )
    *     ),
    *    @OA\Response(
    *    response=401,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="msg", type="string", example="Unauthorized"),
    *        )
    *     ),
     * )
     */
       
     }
     public function updateTask(Request $request, $id)
    {

        $task = Task::find($id);
        if(!$task){
            return response()->json([
                "message"=> "Tache innexistante"
            ], 404);
        }
        if($request->user()->id != $task->user_id){
            return response()->json([
                "message"=> "Accès interdit!"
            ], 403);
        }
        $request->validate([
            'body' => 'required'
        ]);
        $task_updated = Task::where('id', $id)->update([
            'body'=> $request->body 
            ]);
        return response()->json([
            "success"=>true
        ], 200);
       /**
     * @OA\Put(path="/api/updateTask/{id}",
     *   tags={"tasks"},
     *   summary="Update task of user",
     *   description="Update task of user",
     *   operationId="updateTasksUser",
     *   security={ {"bearerAuth": {}} },
     *   @OA\RequestBody(
    *    required=true,
    *    description="Body",
    *    @OA\JsonContent(
    *       required={"body"},
    *       @OA\Property(property="body", type="string", ref="#/components/schemas/Task/properties/body"),
    *    ),
    * ),
     * @OA\Parameter(
    *    description="ID of task",
    *    in="path",
    *    name="id",
    *    required=true,
    *    example="1",
    *    @OA\Schema(
    *       type="integer",
    *       format="int64",
    *       ref="#/components/schemas/Task/properties/id"
    *    )
    * ),
    *  @OA\Response(
    *    response=200,
    *    description="Success",
    *    @OA\JsonContent(
    *       @OA\Property(property="name", type="string", ref="#/components/schemas/Task"), 
    *        )
    *     ),
    *   @OA\Response(
    *    response=400,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="msg", type="string", example="Veuillez remplir tous les champs"),
    *        )
    *     ),
    *    @OA\Response(
    *    response=404,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="msg", type="string", example="Tache inexistante"),
    *        )
    *     ),
    *    @OA\Response(
    *    response=403,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="msg", type="string", example="Accès interdit"),
    *        )
    *     ),
    *    @OA\Response(
    *    response=401,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="msg", type="string", example="Unauthorized"),
    *        )
    *     ),
     * )
     */
     }
     public function completeTask(Request $request, $id)
    {

        $task = Task::find($id);
        if(!$task){
            return response()->json([
                "message"=> "Tache innexistante"
            ], 404);
        }
        if($request->user()->id != $task->user_id){
            return response()->json([
                "message"=> "Accès interdit!"
            ], 403);
        }
        $task_updated = Task::where('id', $id)->update([
            'completed'=> true 
            ]);
        return response()->json([
            "success"=>true
        ], 200);
           /**
     * @OA\Get(path="/api/completeTask/{id}",
     *   tags={"tasks"},
     *   summary="Complete task of user",
     *   description="Complete task of user",
     *   operationId="completeTasksUser",
     *   security={ {"bearerAuth": {}} },
     * @OA\Parameter(
    *    description="ID of task",
    *    in="path",
    *    name="id",
    *    required=true,
    *    example="1",
    *    @OA\Schema(
    *       type="integer",
    *       format="int64",
    *       ref="#/components/schemas/Task/properties/id"
    *    )
    * ),
    *  @OA\Response(
    *    response=200,
    *    description="Success",
    *    @OA\JsonContent(
    *       @OA\Property(property="success", type="string", example="true"),
    *        )
    *     ),
    *    @OA\Response(
    *    response=404,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="msg", type="string", example="Tache inexistante"),
    *        )
    *     ),
    *    @OA\Response(
    *    response=403,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="msg", type="string", example="Accès interdit"),
    *        )
    *     ),
    *    @OA\Response(
    *    response=401,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="msg", type="string", example="Unauthorized"),
    *        )
    *     ),
     * )
     */
       
     }
}
