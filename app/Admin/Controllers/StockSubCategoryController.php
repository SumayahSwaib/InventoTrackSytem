<?php

namespace App\Admin\Controllers;

use App\Models\StockCategory;
use App\models\StockSubCategory;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Symfony\Contracts\Service\Attribute\Required;

class StockSubCategoryController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Stock SubCategory';

    /**  
     * 
     * 
     * 
     * 
     * 
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new StockSubCategory());

        $u = Admin::user();
        $grid->model()
            ->where('company_id', $u->company_id)
            ->orderBy('name', 'asc');
        $grid->disableBatchActions();
        $grid->quickSearch('name');

        $grid->column('id', __('ID'))->sortable();
        $grid->column('image', __('Image'))->image('', 50, 50);
        $grid->column('created_at', __('Created at'))->display(function ($created_at) {
            return date('y-m-d', strtotime($created_at));
        })->sortable()->hide();
        //$grid->column('updated_at', __('Updated at'));

        $grid->column('name', __('Name'))->sortable();
        $grid->column('stock_category_id', __('Stock category'))
            ->display(function ($stock_category_id) {
                $category = StockCategory::find($stock_category_id);
                return $category->name;
            })->sortable();


        $grid->column('buying_price', __('Investment'))->sortable();
        $grid->column('selling_price', __('Expected Sales'))->sortable();
        $grid->column('expected_price', __('Expected Profits'))->sortable();
        $grid->column('earned_price', __('Earned Profits'))->sortable();
        //  $grid->column('measurements_unit', __('Measurements unit'));
        $grid->column(
            'current_quantity',
            __('Current quantity')
        )->display(function ($current_quantity) {
            return number_format($current_quantity) . ' ' . $this->measurements_unit;
        })->sortable();

        $grid->column('reorder_level', __('Reorder Level'))->display(function ($reorder_level) {
            return number_format($reorder_level) . ' ' . $this->measurements_unit;
        })->sortable()->editable();

        $grid->column('description', __('Description'))->hide();
        $grid->column('in_stock', __('In Stock'))
            ->dot([
                "Yes" => "success",
                "No" => "danger"
            ])->sortable()
            ->filter([
                "Yes" => "In Stock",
                "No" => "Out of Stock"
            ]);

        $grid->column('status', __('Status'))->label(
            [
                "Active" => 'sucess',
                "Inactive" => 'danger'
            ]
        )->filter([
            "Active" => 'Active',
            "Inactive" => 'Inactive'
        ]);

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
        $show = new Show(StockSubCategory::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('company_id', __('Company id'));
        $show->field('stock_category_id', __('Stock category id'));
        $show->field('name', __('Name'));
        $show->field('description', __('Description'));
        $show->field('status', __('Status'));
        $show->field('image', __('Image'));
        $show->field('buying_price', __('Buying price'));
        $show->field('selling_price', __('Selling price'));
        $show->field('expected_price', __('Expected price'));
        $show->field('earned_price', __('Earned price'));
        $show->field('measurements_unit', __('Measurements unit'));
        $show->field('current_quantity', __('Current quantity'));
        $show->field('reorder_level', __('Reorder level'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new StockSubCategory());
        $u = Admin::user();

        $categories = StockCategory::where([
            'company_id' => $u->company_id,
            'status' => "active"
        ])->get()->pluck('name', 'id');

        $form->hidden('company_id', __('Company id'))->default($u->company_id);
        $form->select('stock_category_id', __('Stock category '))->options($categories)->rules('required');
        $form->text('name', __('Name'))->required();
        $form->textarea('description', __('Description'));

        $form->image('image', __('Image'));

        $form->text('measurements_unit', __('Measurements unit'))->rules("required");
        // $form->text('current_quantity', __('Current quantity'))->rules('required');
        $form->decimal('reorder_level', __('Reorder level'))->rules('required');
        $form->radio('status', __('Status'))->options([
            "active" => "Active",
            "inactive" => "Inactive",
        ])->default("active");

        return $form;
    }
}
