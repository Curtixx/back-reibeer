<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $employees = Employee::where('is_active', true)->get();
            return response()->json($employees, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao buscar funcionários!'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        try {
            $employee = DB::transaction(function () use ($request) {
                Employee::create($request->validated());
            });

            return response()->json($employee, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao criar funcionário!'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        try {
            return response()->json(new EmployeeResource($employee), 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Funcionário não encontrado!'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        try {
            $employee->update($request->validated());
            return response()->json(new EmployeeResource($employee), 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao atualizar funcionário!'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        try {
            DB::transaction(function () use ($employee) {
                return $employee->update(['is_active' => false]);
            });

            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao deletar funcionário!'], 500);
        }
    }
}
