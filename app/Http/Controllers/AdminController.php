<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function destroy(Request $request) 
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

           $notification = array(
           'message' => 'User Logout Successfull ', 
            'alert-type' => 'success'
    );

        return redirect('/login')->with($notification);
    }// end method


    public function Profile(){
        $id= Auth::User()->id;
        $adminData =User::find($id);
        return view('admin.admin_profile_view',compact('adminData'));

    }// end method



    public function EditProfile(){
        $id= Auth::User()->id;
        $editData =User::find($id);
        return view('admin.admin_profile_edit',compact('editData'));

    }// end method



    public function StoreProfile(Request $request){
    $id = Auth::user()->id;
    $data = User::find($id);
    $data->name = $request->name;
    $data->email = $request->email;
    $data->username = $request->username;

    if ($request->file('profile_image')) {
        $file = $request->file('profile_image');

        $filename = date('YmdHi').$file->getClientOriginalName();
        $file->move(public_path('upload/admin_images'),$filename);
        $data['profile_image'] = $filename;
    }
    $data->save();

    $notification = array(
    'message' => 'Admin Profile Updated Successfully', 
    'alert-type' => 'success'
    );

    return redirect()->route('admin.profile')->with($notification);

    return redirect()->route('admin.profile');

    }// End Method
    


    public function ChangePassword(){

        return view('admin.admin_change_password');

    }// End Method


    public function UpdatePassword(Request $request){


        $validateData = $request->validate([
            'oldpassword' => 'required',
            'newpassword' => 'required',
            'confirm_password' => 'required|same:newpassword',

        ]);

        $hashedPassword = Auth::user()->password;                         //which user login kora
        if (Hash::check($request->oldpassword,$hashedPassword )) {          //old pass match?
            $users = User::find(Auth::id());
            $users->password = bcrypt($request->newpassword);                      //newpass
            $users->save();

            session()->flash('message','Password Updated Successfully');
            return redirect()->back();                                     //same page e back
        } else{
            session()->flash('message','Old password is not matching');
            return redirect()->back();
        }     

    }// End Method







} 
