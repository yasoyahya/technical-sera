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

/**
 * @OA\Info(
 *     title="User API",
 *     description="API untuk mengelola data pengguna",
 *     version="1.0.0"
 * )
 * @OA\Server(
 *     url="http://localhost/api",
 *     description="Development Server"
 * )
 */
class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="Get list of users",
     *     description="Mengambil daftar semua pengguna.",
     *     tags={"Users"},
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mengambil data pengguna.",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john@example.com")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/users",
     *     summary="Create a new user",
     *     description="Membuat pengguna baru.",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "email"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Pengguna berhasil dibuat.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john@example.com")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Bad Request"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     summary="Get user by ID",
     *     description="Mengambil detail pengguna berdasarkan ID.",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID pengguna",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mengambil data pengguna.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john@example.com")
     *         )
     *     ),
     *     @OA\Response(response=404, description="User Not Found"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
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

    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     summary="Update user information",
     *     description="Memperbarui informasi pengguna.",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID pengguna",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "email"},
     *             @OA\Property(property="name", type="string", example="Jane Doe"),
     *             @OA\Property(property="email", type="string", example="jane@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pengguna berhasil diperbarui.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Jane Doe"),
     *             @OA\Property(property="email", type="string", example="jane@example.com")
     *         )
     *     ),
     *     @OA\Response(response=404, description="User Not Found"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     summary="Delete a user",
     *     description="Menghapus pengguna berdasarkan ID.",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID pengguna",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pengguna berhasil dihapus",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="User successfully deleted!")
     *         )
     *     ),
     *     @OA\Response(response=404, description="User Not Found"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
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
