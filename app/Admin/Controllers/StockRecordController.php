<?php

namespace App\Admin\Controllers;

use App\Models\StockCategory;
use App\Models\StockItem;
use App\models\StockRecord;
use App\Models\StockSubCategory;
use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class StockRecordController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'StockRecord';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new StockRecord());

        $u = Admin::user();
        $grid->model()->where('company_id', $u->company_id);
        $grid->disableBatchActions();

        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->like('name', 'Name');
            $u = Admin::user();

            $filter->equal('stock_sub_category_id', 'Stocj Sub Category')
                ->select(StockSubCategory::where([
                    'company_id' => $u->company_id
                ])->pluck('name', 'id')); // we do this foe the rest of the columns
            $filter->equal("sku", 'SKU');
            $filter->between('selling_price', 'Selling Price')
                ->decimal();
        });

        $grid->column('id', __('ID'))->sortable();
        // $grid->column('company_id', __('Company id'));
        $grid->column('stock_item_id', __('Stock item '))
            ->display(function ($stock_item_id) {
                $stock_item = StockItem::find($stock_item_id);
                if ($stock_item) {
                    return $stock_item->name;
                } else {
                    return "N/A";
                }
            })->sortable();
        $grid->column('stock_category_id', __('Stock category'))
            ->display(function ($stock_category_id) {
                $stock_category = StockCategory::find($stock_category_id);
                if ($stock_category) {
                    return $stock_category->name;
                } else {
                    return "N/A";
                }
            })->sortable();
        $grid->column('stock_sub_category_id', __('Stock sub category'))
            ->display(function ($stock_sub_category_id) {
                $stock_sub_category = StockSubCategory::find($stock_sub_category_id);
                if ($stock_sub_category) {
                    return $stock_sub_category->name;
                } else {
                    return "N/A";
                }
            })->sortable();
        $grid->column('sku', __('SKU'))->sortable();
        $grid->column('name', __('Name'))->sortable();
        //  $grid->column('measurement_unit', __('Measurement unit'))->sortable()->hide();
        $grid->column('type', __('Type'))->sortable()
            ->dot([
                [
                    'Sale' => 'success',
                    'Damage' => 'danger',
                    'Expired' => 'warning',
                    'Lost' => 'info',
                    'Internel Use ' => 'primary',
                    'Others' => 'default',
                ]
            ]);
        $grid->column('quantity', __('Quantity'))->sortable()->display(function ($quantity) {
            return number_format($quantity) . "-" . $this->measurement_unit;
        })->totalRow(function ($amount) {
            return "<span class = 'text-danger'>" . number_format($amount) . "</span>";
        });
        $grid->column('selling_price', __('Selling price'))->sortable()
            ->sortable()->display(function ($selling_price) {
                return number_format($selling_price);
            });
        $grid->column('total_sales', __('Total sales'))->sortable()->display(function ($total_sales) {
            return number_format($total_sales);
        });
        $grid->column('created_at', __('Created at'))
            ->display(function ($created_at) {
                return date('y-m-d', strtotime($created_at));
            })->sortable()->hide();
        $grid->column('decription', __('Decription'))->hide();
        $grid->column('created_by_id', __('Created by id'))
            ->display(function ($created_by_id) {
                $user = User::find($created_by_id);
                if ($user) {
                    return $user->name;
                } else {
                    return "N/A";
                }
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
        $show = new Show(StockRecord::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('company_id', __('Company id'));
        $show->field('stock_item_id', __('Stock item id'));
        $show->field('stock_category_id', __('Stock category id'));
        $show->field('stock_sub_category_id', __('Stock sub category id'));
        $show->field('created_by_id', __('Created by id'));
        $show->field('sku', __('Sku'));
        $show->field('name', __('Name'));
        $show->field('measurement_unit', __('Measurement unit'));
        $show->field('decription', __('Decription'));
        $show->field('type', __('Type'));
        $show->field('quantity', __('Quantity'));
        $show->field('selling_price', __('Selling price'));
        $show->field('total_sales', __('Total sales'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new StockRecord());
        $u = Admin::user();

        $form->hidden('company_id', __('Company id'))->default($u->id);

        $stock_item_ajax_url = url('api/stock-items');
        $stock_item_ajax_url = $stock_item_ajax_url . '?company_id=' . $u->company_id;
        $form->select('stock_item_id', __('Stock item'))
            ->ajax($stock_item_ajax_url)
            ->options(function ($id) {
                $item = StockItem::find($id);
                if ($item) {
                    return [
                        $item->id => $item->name
                    ];
                } else {
                    return [];
                }
            })
            ->rules('required');
        //  $form->number('stock_category_id', __('Stock category id'));
        // $form->number('stock_sub_category_id', __('Stock sub category id'));
        $form->hidden('created_by_id', __('Created by id'))->default($u->id);
        // $form->text('sku', __('Sku'));
        // $form->text('name', __('Name'));
        // $form->text('measurement_unit', __('Measurement unit'));
        $form->radio('type', __('Type'))
            ->options([
                'Sale' => 'Sale',
                'Damage' => 'Damage',
                'Expired' => 'Expired',
                'Lost' => 'Lost',
                'Internel Use ' => 'Internel Use ',
                'Others' => 'Others',
            ])->rules("required");
        $form->decimal('quantity', __('Quantity'))->rules("required");
        // $form->decimal('selling_price', __('Selling price'));
        // $form->decimal('total_sales', __('Total sales'));
        $form->textarea('decription', __('Decription'));


        return $form;
    }
}
