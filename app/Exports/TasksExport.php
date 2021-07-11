<?php

namespace App\Exports;

use App\Models\Task;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TasksExport implements FromCollection, WithHeadings
{
    public function headings(): array
    {
        return [
            '#',
            'Parent',
            'Owner',
            'Name',
            'Description',
            'Status',
            'Created At',
            'Updated At',
            'Deleted At',
        ];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $tasks = Task::with('user', 'parent')->get();

        $tasks = $tasks->map(function($val) {
            return [
                $val->id,
                $val->parent ? $val->parent->name : '',
                $val->user->name,
                $val->name,
                $val->description,
                ucwords($val->status),
                $val->created_at ? $val->created_at->format('Y-m-d') : '',
                $val->updated_at ? $val->updated_at->format('Y-m-d') : '',
                $val->deleted_at ? $val->deleted_at->format('Y-m-d') : '',

            ];
        });

        return $tasks;
    }
}
