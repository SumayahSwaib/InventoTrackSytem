<?php

namespace App\Admin\Controllers;

use App\models\Company;
use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Form\Field\Display;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;

class CompanyController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Companies';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Company());
        $grid ->disableBatchActions();
        $grid ->quickSearch('name');

        // $grid->column('id', __('Id'));
        $grid->column('created_at', __('Created at'))
            ->display(function ($created_at) {
                return date('y-m-d', strtotime($created_at));
            })->sortable();

        $grid->column('owner_id', __('Owner'))->display(function ($owner_id) {
            $user = User::find($owner_id);
            if ($user == null) {
                return 'Not Found';
            }
            return $user->name;
        })->sortable();
        $grid->column('name', __('Name'))->sortable();
        $grid->column('email', __('Email'));
        $grid->column('logo', __('Logo'))->hide();
        $grid->column('website', __('Website'))->hide();
        $grid->column('about', __('About'))->hide();
        $grid->column('status', __('Status'))->display(function($status){
            return $status =="active"?"Active":"Inactive";

        })->sortable();
        $grid->column('address', __('Address'))->hide();
        $grid->column('phone_number', __('Phone number'))->hide();
        $grid->column('phone_number_2', __('Phone number 2'))->hide();
        $grid->column('pobox', __('Pobox'))->hide();
        $grid->column('color', __('Color'))->hide();
        $grid->column('slogan', __('Slogan'));
        $grid->column('licence_expire', __('Licence expire'))->display(function ($created_at) {
            return date('y-m-d', strtotime($created_at));
        })->sortable();
        $grid->column('facebook', __('Facebook'))->hide();
        $grid->column('twitter', __('Twitter'))->hide();

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Company::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('owner_id', __('Owner id'));
        $show->field('name', __('Name'));
        $show->field('email', __('Email'));
        $show->field('logo', __('Logo'));
        $show->field('website', __('Website'));
        $show->field('about', __('About'));
        $show->field('status', __('Status'));
        $show->field('address', __('Address'));
        $show->field('phone_number', __('Phone number'));
        $show->field('phone_number_2', __('Phone number 2'));
        $show->field('pobox', __('Pobox'));
        $show->field('color', __('Color'));
        $show->field('slogan', __('Slogan'));
        $show->field('licence_expire', __('Licence expire'));
        $show->field('facebook', __('Facebook'));
        $show->field('twitter', __('Twitter'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {

        /*  $comp = Company::find(1);
        $comp->name = $comp->name . "-" . rand(1, 100);
        $comp->save();
        dd($comp); */

        $admin_role_users = DB::table('admin_role_users')->where([
            'role_id' => 2,
        ])->get();

        $company_admins = [];

        foreach ($admin_role_users as $key => $value) {
            $user = User::find($value->user_id);
            if ($user == null) {
                continue;
            }
            $company_admins[$user->id] = $user->name . '-' . $user->id;
        }

        // dd($company_admins);
        $form = new Form(new Company());

        $form->select('owner_id', __('Owner'))->options($company_admins)->default(1)->rules('required');
        $form->text('name', __('Company Name'))->rules('required');
        $form->email('email', __('Company Email'));
        $form->image('logo', __('Company Logo'));
        $form->url('website', __('Company Website'));
        $form->textarea('about', __('About'));
        $form->text('status', __('Status'));
        $form->text('address', __('Address'));
        $form->text('phone_number', __('Phone number'));
        $form->text('phone_number_2', __('Phone number 2'));
        $form->text('pobox', __('Pobox'));
        $form->color('color', __('Color'));
        $form->text('slogan', __('Slogan'));
        $form->date('licence_expire', __('Licence expire'));
        $form->url('facebook', __('Facebook'));
        $form->url('twitter', __('Twitter'));

        return $form;
    }
}
