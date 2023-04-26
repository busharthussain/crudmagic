<?php
namespace bushart\crudmagic;

class GridDSN
{
    // constant for query reges
    const DB_QUERY_REGEXP = '/(\?)/';

    /**
     * @var \PDO
     */
    private $pdo;

    public function __construct()
    {
        $this->pdo = DB::connection()->getPdo();
    }

    /**
     * @param $str
     *
     * @return string
     */
    public function escape($str)
    {
        return rtrim(ltrim($this->pdo->quote($str), '\''), '\'');
    }


    /**
     * Query args replacements (drupal inspired)
     *
     * This has to be static..
     */
    public static function queryArgsReplace($match, &$factory = false)
    {
        static $args = NULL;
        static $objEscapeFactory; // since its a static function we need to get db factory from
        if (is_object($factory))
        {
            $args = $match;
            $objEscapeFactory = $factory;

            return;
        }

        switch ($match[1])
        {
            case '?':
            {
                if (is_array($args))
                    return $objEscapeFactory->escape(array_shift($args));

                return '?';
            }
        }
    }

    /**
     * @param      $query
     * @param null $args
     *
     * @return null|\PDOStatement
     */
    public function query($query, $args = null)
    {
        $this->queryArgs = $args;
        $result = null;

        try {
            $this->queryArgsReplace($args, $this);
            $query = preg_replace_callback(self::DB_QUERY_REGEXP, '\GridDSN::queryArgsReplace', $query);
            $result = $this->pdo->query($query);
        }
        catch(PDOException $e)
        {
            Log::error('SQL Query: '. "\n" . $query . "\n" . 'Error:'  .  "\n"  . $e->getMessage());
            //echo 'SQL Query: '. "\n" . nl2br($query) . "\n" . 'Error:'  .  "\n"  . $e->getMessage(); die;
        }

        return $result;
    }

    /**
     * @param      $query
     * @param null $args
     *
     * @return bool|mixed
     */
    public function fetchValue($query, $args = null)
    {
        $result = $this->query($query, $args);
        if ($result && $result->rowCount()) {
            $arrResult = @array_pop($result->fetchArray(PDO::FETCH_ASSOC));
        }
        else
            $arrResult = false;

        return $arrResult;
    }

    /**
     * @param      $query
     * @param null $args
     *
     * @return bool|mixed
     */
    public function fetchArray($query, $args = null)
    {
        $result = $this->query($query, $args);
        if ($result && $result->rowCount()) {
            $arrResult = $result->fetch(PDO::FETCH_ASSOC);
        }
        else
            $arrResult = false;

        return $arrResult;
    }

    /**
     * @param       $query
     * @param null  $args
     * @param array $arrExtra
     *
     * @return array|bool
     */
    public function fetchArrayAll($query, $args = null, $arrExtra = array())
    {
        $result = $this->query($query, $args);
        if ($result && $result->rowCount())
        {
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                if (isset($arrExtra['pk']) && $row[$arrExtra['pk']])
                    $arrResult[$row[$arrExtra['pk']]] = $row;
                else
                    $arrResult[] = $row;
            }
        }
        else
            $arrResult = false;

        return $arrResult;
    }
}