<?php
namespace bushart\crudmagic;

class CrudHelpers
{
    /**
     * This is used to format errors
     *
     * @param $data
     *     array:2 [
     * "email" => array:1 [
     * 0 => "The email has already been taken."
     * ]
     * "mobile_number" => array:1 [
     * 0 => "The mobile number has already been taken."
     * ]
     * ]
     * @return array
     *
     * array:2 [
     * 0 => "The email has already been taken."
     * 1 => "The mobile number has already been taken."
     * ]
     */
    static function formatErrors($data)
    {
        $errors = [];
        if (!empty($data)) {
            foreach ($data as $row) {
                if ($row) {
                    foreach ($row as $value) {
                        $errors[] = $value;
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * make_complete_pagination_block
     * @param $obj
     * @param string $type | three possible values 1)short (for short paragraph) 2)long (for long paragraph) 3) null (for no paragraph) .
     * @return  complete pagination block
     */
    static function make_complete_pagination_block($obj, $type = null)
    {
        $info = "";
        $end = $obj->currentPage() * $obj->perPage();
        $start = $end - ($obj->perPage() - 1);
        $current_page = $obj->currentPage();
        $last_page = $obj->lastPage();
        if ($start < 1) {
            $start = 1;
        }
        $total = $obj->total();
        if ($end > $total) {
            $end = $total;
        }
        if ($type) {
            if ($total > 0) {
                if ($type == 'long') {
                    $info = "<div class='pager-info'><p>Showing $start to $end of $total Records.</p><div class='clr'></div></div>";
                } else {
                    $info = "<div class='pager-info'><p>Displaying $current_page of $last_page Pages</p><div class='clr'></div></div>";
                }
            }
        }

        return view('crudmagic::_pager', compact('info', 'obj'))->render();
    }

    /**
     * get_pager_info_paragraph | it will a paginator object provided by laravel paginate method and will return a paragraph line item with the info about total records and showing records range according to the current page.
     * @param array $obj | paginator object provided by laravel paginate method
     * @param string $type | three possible values 1)short (for short paragraph) 2)long (for long paragraph) 3) null (for no paragraph) .
     * @return returns string | returns a string (paragraph line with star end and total records according to the current page.)
     *
     */
    static function get_pager_info_paragraph($obj, $type = 'long')
    {
        $info = "";
        $end = $obj->currentPage() * $obj->perPage();
        $start = $end - ($obj->perPage() - 1);
        $current_page = $obj->currentPage();
        $last_page = $obj->lastPage();
        if ($start < 1) {
            $start = 1;
        }
        $total = $obj->total();
        if ($end > $total) {
            $end = $total;
        }
        if ($type) {
            if ($total > 0) {
                if ($type == 'long') {
                    $info = "<div class='pager-info'><p>Showing $start to $end of $total Records.</p><div class='clr'></div></div>";
                } else {
                    $info = "<div class='pager-info'><p>Displaying $current_page of $last_page Pages</p><div class='clr'></div></div>";
                }
            }
        }

        return $info;
    }

    /**
     * This is used to generate headers dynamically
     *
     * @param $array
     * @return array
     *      array:14 [▼
     * 0 => array:3 [▼
     * "name" => "Sr. NO"
     * "key" => "srn"
     * "isSorter" => true
     * ]
     * 1 => array:3 [▶]
     * 2 => array:3 [▶]
     */
    static function generateHeaders($array)
    {
        $headers = [];
        foreach ($array as $key => $row) {
            $headers[$key]['name'] = $row[0];
            if (isset($row[1])) {
                $keyName = $row[1];
            } else {
                $keyName = str_replace(' ', '_', strtolower($row[0]));
            }
            $headers[$key]['key'] = $keyName;
            $headers[$key]['isSorter'] = (isset($row[2])) ? $row[2] : true;
        }

        return $headers;
    }

    /**
     * This is used to get indexes
     *
     * @param $array
     * @param $values
     * @return $array
     *
     *     array:2 [▼
     * "               order_number" => 1
     * "               custom_label" => 24
     * ]
     *
     */
    static function getIndexByValue($array, $values)
    {
        $indexes = [];
        foreach ($values as $index) {
            foreach ($array as $key => $row) {
                $rowValue = str_replace([' ', '/', ' – '], '_', strtolower($row));
                if ($index == $rowValue) {
                    $indexes[$rowValue] = $key;
                    break;
                }
            }
        }

        return $indexes;
    }

    /**
     * This is used to find header by index
     *
     * @param $array
     * @param $index
     * @return bool
     */
    static function findHeaderByIndex($array, $index)
    {
        $isHeaderExist = false;
        foreach ($array as $row) {
            $rowValue = str_replace(' ', '_', strtolower($row));
            if ($index == $rowValue) {
                $isHeaderExist = true;
            }
        }

        return $isHeaderExist;
    }

    /**
     * This is used to convert date to data base time format
     *
     * @param $date
     * @return false|string
     */
    static function databaseDateFromat($date)
    {
        return date_format(new \DateTime($date), 'Y-m-d');
    }

    /**
     * This is used to generate grid headers
     *
     * @param $data
     * @return array
     *              array:9 [
     * 0 => array:2 [
     * "name" => "transaction_creation_date"
     * "isDisplay" => true
     * ]
     * 1 => array:2 [
     * "name" => "type"
     * "isDisplay" => true
     * ]
     * 2 => array:2 [
     * "name" => "order_number"
     * "isDisplay" => true
     * ]
     */
    static function generateGridHeaders($data)
    {
        $arr = [];
        foreach ($data as $key => $row) {
            $arr[$key]['name'] = $row[0];
            $arr[$key]['isDisplay'] = (isset($row[1])) ? $row[1] : true;
        }
        return $arr;
    }

    /**
     * This is used to generate popup headers
     *
     * @param $data
     * @return array
     */
    static function generateShowHeaders($data)
    {
        $arr = [];
        foreach ($data as $key => $row) {
            $arr[$key]['name'] = $row[0];
            if (isset($row[1])) {
                $keyName = $row[1];
            } else {
                $keyName = str_replace(' ', '_', strtolower($row[0]));
            }
            $arr[$key]['key'] = $keyName;
        }

        return $arr;
    }

    static function test()
    {
        dd('Package 234 successfully.');
    }
}
