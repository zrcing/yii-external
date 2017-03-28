<?php
/**
 * 扩展属性
 *
 * @author Liao Gengling <liaogling@gmail.com>
 */
namespace YiiExternal\Db\Traits;

use YiiExternal\Exception\InvalidArgumentException;

trait CActiveRecordAttribute
{
    /**
     * 所有属性对象
     *
     * @return array [attribute_name => ActiveRecord]
     */
    public function attrs()
    {
        $ar = static::$attrActiveRecord;
        $relatedKey = static::$attrRelatednKey;
        $primaryKey = $this->primaryKey();
        if (is_null($this->attrs)) {
            $attrs = $ar::model()->findAll("{$relatedKey}=:id", [':id' => $this->$primaryKey]);
            $this->attrs = [];
            foreach ($attrs as $v) {
                $this->attrs[$v->attribute_name] = $v;
            }
        }

        return $this->attrs;
    }

    /**
     * 通过属性名称获取对象
     *
     * @param $name
     * @return mixed ActiveRecord|null
     */
    public function getAttr($name)
    {
        $this->verifyFields($name);
        $attrs = $this->attrs();
        if (! isset($attrs[$name])) {
            return null;
        }
        return $attrs[$name];
    }

    /**
     * 通过属性名称获取对应的值
     *
     * @param $name
     * @return mixed string|null
     */
    public function getAttrValue($name)
    {
        $this->verifyFields($name);
        $attrs = $this->attrs();
        if (! isset($attrs[$name])) {
            return null;
        }
        return $attrs[$name]->attribute_value;
    }

    /**
     * 设置属性且保存到数据库
     *
     * @param string $name
     * @param string $value
     */
    public function setAttr($name, $value)
    {
        $this->verifyFields($name);
        $attr = $this->getAttr($name);
        if (is_null($attr)) {
            $attr = new static::$attrActiveRecord();
            $attr->{static::$attrRelatednKey} = $this->{$this->primaryKey()};
            $attr->attribute_name = $name;
            $attr->created_at = time();
        }
        $attr->attribute_value = $value;
        $attr->updated_at = time();
        $attr->save();

        $this->attrs[$name] = $attr;
    }

    /**
     * 据据属性名称删除此属性
     *
     * @param string $name
     */
    public function deleteAttr($name)
    {
        $this->verifyFields($name);
        $attr = $this->getAttr($name);
        if ($attr) {
            unset($this->attrs[$attr->attribute_name]);
            $attr->delete();
        }
    }

    /**
     * 删除所有属性
     */
    public function deleteAttrAll()
    {
        $attrs = $this->attrs();
        foreach ($attrs as $attr) {
            unset($this->attrs[$attr->attribute_name]);
            $attr->delete();
        }
    }

    /**
     * 验证属性名称是否在预设置的字段中
     *
     * @params string $name
     */
    private function verifyFields($name)
    {
        $ar = static::$attrActiveRecord;
        if (! in_array($name, $ar::$attrFields)) {
            throw new InvalidArgumentException('Attribute active record field is wrong.', 11);
        }
    }
}