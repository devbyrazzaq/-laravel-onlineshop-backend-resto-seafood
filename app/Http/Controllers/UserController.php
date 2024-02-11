<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //index
    public function index(Request $request)
    {
        // get all users with pagination
        // bisa $request->input('name') atau $request->name
        $users = DB::table('users')->when($request->input('name'), function ($query) use ($request) {
            $query->where('name', 'like', '%' . $request->input('name') . '%')->orWhere('email', 'like', '%' . $request->input('name') . '%');
        })->paginate(10);
        return view('pages.user.index', compact('users'));
    }

    //create
    public function create()
    {
        return view("pages.user.create");
    }

    //store
    public function store(Request $request)
    {
        // validate request
        $request->validate([
            "name" => "required",
            "email" => "required|unique:users",
            "password" => "required|min:8",
            "role" => "required|in:admin,staff,user"
        ]);

        $user = new User([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password),
            "role" => $request->role,
        ]);

        $user->save();
        return  redirect()->route('users.index')->with('success', 'User created successfully');
    }

    // show
    public function show($id)
    {
        return view("pages.user.show");
    }

    // edit
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view("pages.user.edit", compact(["user"]));
    }



    // update
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if ($user->email == $request->email) {
            $emailValidation = "required";
        } else {
            $emailValidation = "required|unique:users";
        }
        $request->validate([
            "name" => "required",
            "email" => $emailValidation,
            "role" => "required|in:admin,staff,user"
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return  redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    // delete
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return  redirect()->route('users.index')->with('success', 'User deleted successfully');
    }
}
