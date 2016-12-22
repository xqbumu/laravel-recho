<?php

namespace Intendant\{$stub_intendant_zone_upper}\Controllers\Incore;

use Intendant\{$stub_intendant_zone_upper}\Auth\Database\Administrator;
use Intendant\{$stub_intendant_zone_upper}\Auth\Database\OperationLog;
use Intendant\{$stub_intendant_zone_upper}\Facades\Incore;

use Inchow\Incore\Grid;
use Inchow\Incore\Layout\Content;
use Illuminate\Routing\Controller;

class LogController extends Controller
{
    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Incore::content(function (Content $content) {
            $content->header(trans('docore::lang.operation_log'));
            $content->description(trans('docore::lang.list'));

            $grid = Incore::grid(OperationLog::class, function (Grid $grid) {
                $grid->model()->orderBy('id', 'DESC');

                $grid->id('ID')->sortable();
                $grid->user()->name();
                $grid->method()->value(function ($method) {
                    $color = array_get(OperationLog::$methodColors, $method, 'grey');

                    return "<span class=\"badge bg-$color\">$method</span>";
                });
                $grid->path()->label('info');
                $grid->ip()->label('primary');
                $grid->input()->value(function ($input) {
                    $input = json_decode($input, true);
                    $input = array_except($input, '_pjax');

                    return '<code>'.json_encode($input, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE).'</code>';
                });

                $grid->created_at(trans('docore::lang.created_at'));

                $grid->rows(function ($row) {
                    $row->actions('delete');
                });

                $grid->disableCreation();

                $grid->filter(function ($filter) {
                    $filter->is('user_id', 'User')->select(Administrator::all()->pluck('name', 'id'));
                    $filter->is('method')->select(array_combine(OperationLog::$methods, OperationLog::$methods));
                    $filter->like('path');
                    $filter->is('ip');

                    $filter->useModal();
                });
            });

            $content->body($grid);
        });
    }

    public function destroy($id)
    {
        $ids = explode(',', $id);

        if (OperationLog::destroy(array_filter($ids))) {
            return response()->json([
                'status'  => true,
                'message' => trans('docore::lang.delete_succeeded'),
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => trans('docore::lang.delete_failed'),
            ]);
        }
    }
}
