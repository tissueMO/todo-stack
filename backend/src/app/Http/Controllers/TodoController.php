<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Todo;
use MongoDB\BSON\UTCDateTime;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $todos = Todo::all()
            ->map(function ($todo) {
                $todo->limit = $todo->limit->toDateTime()->format('Y-m-d\TH:i:s');
                return $todo;
            });
        return $todos;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $todo = new Todo();
        $data = $request->all();
        $data['limit'] = new UTCDateTime(date_create_from_format('Y-m-d\TH:i:s', $data['limit']));
        $todo->fill($data)->save();
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
        $todo = Todo::find($id);
        $data = $request->all();
        $data['limit'] = new UTCDateTime(date_create_from_format('Y-m-d\TH:i:s', $data['limit']));
        $todo->fill($data)->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Todo::destroy($id);
    }
}
