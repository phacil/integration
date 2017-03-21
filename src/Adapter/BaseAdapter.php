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
        $queryString = 'SELECT ' . $query->select . ' FROM ' . $query->from;
       
        $queryString .= $query->isNotNullReturn($query->join);
        $queryString .= $query->isNotNullReturn($query->where, ' WHERE ');
        $queryString .= $query->isNotNullReturn($query->groupBy, ' GROUP BY ');
        $queryString .= $query->isNotNullReturn($query->having , ' HAVING ');
        $queryString .= $query->isNotNullReturn($query->orderBy, ' ORDER BY ');
        $queryString .= $query->isNotNullReturn($query->limit, ' LIMIT ');
        $queryString .= $query->isNotNullReturn($query->offset, ' OFFSET ');
        
        return $queryString;
    }
    
    
    
}
