<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;

class UserPermissionController extends Controller
{
    /**
     * Sincroniza as permissões de um usuário.
     */
    public function syncPermissions(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        try {
            $user = User::findOrFail($request->user_id);
            $user->permissions()->sync($request->permissions);

            return response()->json([
                'message' => 'Permissões atualizadas com sucesso!',
                'user' => $user->load('permissions'),
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao atualizar permissões do usuário!'], 500);
        }
    }

    public function getPermissions()
    {
        try {
            $permissions = Permission::all();

            return PermissionResource::collection($permissions);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao buscar permissões!'], 500);
        }
    }

    public function getUserPermissions(User $user)
    {
        try {
            return PermissionResource::collection($user->permissions);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao buscar permissões do usuário!'], 500);
        }
    }
}
