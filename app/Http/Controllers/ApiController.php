<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use App\Models\Utils;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

use Illuminate\Routing\Controller as BaseController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ApiController extends BaseController

{
    

    public function Myfile_uploading($model, Request $r)
    {
        die("time to uplaod file");
    }

    public function My_list($model, Request $r)
    {
        $u = Utils::get_user($r);
        if ($u == null) {
            Utils::error("user not found");
        }
        $model = "App\Models\\" . $model;
        $data = $model::where('company_id', $u->company_id)->limit(100)->get();
        Utils::success($data, "Listed Successfully");
    }
    public function my_update($model, Request $r)
    {
        $u = Utils::get_user($r);
        if ($u == null) {
            Utils::error("user not found");
        }
        $model = "App\Models\\" . $model;
        $object = $model::find($r->id);
        $isEditing = true;
        if ($object == null) {
            $object = new $model();
            $isEditing = false;
        }

        // $object = new $model;
        $table_name = $object->getTable();
        $column =  Schema::getColumnListing($table_name);
        $exept = ['id', 'created_at' . 'updated_at'];
        $data = $r->all();

        foreach ($data as $key => $value) {
            if (!in_array($key, $column)) {
                continue;
            }
            if (in_array($key, $exept)) {
                continue;
            }
            $object->$key = $value;
        }
        try {
            $object->save();
        } catch (\Throwable $e) {
            Utils::error($e->getMessage());
        }
        $new_object = $model::find($object->id);
        if ($isEditing) {
            Utils::success($new_object, "updated successfully.");
        }
        Utils::success($new_object, "Created successfully.");
    }
    public function login(Request $r)
    {

        // checking if the email is provided
        if ($r->email == null) {
            Utils::error('email is required');
        }


        // checking if the email is valid
        if (!filter_var($r->email, FILTER_VALIDATE_EMAIL)) {
            Utils::error('Email is invalid');
        }


        //  if password is provided
        if ($r->password == null) {
            Utils::error('password is required');
        }

        $user = User::where("email", $r->email)->first();
        if ($user == null) {
            Utils::error("Acount not found");
        }

        if (!password_verify($r->password, $user->password)) {
            Utils::error('password does not match');
        }
        $company = Company::find($user->company_id);
        if ($company == null) {
            Utils::error('Company not found');
        }

        Utils::success(
            [
                'user' => $user,
                'company' => $company,
            ],
            "login sucessfull"
        );
    }



    public function register(Request $r)
    {
        if ($r->first_name == null) {
            Utils::error('first name is required');
        }

        if ($r->last_name == null) {
            Utils::error('last_name is required');
        }

        if ($r->email == null) {
            Utils::error('email is required');
        }


        //checking if the email is valid
        if (!filter_var($r->email, FILTER_VALIDATE_EMAIL)) {
            Utils::error('Email is invalid');
        }

        // checking if the email is already registered
        $u = User::where('email', $r->email)->first();
        if ($u != null) {
            Utils::error('Email is already registered');
        }
        if ($r->password == null) {
            Utils::error('password is required');
        }
        /* 
         checking if  password is atleast 8  characters
        if (strlen($r->password == null) < 8) {
            Utils::error('password  must be atleast 8 character');
        } */

        // for the company info 
        if ($r->company_name == null) {
            Utils::error('company_name');
        }

        if ($r->currency == null) {
            Utils::error('currency');
        }

        // we are registering the new user
        $new_user = new  User();
        $new_user->first_name = $r->first_name;
        $new_user->last_name = $r->last_name;
        $new_user->username = $r->email;
        $new_user->email = $r->email;
        $new_user->password = password_hash($r->password, PASSWORD_DEFAULT);
        $new_user->name = $r->first_name . "" . $r->last_name;
        $new_user->company_id = 1;
        $new_user->phone_number = $r->phone_number;
        $new_user->status = "active";
        try {
            $new_user->save();
        } catch (\Throwable $e) {
            Utils::error($e->getMessage());
        }

        $registered_user = User::find($new_user->id);
        if ($registered_user == null) {
            Utils::error("Failed to save user");
        }

        $company = new Company();
        $company->owner_id = $registered_user->id;
        $company->name = $r->company_name;
        $company->email = $r->email;
        $company->currency = $r->currency;
        $company->phone_number = $r->phone_number;
        $company->status = "status";
        $company->licence_expire = date('y-m-d', strtotime("+1 year"));

        try {
            $company->save();
        } catch (\Throwable $e) {
            Utils::error($e->getMessage());
        }

        $registered_company = Company::find($company->id);
        if ($registered_company == null) {
            Utils::error("Failed to save company");
        }

        // insert into the adminrole users

        DB::table('admin_role_users')->insert([
            'user_id' => $registered_user->id,
            'role_id' => 2,

        ]);

        Utils::success(
            [
                'user' => $registered_user,
                'company' => $registered_company,
            ],
            "Registration sucessfull"
        );
    }
}
