<?php

namespace App\Admin\Controllers;

use App\models\FinacialPeriod;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class FinacialPeriodController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Finacial Period';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new FinacialPeriod());
        $u = Admin::user();
        $grid->model()->where('company_id', $u->company_id);
        $grid->model()
            ->where('company_id', $u->company_id)
            ->orderBy('start_date', 'desc');
        $grid->disableBatchActions();
        $grid->quickSearch('name');

        $grid->column('id', __('ID'));

        $grid->column('name', __('Name'))->sortable();
        $grid->column('start_date', __('Start date'))->display(function ($start_date) {
            return date('y-m-d', strtotime($start_date));
        })->sortable();
        $grid->column('end_date', __('End date'))->display(function ($end_date) {
            return date('y-m-d', strtotime($end_date));
        })->sortable();
        $grid->column('status', __('Status'))->label([
            'Active' => 'success',
            'Inactive' => 'danger'
        ]);
        $grid->column('description', __('Description'))->hide();
        $grid->column('total_investment', __('Total investment'))->display(function ($total_investment) {
            return number_format($total_investment);
        })->sortable();
        $grid->column('total_sales', __('Total sales'))->display(function ($total_sales) {
            return number_format($total_sales);
        })->sortable();
        $grid->column('total_profits', __('Total profits'))->display(function ($total_profits) {
            return number_format($total_profits);
        })->sortable();
        $grid->column('total_expenses', __('Total expenses'))->display(function ($total_expenses) {
            return number_format($total_expenses);
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
        $show = new Show(FinacialPeriod::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('company_id', __('Company id'));
        $show->field('name', __('Name'));
        $show->field('start_date', __('Start date'));
        $show->field('end_date', __('End date'));
        $show->field('status', __('Status'));
        $show->field('description', __('Description'));
        $show->field('total_investment', __('Total investment'));
        $show->field('total_sales', __('Total sales'));
        $show->field('total_profits', __('Total profits'));
        $show->field('total_expenses', __('Total expenses'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new FinacialPeriod());
        $u = Admin::user();


        $form->hidden('company_id', __('Company id'))->default($u->company_id);
        $form->text('name', __('Name'))->rules("required");
        $form->date('start_date', __('Start date'))->default(date('Y-m-d'));
        $form->date('end_date', __('End date'))->default(date('Y-m-d'));

        $form->radio('status', __('Status'))->options([
            'Active' => 'Active',
            'Inactive' => 'Inactive',
        ])->default('Active')->rules("required");
        $form->textarea('description', __('Description'));


        return $form;
    }
}
