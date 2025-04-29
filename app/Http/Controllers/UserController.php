<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Actions\Users\CreateUser;
use App\Actions\Users\ShowUserById;
use App\Actions\Users\ListUsers;
use App\Actions\Users\UpdateUser;
use App\Actions\Users\DeleteUser;

use App\Models\User;

class UserController extends Controller
{
    public function index(ListUsers $action)
    {
        try {
            $users = $action->handle();

            return response()->json([
                'status' => 'success',
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load users',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request, CreateUser $action)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
            ]);

            $user = $action->handle($data);

            return response()->json([
                'status' => 'success',
                'message' => 'User successfully created!',
                'data' => $user
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function show($id, ShowUserById $action)
    {
        try {
            $user = $action->handle((int) $id);

            return response()->json([
                'status' => 'success',
                'data' => $user,
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found.',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function update($id, Request $request, UpdateUser $action)
    {
        try {
            $user = User::findOrFail($id);

            $rules = [];

            if ($request->has('name')) {
                $rules['name'] = 'required|string';
            }

            if ($request->has('email')) {
                $rules['email'] = 'required|email|unique:users,email,' . $user->id;
            }

            $data = $request->validate($rules);

            $updatedUser = $action->handle($user, $data);

            return response()->json([
                'status' => 'success',
                'message' => 'User successfully updated!',
                'data' => $updatedUser
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found.'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id, Request $request, DeleteUser $action)
    {
        try {
            $user = User::findOrFail($id);

            $deleteUser = $action->handle($user);

            return response()->json([
                'status' => 'success',
                'message' => 'User successfully deleted!',
                'data' => $deleteUser
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found.'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
