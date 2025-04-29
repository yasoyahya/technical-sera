<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Actions\Address\CreateAddress;
use App\Actions\Address\ListAddress;
use App\Actions\Address\ShowAddressById;

use App\Models\User;

class AddressController extends Controller
{
    public function index(ListAddress $action)
    {
        try {

            $address = $action->handle();

            return response()->json([
                'status' => 'success',
                'data' => $address
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load address',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request, CreateAddress $action)
    {
        try {
            $data = $request->validate([
                'user_id' => 'required|integer',
                'street' => 'required',
            ]);

            $user = User::findOrFail($data['user_id']);

            $address = $action->handle($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Address successfully created!',
                'data' => $address
            ], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function show($id, ShowAddressById $action)
    {
        try {
            $address = $action->handle((int) $id);

            return response()->json([
                'status' => 'success',
                'data' => $address,
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Address not found.',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

}
