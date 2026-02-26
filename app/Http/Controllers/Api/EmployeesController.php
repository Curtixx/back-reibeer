<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EmployeesController extends Controller
{
    /**
     * Clear all employees cache.
     */
    private function clearEmployeesCache(): void
    {
        Cache::tags(['employees'])->flush();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection|JsonResponse
    {
        try {
            $page = request()->input('page', 1);
            $perPage = request()->input('per_page', 15);
            $cacheKey = 'employees_page:' . $page . ':per_page:' . $perPage;

            $employees = Cache::tags(['employees'])->remember($cacheKey, 600, function () use ($page, $perPage) {
                return Employee::where('is_active', true)->paginate($perPage);
            });

            return EmployeeResource::collection($employees);
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
                $funcionario = Employee::create($request->only(['name', 'email']));
                User::create([
                    'name' => $funcionario->name,
                    'email' => $funcionario->email,
                    'password' => $request->password,
                ]);

                return $funcionario;
            });

            $this->clearEmployeesCache();

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
            $employee->update($request->only(['name', 'email', 'is_active']));
            User::where('email', $employee->email)->update($request->only(['name', 'email', 'password']));

            $this->clearEmployeesCache();

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

            $this->clearEmployeesCache();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao deletar funcionário!'], 500);
        }
    }
}
