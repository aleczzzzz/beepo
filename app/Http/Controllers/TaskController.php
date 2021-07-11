<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Exports\TasksExport;
use Excel;
use DB;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tasks = auth()->user()->tasks()->greatParent()->pending()->orderBy('position', 'asc')->with('pendingChildren')->get();

        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks
        ]);
    }

    public function complete()
    {
        $tasks = auth()->user()->tasks()->complete()->orderBy('position', 'asc')->get();

        return Inertia::render('Tasks/Complete', [
            'tasks' => $tasks
        ]);
    }

    public function deleted()
    {
        $tasks = auth()->user()->tasks()->onlyTrashed()->get();

        return Inertia::render('Tasks/Deleted', [
            'tasks' => $tasks
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $tasks = auth()->user()->tasks()->pluck('name', 'id');
        
        return Inertia::render('Tasks/Create', [
            'tasks' => $tasks
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateTaskRequest $request)
    {
        $data = $request->validated();
        if ($request->parent_id) {
            $parent = Task::find($request->parent_id);
            $last = $parent->children()->orderBy('position', 'desc')->first();
        } else {
            $last = Task::greatParent()->orderBy('position', 'desc')->first();
        }

        $data['position'] = $last ? $last->position+1 : 0;
        $data['status'] = 'pending';

        auth()->user()->tasks()->create($data);
  
        return redirect()->route('tasks.index')
                    ->with('message', 'Task Created Successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        $tasks = auth()->user()->tasks()->where('id', '!=', $task->id)->pluck('name', 'id');

        return Inertia::render('Tasks/Edit', [
            'tasks' => $tasks,
            'task' => $task
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task->update($request->validated());

        return redirect()->route('tasks.index')
                    ->with('message', 'Task Created Successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        if ($task->children->count()) {
            return redirect()->route('tasks.index')
                    ->with('message', 'Task Created Successfully.');
        }
        
        $task->delete();

        return redirect()->route('tasks.index')
                    ->with('message', 'Task Created Successfully.');
    }

    public function restore($id)
    {
        $task = Task::withTrashed()->find($id);
        
        $task->restore();

        return redirect()->route('tasks.index')
                    ->with('message', 'Task Created Successfully.');
    }

    public function updatePosition(Task $task, Request $request)
    {
        // return $request->all();

        DB::beginTransaction();
        
        if ($request->parent_id) {
            $parent = Task::find($request->parent_id);
            $needSort = $parent->children()->orderBy('position', 'asc')->get();
        } else {
            $needSort = Task::greatParent()->orderBy('position', 'asc')->get();
        }

        $initPosition = $request->index;

        foreach ($needSort as $key => $value) {
            if ($value->position >= $request->index) {
                $initPosition++;
                $value->position = $initPosition;
                $value->save();
            }
        }

        $task->parent_id = $request->parent_id;
        $task->position = $request->index;
        $task->save();

        DB::commit();

        return redirect()->route('tasks.index')
                    ->with('message', 'Task Created Successfully.');
    }

    public function updateStatus(Task $task)
    {
        // return $request->all();

        DB::beginTransaction();

        $task->status = 'complete';
        $task->save();

        DB::commit();

        return redirect()->route('tasks.index')
                        ->with('message', 'Task Created Successfully.');
    }

    public function downloadXLS()
    {
        return Excel::download(new TasksExport,'tasks.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
    
    public function downloadCSV()
    {
        return Excel::download(new TasksExport,'tasks.csv', \Maatwebsite\Excel\Excel::CSV);
    }
}
