<?php

namespace App\Http\Controllers\Admin\Manage\Establishment;

use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;
use App\Models\Manage\Company\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::orderBy('path')->get();

        ActivityLogHelper::action('Accessed organizational chart management');

        return view('admin.manage.departments.index', compact('departments'));
    }

    public function create()
    {
        $parents = Department::orderBy('path')->get();

        ActivityLogHelper::action('Accessed department creation form');

        return view('admin.manage.departments.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'acronym'     => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'parent_id'   => 'nullable|exists:departments,id',
            'order'       => 'nullable|integer',
            'status'      => 'boolean',
        ]);

        $data['filter'] = str($data['title'])->lower();

        // Hierarquia
        if ($data['parent_id']) {
            $parent = Department::find($data['parent_id']);
            $data['level'] = $parent->level + 1;
        } else {
            $data['level'] = 0;
        }

        $data['establishment_id'] = 94;

        $department = Department::create($data);

        // Path (após ter ID)
        $department->update([
            'path' => $data['parent_id']
                ? $parent->path . '.' . $department->id
                : (string) $department->id
        ]);

        ActivityLogHelper::action("Created department: {$department->title}");

        return redirect()->route('config.departments.index')
            ->with('success', 'Department created successfully');
    }

    public function edit(Department $department)
    {
        $parents = Department::orderBy('path')->get();

        ActivityLogHelper::action("Accessed department edit: {$department->title}");

        return view('admin.manage.departments.edit', compact('department', 'parents'));
    }

    public function update(Request $request, Department $department)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'acronym'     => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'parent_id'   => 'nullable|exists:departments,id',
            'order'       => 'nullable|integer',
            'status'      => 'boolean',
        ]);

        $data['filter'] = str($data['title'])->lower();

        $department->update($data);

        ActivityLogHelper::action("Updated department: {$department->title}");

        return redirect()->route('config.departments.index')
            ->with('success', 'Department updated successfully');
    }

    public function status(Department $department)
    {
        $department->update([
            'status' => ! $department->status
        ]);

        ActivityLogHelper::action("Changed department status: {$department->title}");

        return back();
    }
}
