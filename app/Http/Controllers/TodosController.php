<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Todo;
use Illuminate\Support\Facades\Auth;

class TodosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Todo::where('user_id', auth()->user()->id)->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    { }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'completed' => 'required|boolean'
        ]);
        $data['user_id'] = auth()->user()->id;
        $todo = Todo::create($data);

        return response($todo, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $todo = Todo::findOrFail($id);

        return response($todo, '200');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'completed' => 'required|boolean'
        ]);

        $todo = Todo::findOrFail($id);
        $data['user_id'] = auth()->user()->id;
        $todo->update($data);

        return response($todo, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $todo = Todo::findOrFail($id);
        if ($todo->user_id !== auth()->user()->id) {
            return response()->json('Unauthorized', 401);
        }
        $todo->delete();

        return response()->json('Deleted', 200);
    }

    public function updateAll(Request $request)
    {
        $data = $request->validate([
            'completed' => 'required|boolean',
        ]);
        $data['user_id'] = auth()->user()->id;
        Todo::query()->update($data);

        return response('UpdatedAll', 200);
    }

    public function destroyCompleted(Request $request)
    {
        $request->validate([
            'todos' => 'required|array'
        ]);
        $userId = auth()->user()->id;
        $listTodoDelete = $request->all();
        foreach ($listTodoDelete as $idTodoDelete) {
            $todos = Todo::findOrFail($idTodoDelete);
            foreach ($todos as $todo) {
                if ($todo->user_id !== $userId) {
                    return response()->json('Unauthorized', 401);
                } else {
                    $todo->delete();
                }
            }
        }
        return response()->json('Deleted', 200);
        // Todo::destroy($request->todos);
        // return response()->json('Deleted', 200);
    }
}
