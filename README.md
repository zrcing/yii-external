## Yii External 

Yii External is a extension package for Yii Framework , providing new feature for Yii.

## Getting Started
#### Console

Use the daemon directly on the command line.
```
php corn.php test test --daemon=start
php corn.php test test --daemon=stop
```

#### ActiveRecord
Create an association extension table in ActiveRecord.

````
CREATE TABLE `user_attributes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `attribute_name` varchar(255) NOT NULL DEFAULT '',
  `attribute_value` varchar(799) NOT NULL DEFAULT '',
  `created_at` int(11) DEFAULT '0',
  `updated_at` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_pgid` (`pg_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
````
````
class User extends PActiveRecord
{
    use YiiExternal\Db\Traits\CActiveRecordAttribute;

    protected static $attrActiveRecord = UserAttribute::class;

    protected static $attrRelatednKey = 'user_id';
}
````
````
class UserAttribute extends PActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'user_attributes';
    }

    public function primaryKey()
    {
        return 'id';
    }

    public static $attrFields = ['black_type', 'recommand_type'];
}
````
````
$user = User::model()->findByPk(10);
$attrs = $user->attrs();

$user->getAttr('black_type'); // return UserAttribute Object
$user->getAttrValue('black_type'); // return black_type's attribute_value
$user->setAttr('black_type', 2); // save  black_type's attribute_value

````
### License

Yii External is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).

