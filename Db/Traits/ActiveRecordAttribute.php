<?php
/**
 * @author Liao Gengling <liaogling@gmail.com>
 */
namespace YiiExternal\Db\Traits;

use YiiExternal\Exception\InvalidArgumentException;

trait CActiveRecordAttribute
{
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

    public function getAttr($name)
    {
        $this->verifyFields($name);
        $attrs = $this->attrs();
        if (isset($attrs[$name])) {
            return $attrs[$name];
        }
        return null;
    }

    public function getAttrValue($name)
    {
        $this->verifyFields($name);
        $attrs = $this->attrs();
        if (isset($attrs[$name])) {
            return $attrs[$name]->attribute_value;
        }
        return null;
    }

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

    public function deleteAttr($name)
    {
        $this->verifyFields($name);
        $attr = $this->getAttr($name);
        if ($attr) {
            unset($this->attrs[$attr->attribute_name]);
            $attr->delete();
        }
    }

    public function deleteAttrAll()
    {
        $attrs = $this->attrs();
        foreach ($attrs as $attr) {
            unset($this->attrs[$attr->attribute_name]);
            $attr->delete();
        }
    }


    private function verifyFields($name)
    {
        $ar = static::$attrActiveRecord;
        if (! in_array($name, $ar::$attrFields)) {
            throw new InvalidArgumentException('Attribute active record field is wrong.', 11);
        }
    }
}