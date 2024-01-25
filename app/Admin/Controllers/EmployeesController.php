<?php

namespace App\Admin\Controllers;

use App\models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Validation\Rule;

class EmployeesController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'User';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User());

        $u = Admin::user();
        $grid->model()->where('company_id', $u->company_id);
        $grid->quickSearch('name', 'first_name', 'last_name')->placeholder('search here');
        $grid->disableBatchActions();

        // $grid->column('id', __('Id'));
        $grid->column('name', __('Name'))->sortable();

        $grid->column('avatar', __('Avatar'))->image('', 50, 50);
        $grid->column('username', __('Username'));
        //  $grid->column('password', __('Password'));
        //$grid->column('remember_token', __('Remember token'));

        //  $grid->column('company_id', __('Company id'));
        //  $grid->column('first_name', __('First name'));
        // $grid->column('last_name', __('Last name'));
        $grid->column('phone_number', __('Phone number'));
        $grid->column('phone_number2', __('Phone number2'))->hide();
        $grid->column('address', __('Address'));
        $grid->column('sex', __('Gender'))->filter([
            'Male' => 'Male',
            'Female' => 'Female',
        ]);
        $grid->column('dob', __('Dob'))->sortable();
        $grid->column('status', __('Status'))->sortable()
        ->label([
            'Active'=>'success',
            'Inactive'=>'danger'
        ]);
        $grid->column('created_at', __('Registered'))->display(function ($created_at) {
            return date('y-m-d', strtotime($created_at));
        })->sortable();

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
        $show = new Show(User::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('username', __('Username'));
        $show->field('password', __('Password'));
        $show->field('name', __('Name'));
        $show->field('avatar', __('Avatar'));
        $show->field('remember_token', __('Remember token'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('company_id', __('Company id'));
        $show->field('first_name', __('First name'));
        $show->field('last_name', __('Last name'));
        $show->field('phone_number', __('Phone number'));
        $show->field('phone_number2', __('Phone number2'));
        $show->field('address', __('Address'));
        $show->field('sex', __('Sex'));
        $show->field('dob', __('Dob'));
        $show->field('status', __('Status'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new User());
        $u = Admin::user();
        $form->hidden('company_id', __('Company id'))->default($u->company_id);
        $form->divider('Personal Information');
        $form->text('first_name', __('First name'))->rules('required');
        $form->text('last_name', __('Last name'))->rules('required');
        $form->radio('sex', __('Sex'))->options([
            'Female' => 'Female',
            'Male' => 'Male'
        ])->rules("required");
        $form->text('phone_number', __('Phone number'))->rules('required');
        $form->text('phone_number2', __('Phone number2'));
        $form->date('dob', __('Dob'));
        $form->textarea('address', __('Address'));



        $form->divider('Account Information');

        $form->image('avatar', __('Avatar'));
        $form->text('email', __('Username'));

        $form->radio('status', __('Status'))->default('active')->options([
            'Active' => 'Active',
            'InActive' => 'InActive'
        ]);

        // $form->password('password', __('Password'));
        // $form->text('name', __('Name'));


        return $form;
    }
}
