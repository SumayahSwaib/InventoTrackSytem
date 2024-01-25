<?php

namespace App\Admin\Controllers;

use App\Models\FinacialPeriod;
use App\Models\StockCategory;
use App\models\StockItem;
use App\Models\StockSubCategory;
use App\Models\User;
use App\Models\Utils;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class StockItemController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Stock Items';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()

    {


        $grid = new Grid(new StockItem());

        $u = Admin::user();
        $grid->model()->where('company_id', $u->company_id);
        $grid->disableBatchActions();
        $grid->quickSearch('name');




        $grid->column('id', __('Id'))->hide();
        $grid->column('image', __('Image'))->image();

        //  $grid->column('updated_at', __('Updated at'));
        //   $grid->column('company_id', __('Company id'));
        $grid->column('name', __('Product Name'));

        $grid->column('stock_category_id', __('Stock category'))
        ->display(function ($stock_category_id) {
            $stock_category = StockCategory::find($stock_category_id);
            if($stock_category){
                return $stock_category->name;
            }else{
                return "N/A";
            }
        })->sortable();
        
        $grid->column('stock_sub_category_id', __('Stock sub category'))
         ->display(function ($stock_sub_category_id) {
            $stock_sub_category = StockSubCategory::find($stock_sub_category_id);
            if($stock_sub_category){
                return $stock_sub_category->name;
            }else{
                return "N/A";
            }
        })->sortable();
        $grid->column('finacial_period_id', __('Finacial period'))
        ->display(function ($finacial_period_id) {
            $finacial_period = Utils::getActiveFinacialPeriod($finacial_period_id);
            if($finacial_period ){
                return $finacial_period ->name;
            }else{
                return "N/A";
            }
        })->sortable();
        //$grid->column('gallery', __('Gallery'));
        // $grid->column('barcode', __('Barcode'));
        $grid->column('sku', __('Sku'))->sortable();
       // $grid->column('generate_sku', __('Generate sku'));
        $grid->column('buying_price', __('Buying Price'))->sortable();
        $grid->column('selling_price', __('Selling Price'))->sortable();
        $grid->column('original_quantity', __('Original Quantity'))->sortable();
        $grid->column('current_quantity', __('Current Quantity'))->sortable();
        $grid->column('created_by_id', __('Created by'))
        ->display(function ($created_by_id) {
            $user = User::find($created_by_id);
            if($user){
                return $user->name;
            }else{
                return "N/A";
            }
        })->sortable();
        $grid->column('created_at', __('Registered'))
        ->display(function ($created_at) {
            return date('y-m-d', strtotime($created_at));
        })->sortable()->hide();
        $grid->column('description', __('Description'))->hide();


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
        $show = new Show(StockItem::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('company_id', __('Company id'));
        $show->field('created_by_id', __('Created by id'));
        $show->field('stock_category_id', __('Stock category id'));
        $show->field('stock_sub_category_id', __('Stock sub category id'));
        $show->field('finacial_period_id', __('Finacial period id'));
        $show->field('name', __('Name'));
        $show->field('description', __('Description'));
        $show->field('image', __('Image'));
        $show->field('gallery', __('Gallery'));
        $show->field('barcode', __('Barcode'));
        $show->field('sku', __('Sku'));
        $show->field('generate_sku', __('Generate sku'));
        $show->field('buying_price', __('Buying price'));
        $show->field('selling_price', __('Selling price'));
        $show->field('original_quantity', __('Original quantity'));
        $show->field('current_quantity', __('Current quantity'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()

    {

        $u = Admin::user();
        $financial_period = Utils::getActiveFinacialPeriod($u->company_id);
        if ($financial_period == null) {
            return admin_error("please create a finacial period first");
        }

        $form = new Form(new StockItem());

        $form->hidden('company_id', __('Company id'))->default($u->company_id);
        $form->hidden('created_by_id', __('Created by id'))->default($u->id);

        /* if ($form->isCreating()) {
        } */
        $sub_cat_ajax_url = url('api/stock-sub-categories');
        $sub_cat_ajax_url = $sub_cat_ajax_url . '?company_id=' . $u->company_id;


        //  $form->select('stock_category_id', __('Stock Category'))->ajax('/admin/api/stock-categories');
        $form->select('stock_sub_category_id', __('Stock sub category'))
            ->ajax($sub_cat_ajax_url)
            ->options(function ($id) {
                $sub_cat = StockSubCategory::find($id);
                if ($sub_cat) {
                    return [
                        $sub_cat->id => $sub_cat->name_text . "(" . $sub_cat->measurements_unit . ")"
                    ];
                } else {
                    return [];
                }
            })
            ->rules('required');

        $form->hidden('finacial_period_id', __('Finacial period id'));
        $form->text('name', __('Name'))->rules('required');
        $form->image('image', __('Image'))->uniqueName();
        //  $form->textarea('barcode', __('Barcode'));
        // $form->text('sku', __('SKU'));


        if ($form->isEditing()) {
            $form->radio('update_sku', __('Update SKU(Batch Number)'))
                ->options([
                    'Yes' => 'Yes',
                    'No' => 'No'
                ])->when('Yes', function (Form $form) {
                    $form->text('sku', __('ENTER SKU (Batch Number)'));
                })->rules('required')->default('NO');
        } else {
            $form->hidden('update_sku', __('Update SKU(Batch Number)'))->default('NO');
            $form->radio('generate_sku', __('Generate sku(Batch Number)'))
                ->options([
                    'Manual' => 'Manual',
                    'Auto' => 'Auto'
                ])->when('Manual', function (Form $form) {
                    $form->text('sku', __('ENTER SKU (Batch Number)'))->rules('required');
                })->rules('required');
        }
        $form->multipleImage('gallery', __('Gallery'))
            ->removable()
            ->uniqueName();

        $form->decimal('buying_price', __('Buying price'))
            ->rules('required')
            ->default(0.00);

        $form->decimal('selling_price', __('Selling price'))
            ->rules('required')
            ->default(0.00);

        $form->decimal('original_quantity', __('Original quantity'))
            ->rules('required')
            ->default(0.00);

        $form->textarea('description', __('Description'));


        return $form;
    }
}
