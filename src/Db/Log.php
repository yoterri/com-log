<?php
namespace Com\Component\Log\Db;

use Com\Db\AbstractDb;
use Com\Interfaces\LazyLoadInterface;

class Log extends AbstractDb implements LazyLoadInterface
{
    protected $tableName = 'log';
        
}
