<?php
//PIXIE
namespace Phacil\Integration\Adapter;
use Phacil\Integration\Database\Query;

abstract class BaseAdapter
{
    /**
     * @var string
     */
    protected $pdo = null;
    const SANITIZER = '`';
    protected $op = array('=','!=','<','>','<=','>=','<>');
    /**
     * 
     */
    public function __construct()
    {        
    }
    
    /**
     * @param $config
     *
     * @return mixed
     */
    abstract protected function doConnect($config);
    
    /**
     * @param midex $select
     * @param mixed $from
     * @param mixed $join
     * 
     * @return String
     */
    abstract protected function makeSelect($select, $from = null, $join = [], $pdo = null);
    
    /**
     * @param $config
     * @return PDO
     */
    public function connect($config)
    {
        if (isset($config['options']) === false) {
            $config['options'] = [];
        }
        
        try {
            return $this->doConnect($config);
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }
    
    public function buildQuery(Query $query)
    {
        $queryString  = 'SELECT ' . $this->makeSelect($query->select, $query->from, $query->join, $query->pdo);
        $queryString .= ' FROM ' . $this->makeFrom($query->from);
       
        $queryString .= $this->makeJoin($query->join);
        $queryString .= $query->isNotNullReturn($query->where, ' WHERE ');
        $queryString .= $query->isNotNullReturn($query->groupBy, ' GROUP BY ');
        $queryString .= $query->isNotNullReturn($query->having , ' HAVING ');
        $queryString .= $query->isNotNullReturn($query->orderBy, ' ORDER BY ');
        $queryString .= $query->isNotNullReturn($query->limit, ' LIMIT ');
        $queryString .= $query->isNotNullReturn($query->offset, ' OFFSET ');
        
        return $queryString;
    }
    
    /*
     */
    
    public function makeFrom($from)
    {                        
        return $this->arrayStr($from, 'AS');;
    }
    
    /*

     */ 
    
    public function makeJoin(array $join)
    {
        $joins = null;
        
        foreach($join as $_table){ 
            
            if(count(explode(' as ', strtolower($_table['table']))) == 2){
                list($table, $alias) = explode(' as ', $_table['table']);
            }else if(count(explode(' ', strtolower($_table['table']))) == 2){
                list($table, $alias) = explode(' ', strtolower($_table['table']));
            }else{
                $alias = $table = $_table['table'];
            }
            
            $_table['table'] = $table . ' AS ' . $this->wrapSanitizer($_table['field1']);
            
            $where = '('. $this->wrapSanitizer($_table['field1']). ' ';
            $where .= (in_array($_table['op'],$this->op)?$_table['op']:' = ') . ' ';
            $where .= $this->wrapSanitizer($_table['field2']) . ')';
            
            $joins = $joins . ' ' 
                    . $_table['join'] . 'JOIN' . ' ' 
                    . $_table['table'] . ' ON ' . $where;
                
        }
        
        return $joins;
    }
    
    /**
     * Array concatenating method, like implode.
     * But it does wrap sanitizer and trims last glue
     *
     * @param array $pieces
     * @param       $glue
     * @param bool $wrapSanitizer
     *
     * @return string
     */
    protected function arrayStr(array $pieces, $gluePiece = ' ', $glue = ',', $wrapSanitizer = true)
    {
        $str = '';
        foreach ($pieces as $key => $piece) {
            if ($wrapSanitizer) {
                $piece = $this->wrapSanitizer($piece, true);
            }

            if (!is_int($key)) {
                $piece = ($wrapSanitizer ? $this->wrapSanitizer($key) : $key) . ' ' . $gluePiece . ' ' . $piece;
            }

            $str .= $piece . $glue;
        }

        return trim($str, $glue);
    }
    
    /**
     * Wrap values with adapter's sanitizer like, '`'
     *
     * @param $value
     *
     * @return string
     */
    protected function wrapSanitizer($value, $ignorePoint = false)
    {
//      Its a raw query, just cast as string, object has __toString()
        //pr($value);
        if ($value instanceof \Phacil\Integration\Database\Raw) {
            return (string)$value;
        } elseif ($value instanceof \Closure) {
            return $value;
        }

        // Separate our table and fields which are joined with a ".", like my_table.id
        if(strpos($value, '*') === false && $ignorePoint){
            return static::SANITIZER . $value . static::SANITIZER;
        }
        
        $valueArr = explode('.', $value, 2);
        foreach ($valueArr as $key => $subValue) {
            // Don't wrap if we have *, which is not a usual field
            $valueArr[$key] = trim($subValue) === '*'
                              ? $subValue 
                              : static::SANITIZER . $subValue . static::SANITIZER;
        }

        // Join these back with "." and return
        return implode('.', $valueArr);
    }
    
}
