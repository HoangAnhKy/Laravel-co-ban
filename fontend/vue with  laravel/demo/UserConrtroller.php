<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function dataList(Request $request)
    {
        $keySearch = $request->input('keySearch', '');
        $sortBy = $request->input('sortBy', 'id');
        $data = User::query();

        if ($keySearch) {
            $data = $data->where('name', 'like', '%' . $keySearch . '%');
        }

        if ($sortBy) {
            if (!empty($sortBy["display_created"])){
                $data = $data->orderBy("users.updated_at", $sortBy["display_created"]);
            }
            if (!empty($sortBy["name"])){
                $data = $data->orderBy("users.name", $sortBy["name"]);
            }
        }

        $data = $data->latest()->paginate(10);

        return response()->json([
            'status' => true,
            'data' => [
                'users' => $data->getCollection(),
                'pagination' => $data->linkCollection(),
                'meta' => [
                    'total' => $data->total(),
                    'per_page' => $data->perPage(),
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                    'first_page_url' => $data->url(1),
                    'last_page_url' => $data->url($data->lastPage()),
                    'next_page_url' => $data->nextPageUrl(),
                    'prev_page_url' => $data->previousPageUrl(),
                    'fromData' => $data->firstItem(),
                    'toData' => $data->lastItem(),
                ]
            ]
        ]);
    }

    public function addUser(Request $request)
    {
        if ($request->isMethod('POST')) {
            try {
                $data = $request->validate([
                    'name' => 'required|string|unique:users,name',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required|string|min:6',
                ]);
                if (User::query()->create($data)) {
                    return response()->json([
                        'status' => true,
                        'message' => 'User created successfully!'
                    ]);
                }
                throw new \Exception('Cannot add user!');
            } catch (ValidationException $exception) {
                return response()->json([
                    'status' => false,
                    'message' => $exception->validator->errors()->first(),
                    'errors' => $exception->errors()
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }
    }

    public function editUser(Request $request, $id = null)
    {
        if ($request->isMethod('POST')) {
            try {
                $request->merge(['user' => $id]);
                $data = $request->validate([
                    'user' => 'required|string|exists:users,id',
                ]);
                unset($data['user']);
                $data = $request->validate([
                    'name' => 'required|string|unique:users,name,' . $id,
                    'email' => 'required|email|unique:users,email,' . $id,
                ]);
                if (User::query()->where(['id' => $id])->update($data)) {
                    return response()->json([
                        'status' => true,
                        'message' => 'User edit successfully!'
                    ]);
                }
                throw new \Exception('Cannot edit user!');
            } catch (ValidationException $exception) {
                return response()->json([
                    'status' => false,
                    'message' => $exception->validator->errors()->first(),
                    'errors' => $exception->errors()
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }
    }

    public function deleteUser(Request $request, $id = null)
    {
        if ($request->isMethod('POST')) {
            try {
                $request->merge(['user' => $id]);
                $request->validate([
                    'user' => 'required|string|exists:users,id',
                ]);
                if (User::query()->where(['id' => $id])->delete()) {
                    return response()->json([
                        'status' => true,
                        'message' => 'Delete user successfully!'
                    ]);
                }
                throw new \Exception('Cannot delete user!');
            } catch (ValidationException $exception) {
                return response()->json([
                    'status' => false,
                    'message' => $exception->validator->errors()->first(),
                    'errors' => $exception->errors()
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }
    }
}