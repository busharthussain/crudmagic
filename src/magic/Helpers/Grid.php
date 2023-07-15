<?php
namespace bushart\crudmagic\Helpers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class Grid
{
    private static $dsn;

    public static function runSql($grid, $type = 'normal')
    {
        $currentPage = $grid['page'];
        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });

        $sql = $grid['query'];
        $gridFields = (isset($grid['gridFields'])) ? $grid['gridFields'] : '';
        if ($type == 'un_prepared') {
            DB::unprepared("SET sql_mode = ''");
        }
        if ($type == 'simple') {
            $totalRecord = $sql->count();
            $records = $sql->simplePaginate($grid['perPage'])->setPageName('page');
            $isSimple = true;
        } else {
            $records = $sql->paginate($grid['perPage'])->setPageName('page');
            $totalRecord = $records->total();
            $isSimple = false;
        }
        /*print_r($records->items());
        exit;*/
        $record_item = [];
        if (count($records->items()) > 0) {
            foreach ($records->items() as $item) {
                $record_item[] = $item;
            }
        }
        $data = [
            'result' => $record_item,
            'total' => $totalRecord,
            'pager' => CrudHelpers::make_complete_pagination_block($records, 'long', $isSimple, $totalRecord),
            'gridFields' => $gridFields
        ];
        return $data;

    }

    public static function getDSN($dsn_name = 'default', $reBuild = false)
    {
        self::$dsn = new GridDSN();

        return self::$dsn;
    }

    public static function tokenizeGridFields($fields)
    {
        return "'" . implode("', '", array_keys($fields)) . "'";
        return implode(',', array_keys($fields));
    }

    function quote_escape($str)
    {
        return self::getDSN()->escape($str);
    }
}
