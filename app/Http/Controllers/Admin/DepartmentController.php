<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DepartmentController extends Controller
{
    use AuthorizesRequests;
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Department::class);
        
        $query = Department::with('manager')
            ->withCount('employees')
            ->latest();
            
        // If user is a department manager, only show their department
        if (auth()->user()->hasRole('department_manager')) {
            $query->where('id', auth()->user()->department_id);
        }
        
        $departments = $query->paginate(10);
            
        return view('admin.departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Department::class);
        
        $managers = User::role(['admin', 'manager'])
            ->whereDoesntHave('managedDepartment')
            ->pluck('name', 'id');
            
        return view('admin.departments.create', compact('managers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Department::class);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments',
            'code' => 'required|string|max:10|unique:departments',
            'description' => 'nullable|string',
            'manager_id' => ['nullable', 'exists:users,id', function($attribute, $value, $fail) {
                $user = User::find($value);
                if ($user && !$user->hasAnyRole(['admin', 'manager'])) {
                    $fail('The selected user must have admin or manager role.');
                }
            }],
        ]);
        
        $department = Department::create($validated);
        
        // If a manager is assigned, ensure they have the department_manager role
        if ($department->manager_id) {
            $manager = User::find($department->manager_id);
            if ($manager && !$manager->hasRole('department_manager')) {
                $manager->assignRole('department_manager');
            }
        }
        
        return redirect()->route('admin.departments.index')
            ->with('success', 'Department created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        $this->authorize('view_departments');
        
        $department->load(['manager', 'employees']);
        
        return view('admin.departments.show', compact('department'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
    {
        $this->authorize('edit_departments');
        
        $managers = User::role(['admin', 'manager'])
            ->where(function($query) use ($department) {
                $query->whereDoesntHave('managedDepartment')
                    ->orWhere('id', $department->manager_id);
            })
            ->pluck('name', 'id');
            
        return view('admin.departments.edit', compact('department', 'managers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department)
    {
        $this->authorize('edit_departments');
        
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('departments')->ignore($department->id),
            ],
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('departments')->ignore($department->id),
            ],
            'description' => 'nullable|string',
            'manager_id' => 'nullable|exists:users,id',
        ]);
        
        $department->update($validated);
        
        return redirect()->route('admin.departments.index')
            ->with('success', 'Department updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        $this->authorize('delete_departments');
        
        // Check if department has employees
        if ($department->employees()->exists()) {
            return back()->with('error', 'Cannot delete department with assigned employees.');
        }
        
        DB::transaction(function () use ($department) {
            // Remove manager association
            $department->update(['manager_id' => null]);
            $department->delete();
        });
        
        return redirect()->route('admin.departments.index')
            ->with('success', 'Department deleted successfully.');
    }
}
