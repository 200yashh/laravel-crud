<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::select('*');
            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn("image",function(User $user){
                    if (!empty($user->image)) {
                        return asset("uploads/" .$user->image);
                    }
                    return "";
                })
                ->addColumn('action', function($row){
                    return '<a href="javascript:;" class="btn btn-sm btn-primary edit-user" data-id="'.$row->id.'" onclick="editUserData(this);">Edit</a> '.
                           '<a href="javascript:;" class="btn btn-sm btn-danger delete-user" data-id="'.$row->id.'" onclick="userDeleteData(this);">Delete</a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('users.list');
    }

    public function store(Request $request)
    {
        return $this->save($request, null);
    }

    public function save($request ,$id)
    {
        // dd($request->all());
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = new User();
        $response_message = "User saved successfully.";
        if (!empty($id)) {
            $data = User::find($id);
            $response_message = "User updated successfully.";
        }

        $values = Arr::except($request->all(), ['_token','image']);

        foreach ($values as $name => $value) {
            $data->{$name} = $value;
        }
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('uploads/');
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0777, true, true);
            }

            if ($image->move($destinationPath, $name)) {
                $data->image = $name;
            }
        }
        $data->save();

        return response()->json(['status' =>true,'success' => $response_message]);
    }

    public function edit($id)
    {
        $id = request()->get('id');
        $data = User::find($id)->toArray();
        // dd($data);
        $html = view('users.edit_modal', ['data' => $data])->render();

        return response()->json(['status' => true ,'html' =>$html]);
    }

    public function update(Request $request, $id)
    {
        return $this->save($request, $id);
    }


    public function destroy($id)
    {
        $id = request()->post('id');
        User::find($id)->delete();
        return response()->json(['success' => 'User deleted successfully.']);
    }
}