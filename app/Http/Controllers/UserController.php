<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException as MaatwebsiteValidationException;
use App\Imports\UsersImport;
use App\Exports\UsersExport;
use App\Exports\UsersTemplateExport;
use App\Http\Requests\ImportRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $perPage = request()->query('per_page') ?: 10;
        $sort = request()->query('sort') ?: 'created_at';
        $direction = request()->query('direction') ?: 'asc';
        return User::orderBy($sort, $direction)->paginate($perPage);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $validated = $request->validated();
        $request = array_merge($validated, ['password' => Hash::make($validated['password'])]);
        $user = User::create($request);
        return response([
            'message'=>'Successfully created User',
            'data' => $user
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return $user;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $user->update($request->all());
        return response([
            'message'=>'Successfully updated User',
            'data' => $user
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response(['message'=>'Successfully deleted User'], 200);
    }

    public function export ()
    {
        return Excel::download(new UsersExport, 'users.xlsx');
    }

    public function template ()
    {
        return Excel::download(new UsersTemplateExport, 'users-template.xlsx');
    }

    public function import(ImportRequest $request)
    {
        try {
            Excel::import(new UsersImport, $request->file);
        } catch (MaatwebsiteValidationException $e) {
            $failures = $e->failures();
            $message = collect();
            foreach ($failures as $failure) {
                $message->push([
                    // row that went wrong
                    'row' => $failure->row(),
                    // either heading key (if using heading row concern) or column index
                    'attribute' => $failure->attribute(),
                    // Actual error messages from Laravel validator
                    'errors' => $failure->errors(),
                    // The values of the row that has failed.
                    // 'values' => $failure->values(),
                ]);
            }
            return response([
                'message' => $message,
            ], 422);
        }

        return response([
            'message'=>'Successfully uploaded users.',
        ], 200);
    }
}
