<?php
/**
 * @author Liao Gengling <liaogling@gmail.com>
 */
namespace YiiExternal\Db;

class ActiveRecord extends \CActiveRecord
{
    /**
     * 属性表对象
     * @var string
     */
    protected static $attrActiveRecord;

    /**
     * 属性表关联的字段
     * @var string
     */
    protected static $attrRelatedKey;

    /**
     * 属性表字段
     * @var array
     */
    public static $attrFields = [];

    /**
     * 属性表对象集
     * @var array [field=>CActiveRecord]
     */
    protected $attrs;
}