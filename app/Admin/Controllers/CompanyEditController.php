<?php

namespace App\Admin\Controllers;

use App\models\Company;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CompanyEditController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Company Profile';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Company());
        $grid->disableCreateButton();
        $grid->disableBatchActions();
        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableView();
        });

        $u = Admin::user();
        $grid->model()->where('id', $u->company_id);
        $grid->column('logo', __('Logo'))->image('', 50, 50);

        $grid->column('name', __('Name'));
        $grid->column('email', __('Email'));
        $grid->column('website', __('Website'));
        $grid->column('about', __('About'))->hide();
        $grid->column('licence_expire', __('Licence Expire'));
        $grid->column('address', __('Address'))->hide();
        $grid->column('phone_number', __('Phone number'));
        $grid->column('phone_number_2', __('Phone number 2'))->hide();
        /* $grid->column('pobox', __('Pobox'));
        $grid->column('color', __('Color'));
        $grid->column('slogan', __('Slogan'));
        $grid->column('facebook', __('Facebook'));
        $grid->column('twitter', __('Twitter'));
        $grid->column('currency', __('Currency'));
        $grid->column('workers_can_create_stock_item', __('Workers can create stock item'));
        $grid->column('workers_can_create_stock_record', __('Workers can create stock record'));
        $grid->column('workers_can_create_stock_category', __('Workers can create stock category'));
        $grid->column('workers_can_view_stock_balance', __('Workers can view stock balance'));
        $grid->column('workers_can_view_stock_stats', __('Workers can view stock stats')); */

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
        $show->field('currency', __('Currency'));
        $show->field('workers_can_create_stock_item', __('Workers can create stock item'));
        $show->field('workers_can_create_stock_record', __('Workers can create stock record'));
        $show->field('workers_can_create_stock_category', __('Workers can create stock category'));
        $show->field('workers_can_view_stock_balance', __('Workers can view stock balance'));
        $show->field('workers_can_view_stock_stats', __('Workers can view stock stats'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Company());

        $form->text('name', __('Name'))->rules('required');
        $form->text('email', __('Email'));
        $form->image('logo', __('Logo'));
        $form->text('website', __('Website'));
        $form->text('about', __('About'));
        $form->textarea('address', __('Address'));
        $form->text('phone_number', __('Phone number'));
        $form->text('phone_number_2', __('Phone number 2'));
        $form->text('pobox', __('Pobox'));
        $form->color('color', __('Color'));
        $form->text('slogan', __('Slogan'));
        $form->text('facebook', __('Facebook'));
        $form->text('twitter', __('Twitter'));

        $form->divider('Settings');

        $form->text('currency', __('Currency'))->default('USD')->rules('required');
        $form->radio('workers_can_create_stock_item', __('Can Workers  create stock item ?'))
            ->options([
                'Yes' => 'Yes',
                'No' => 'No'
            ])->default('Yes');

        $form->radio('workers_can_create_stock_record', __('Can Workers  create stock record ?'))
            ->options([
                'Yes' => 'Yes',
                'No' => 'No'
            ])->default('Yes');
        $form->radio('workers_can_create_stock_category', __(' Can Workers  create stock category ?'))
            ->options([
                'Yes' => 'Yes',
                'No' => 'No'
            ])->default('Yes');
        $form->radio('workers_can_view_stock_balance', __(' Can Workers  view stock balance ?'))
            ->options([
                'Yes' => 'Yes',
                'No' => 'No'
            ])->default('Yes');
        $form->radio('workers_can_view_stock_stats', __('Can Workers  view stock stats ?'))
            ->options([
                'Yes' => 'Yes',
                'No' => 'No'
            ])->default('Yes');

        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
            $tools->disableView();
        });
        $form->disableCreatingCheck();
        $form->disableEditingCheck();
        $form->disableViewCheck();
        $form->disableReset();




        return $form;
    }
}
