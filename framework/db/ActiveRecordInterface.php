<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\db;

use yii\base\StaticInstanceInterface;

/**
 * ActiveRecordInterface.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @author Carsten Brandt <mail@cebe.cc>
 * @since 2.0
 */
interface ActiveRecordInterface extends StaticInstanceInterface
{
    /**
     * Returns the primary key **name(s)** for this AR class.
     *
     * Note that an array should be returned even when the record only has a single primary key.
     *
     * For the primary key **value** see [[getPrimaryKey()]] instead.
     *
     * @return string[] the primary key name(s) for this AR class.   
     *
     * 返回此ActiveRecord类的主键名称。
     * 需要注意的是，即使此活动记录只有单个主键，也应当返回一个数组。
     * 此方法针对的是主键名称，如果想要获取主键的值，请查看[[getPrimaryKey()]]方法。
     * 结果：
     *   string[] | 表示此ActiveRecord类的主键名称。
     */
    public static function primaryKey();

    /**
     * Returns the list of all attribute names of the record.
     * @return array list of attribute names.
     *
     * 返回此活动记录的所有属性名称。
     * 结果：
     *   array | 属性名列表。
     */
    public function attributes();

    /**
     * Returns the named attribute value.
     * If this record is the result of a query and the attribute is not loaded,
     * `null` will be returned.
     * @param string $name the attribute name
     * @return mixed the attribute value. `null` if the attribute is not set or does not exist.
     * @see hasAttribute()
     *
     * 返回指定属性的值。
     * 如果此活动记录为查询的结果，且未加载参数指定的属性，则会返回`null`。
     * 参数：
     *   $name | string | 需要获取值的属性名
     * 结果：
     *   mixed | 指定属性名的值。 如果此属性未赋值或不存在，则返回`null`。
     * 相关：
     *   @see hasAttribute()
     */
    public function getAttribute($name);

    /**
     * Sets the named attribute value.
     * @param string $name the attribute name.
     * @param mixed $value the attribute value.
     * @see hasAttribute()
     *
     * 给指定的属性赋值。
     * 参数：
     *   $name  | string | 属性名
     *   $value | mixed  | 属性值
     * 相关：
     *   @see hasAttribute()
     */
    public function setAttribute($name, $value);

    /**
     * Returns a value indicating whether the record has an attribute with the specified name.
     * @param string $name the name of the attribute
     * @return bool whether the record has an attribute with the specified name.
     */
    public function hasAttribute($name);

    /**
     * Returns the primary key value(s).
     * @param bool $asArray whether to return the primary key value as an array. If true,
     * the return value will be an array with attribute names as keys and attribute values as values.
     * Note that for composite primary keys, an array will always be returned regardless of this parameter value.
     * @return mixed the primary key value. An array (attribute name => attribute value) is returned if the primary key
     * is composite or `$asArray` is true. A string is returned otherwise (`null` will be returned if
     * the key value is `null`).
     */
    public function getPrimaryKey($asArray = false);

    /**
     * Returns the old primary key value(s).
     * This refers to the primary key value that is populated into the record
     * after executing a find method (e.g. find(), findOne()).
     * The value remains unchanged even if the primary key attribute is manually assigned with a different value.
     * @param bool $asArray whether to return the primary key value as an array. If true,
     * the return value will be an array with column name as key and column value as value.
     * If this is `false` (default), a scalar value will be returned for non-composite primary key.
     * @property mixed The old primary key value. An array (column name => column value) is
     * returned if the primary key is composite. A string is returned otherwise (`null` will be
     * returned if the key value is `null`).
     * @return mixed the old primary key value. An array (column name => column value) is returned if the primary key
     * is composite or `$asArray` is true. A string is returned otherwise (`null` will be returned if
     * the key value is `null`).
     */
    public function getOldPrimaryKey($asArray = false);

    /**
     * Returns a value indicating whether the given set of attributes represents the primary key for this model.
     * @param array $keys the set of attributes to check
     * @return bool whether the given set of attributes represents the primary key for this model
     */
    public static function isPrimaryKey($keys);

    /**
     * Creates an [[ActiveQueryInterface]] instance for query purpose.
     *
     * The returned [[ActiveQueryInterface]] instance can be further customized by calling
     * methods defined in [[ActiveQueryInterface]] before `one()` or `all()` is called to return
     * populated ActiveRecord instances. For example,
     *
     * ```php
     * // find the customer whose ID is 1
     * $customer = Customer::find()->where(['id' => 1])->one();
     *
     * // find all active customers and order them by their age:
     * $customers = Customer::find()
     *     ->where(['status' => 1])
     *     ->orderBy('age')
     *     ->all();
     * ```
     *
     * This method is also called by [[BaseActiveRecord::hasOne()]] and [[BaseActiveRecord::hasMany()]] to
     * create a relational query.
     *
     * You may override this method to return a customized query. For example,
     *
     * ```php
     * class Customer extends ActiveRecord
     * {
     *     public static function find()
     *     {
     *         // use CustomerQuery instead of the default ActiveQuery
     *         return new CustomerQuery(get_called_class());
     *     }
     * }
     * ```
     *
     * The following code shows how to apply a default condition for all queries:
     *
     * ```php
     * class Customer extends ActiveRecord
     * {
     *     public static function find()
     *     {
     *         return parent::find()->where(['deleted' => false]);
     *     }
     * }
     *
     * // Use andWhere()/orWhere() to apply the default condition
     * // SELECT FROM customer WHERE `deleted`=:deleted AND age>30
     * $customers = Customer::find()->andWhere('age>30')->all();
     *
     * // Use where() to ignore the default condition
     * // SELECT FROM customer WHERE age>30
     * $customers = Customer::find()->where('age>30')->all();
     *
     * @return ActiveQueryInterface the newly created [[ActiveQueryInterface]] instance.
     *
     * 创建一个拥有[[ActiveQueryInterface]]接口协议的实例，用于查询。
     * 其结果返回的实例可以通过调用[[ActiveQueryInterface]]接口协议中已定义的方法来增加查询条件，最后调用`one()`或者`all()`
     * 方法，将返回一个ActiveRecord类的实例。例如：
     * ```php
     * // 查询id为1的顾客
     * $customer = Customer::find()->where(['id' => 1])->one();
     *
     * // 查询所有活跃的顾客，并通过年龄来排序
     * $customers = Customer::find()
     *     ->where(['status' => 1])
     *     ->orderBy('age')
     *     ->all();
     * ```
     * 
     * [[BaseActiveRecord::hasOne()]]和[[BaseActiveRecord::hasMany()]]通过调用此方法来创建一个关联查询。
     *
     * 你可以重写这个方法返回一个自定义的查询。例如：
     * 
     * ```php
     * class Customer extends ActiveRecord
     * {
     *     public static function find()
     *     {
     *         // 使用CustomerQuery类代替默认的ActiveQuery类
     *         return new CustomerQuery(get_called_class());
     *     }
     * }
     * ```
     * 下面的代码展示了如何给查询设定一个默认条件：
     * ```php
     * class Customer extends ActiveRecord
     * {
     *     public static function find()
     *     {
     *         return parent::find()->where(['deleted' => false]);
     *     }
     * }
     *
     * // 使用andWhere()/orWhere()，将会添加查询条件
     * // SELECT FROM customer WHERE `deleted`=:deleted AND age>30
     * $customers = Customer::find()->andWhere('age>30')->all();
     *
     * // 使用where()，将会覆盖掉默认的查询条件
     * // SELECT FROM customer WHERE age>30
     * $customers = Customer::find()->where('age>30')->all();
     * ```
     * 结果：
     *   ActiveQueryInterface实例 | 新创建的拥有[[ActiveQueryInterface]]接口协议的实例。
     */
    public static function find();

    /**
     * Returns a single active record model instance by a primary key or an array of column values.
     *
     * The method accepts:
     *
     *  - a scalar value (integer or string): query by a single primary key value and return the
     *    corresponding record (or `null` if not found).
     *  - a non-associative array: query by a list of primary key values and return the
     *    first record (or `null` if not found).
     *  - an associative array of name-value pairs: query by a set of attribute values and return a single record
     *    matching all of them (or `null` if not found). Note that `['id' => 1, 2]` is treated as a non-associative array.
     *    Column names are limited to current records table columns for SQL DBMS, or filtered otherwise to be limited to simple filter conditions.
     *
     * That this method will automatically call the `one()` method and return an [[ActiveRecordInterface|ActiveRecord]]
     * instance.
     *
     * > Note: As this is a short-hand method only, using more complex conditions, like ['!=', 'id', 1] will not work.
     * > If you need to specify more complex conditions, use [[find()]] in combination with [[ActiveQuery::where()|where()]] instead.
     *
     * See the following code for usage examples:
     *
     * ```php
     * // find a single customer whose primary key value is 10
     * $customer = Customer::findOne(10);
     *
     * // the above code is equivalent to:
     * $customer = Customer::find()->where(['id' => 10])->one();
     *
     * // find the customers whose primary key value is 10, 11 or 12.
     * $customers = Customer::findOne([10, 11, 12]);
     *
     * // the above code is equivalent to:
     * $customers = Customer::find()->where(['id' => [10, 11, 12]])->one();
     *
     * // find the first customer whose age is 30 and whose status is 1
     * $customer = Customer::findOne(['age' => 30, 'status' => 1]);
     *
     * // the above code is equivalent to:
     * $customer = Customer::find()->where(['age' => 30, 'status' => 1])->one();
     * ```
     *
     * If you need to pass user input to this method, make sure the input value is scalar or in case of
     * array condition, make sure the array structure can not be changed from the outside:
     *
     * ```php
     * // yii\web\Controller ensures that $id is scalar
     * public function actionView($id)
     * {
     *     $model = Post::findOne($id);
     *     // ...
     * }
     *
     * // explicitly specifying the colum to search, passing a scalar or array here will always result in finding a single record
     * $model = Post::findOne(['id' => Yii::$app->request->get('id')]);
     *
     * // do NOT use the following code! it is possible to inject an array condition to filter by arbitrary column values!
     * $model = Post::findOne(Yii::$app->request->get('id'));
     * ```
     *
     * @param mixed $condition primary key value or a set of column values
     * @return static ActiveRecord instance matching the condition, or `null` if nothing matches.
     */
    public static function findOne($condition);

    /**
     * Returns a list of active record models that match the specified primary key value(s) or a set of column values.
     *
     * The method accepts:
     *
     *  - a scalar value (integer or string): query by a single primary key value and return an array containing the
     *    corresponding record (or an empty array if not found).
     *  - a non-associative array: query by a list of primary key values and return the
     *    corresponding records (or an empty array if none was found).
     *    Note that an empty condition will result in an empty result as it will be interpreted as a search for
     *    primary keys and not an empty `WHERE` condition.
     *  - an associative array of name-value pairs: query by a set of attribute values and return an array of records
     *    matching all of them (or an empty array if none was found). Note that `['id' => 1, 2]` is treated as
     *    a non-associative array.
     *    Column names are limited to current records table columns for SQL DBMS, or filtered otherwise to be limted to simple filter conditions.
     *
     * This method will automatically call the `all()` method and return an array of [[ActiveRecordInterface|ActiveRecord]]
     * instances.
     *
     * > Note: As this is a short-hand method only, using more complex conditions, like ['!=', 'id', 1] will not work.
     * > If you need to specify more complex conditions, use [[find()]] in combination with [[ActiveQuery::where()|where()]] instead.
     *
     * See the following code for usage examples:
     *
     * ```php
     * // find the customers whose primary key value is 10
     * $customers = Customer::findAll(10);
     *
     * // the above code is equivalent to:
     * $customers = Customer::find()->where(['id' => 10])->all();
     *
     * // find the customers whose primary key value is 10, 11 or 12.
     * $customers = Customer::findAll([10, 11, 12]);
     *
     * // the above code is equivalent to:
     * $customers = Customer::find()->where(['id' => [10, 11, 12]])->all();
     *
     * // find customers whose age is 30 and whose status is 1
     * $customers = Customer::findAll(['age' => 30, 'status' => 1]);
     *
     * // the above code is equivalent to:
     * $customers = Customer::find()->where(['age' => 30, 'status' => 1])->all();
     * ```
     *
     * If you need to pass user input to this method, make sure the input value is scalar or in case of
     * array condition, make sure the array structure can not be changed from the outside:
     *
     * ```php
     * // yii\web\Controller ensures that $id is scalar
     * public function actionView($id)
     * {
     *     $model = Post::findOne($id);
     *     // ...
     * }
     *
     * // explicitly specifying the colum to search, passing a scalar or array here will always result in finding a single record
     * $model = Post::findOne(['id' => Yii::$app->request->get('id')]);
     *
     * // do NOT use the following code! it is possible to inject an array condition to filter by arbitrary column values!
     * $model = Post::findOne(Yii::$app->request->get('id'));
     * ```
     *
     * @param mixed $condition primary key value or a set of column values
     * @return array an array of ActiveRecord instance, or an empty array if nothing matches.
     */
    public static function findAll($condition);

    /**
     * Updates records using the provided attribute values and conditions.
     *
     * For example, to change the status to be 1 for all customers whose status is 2:
     *
     * ```php
     * Customer::updateAll(['status' => 1], ['status' => '2']);
     * ```
     *
     * @param array $attributes attribute values (name-value pairs) to be saved for the record.
     * Unlike [[update()]] these are not going to be validated.
     * @param array $condition the condition that matches the records that should get updated.
     * Please refer to [[QueryInterface::where()]] on how to specify this parameter.
     * An empty condition will match all records.
     * @return int the number of rows updated
     
     * 使用提供的数据来更新特定的活动记录。
     *
     * 例如，将`status`为2的所有顾客的`status`修改为1：
     * ```php
     * Customer::updateAll(['status' => 1], ['status' => '2']);
     * ```
     * 参数：
     *   $attributes | array | 保存到活动记录中的属性值（键值对），此属性不像[[update()]]方法一样需要验证。
     *   $condition  | array | 匹配条件。用于匹配特定的活动记录，进行更新。
     *                         格式请参考[[QueryInterface::where()]]。
     *                         如果筛选条件为空，则会匹配所有活动记录。
     * 结果：
     *   int | 更新的行条数
     */
    public static function updateAll($attributes, $condition = null);

    /**
     * Deletes records using the provided conditions.
     * WARNING: If you do not specify any condition, this method will delete ALL rows in the table.
     *
     * For example, to delete all customers whose status is 3:
     *
     * ```php
     * Customer::deleteAll([status = 3]);
     * ```
     *
     * @param array $condition the condition that matches the records that should get deleted.
     * Please refer to [[QueryInterface::where()]] on how to specify this parameter.
     * An empty condition will match all records.
     * @return int the number of rows deleted
     *
     * 删除指定的活动记录
     * 警告：如果你没有指定匹配条件，此方法将删除数据表中的所有数据。
     *
     * 例如，以下代码将会删除status为3的所有客户信息：
     * ```php
     * Customer::deleteAll([status = 3]);
     * ```
     *
     * 参数：
     *   $condition | array | 匹配条件。用于匹配特定的活动记录，进行删除。
     *                        格式请参考[[QueryInterface::where()]]。
     *                        如果筛选条件为空，则会匹配所有活动记录。
     * 结果：
     *   int | 删除的行条数
     */
    public static function deleteAll($condition = null);

    /**
     * Saves the current record.
     *
     * This method will call [[insert()]] when [[getIsNewRecord()|isNewRecord]] is true, or [[update()]]
     * when [[getIsNewRecord()|isNewRecord]] is false.
     *
     * For example, to save a customer record:
     *
     * ```php
     * $customer = new Customer; // or $customer = Customer::findOne($id);
     * $customer->name = $name;
     * $customer->email = $email;
     * $customer->save();
     * ```
     *
     * @param bool $runValidation whether to perform validation (calling [[\yii\base\Model::validate()|validate()]])
     * before saving the record. Defaults to `true`. If the validation fails, the record
     * will not be saved to the database and this method will return `false`.
     * @param array $attributeNames list of attribute names that need to be saved. Defaults to `null`,
     * meaning all attributes that are loaded from DB will be saved.
     * @return bool whether the saving succeeded (i.e. no validation errors occurred).
     *
     * 保存当前的活动记录
     * 此方法适用于新增活动记录或者修改活动记录。
     * 当[[getIsNewRecord()|isNewRecord]]为true时，会调用[[insert()]]方法；
     * 当[[getIsNewRecord()|isNewRecord]]为false时，会调用[[update()]]方法。
     *
     * 例如，save一个顾客信息活动记录：
     * 
     * ```php
     * $customer = new Customer; // 新增一条记录 
     * // $customer = Customer::findOne($id); // 查找一条旧记录
     * $customer->name = $name;
     * $customer->email = $email;
     * $customer->save(); // 均可调用save()来保存
     * ```
     *
     * 参数：
     *   $runValidation  | bool  | 是否在保存之前进行属性有效性验证（调用[[\yii\base\Model::validate()|validate()]]方法）。
     *                             默认为`true`。如果属性有效性验证失败，则活动记录不会保存到数据库中，此方法也将返回`false`。
     *   $attributeNames | array | 需要保存的属性名。默认为`null`，意味着从数据库中读取的所有属性都将保存。
     * 结果：
     *   bool | 是否保存成功（即没有有效性验证错误）。
     */
    public function save($runValidation = true, $attributeNames = null);

    /**
     * Inserts the record into the database using the attribute values of this record.
     *
     * Usage example:
     *
     * ```php
     * $customer = new Customer;
     * $customer->name = $name;
     * $customer->email = $email;
     * $customer->insert();
     * ```
     *
     * @param bool $runValidation whether to perform validation (calling [[\yii\base\Model::validate()|validate()]])
     * before saving the record. Defaults to `true`. If the validation fails, the record
     * will not be saved to the database and this method will return `false`.
     * @param array $attributes list of attributes that need to be saved. Defaults to `null`,
     * meaning all attributes that are loaded from DB will be saved.
     * @return bool whether the attributes are valid and the record is inserted successfully.
     * 
     * 将此活动记录的数据插入到数据库中。
     * 使用方法：
     * 
     * ```php
     * $customer = new Customer;
     * $customer->name = $name;
     * $customer->email = $email;
     * $customer->insert();
     * ```
     * 参数：
     *   $runValidation  | bool  | 是否在保存之前进行属性有效性验证（调用[[\yii\base\Model::validate()|validate()]]方法）。
     *                             默认为`true`。如果属性有效性验证失败，则活动记录不会保存到数据库中，此方法也将返回`false`。
     *   $attribute      | array | 需要保存的属性。默认为`null`，意味着从数据库中读取的所有属性都将保存。 
     * 结果：
     *   bool | 属性是否有效和成功记录插入。
     */
    public function insert($runValidation = true, $attributes = null);

    /**
     * Saves the changes to this active record into the database.
     *
     * Usage example:
     *
     * ```php
     * $customer = Customer::findOne($id);
     * $customer->name = $name;
     * $customer->email = $email;
     * $customer->update();
     * ```
     *
     * @param bool $runValidation whether to perform validation (calling [[\yii\base\Model::validate()|validate()]])
     * before saving the record. Defaults to `true`. If the validation fails, the record
     * will not be saved to the database and this method will return `false`.
     * @param array $attributeNames list of attributes that need to be saved. Defaults to `null`,
     * meaning all attributes that are loaded from DB will be saved.
     * @return int|bool the number of rows affected, or `false` if validation fails
     * or updating process is stopped for other reasons.
     * Note that it is possible that the number of rows affected is 0, even though the
     * update execution is successful.
     * 
     * 将此活动记录的修改保存到数据库中。
     *
     * 使用方法：
     * ```php
     * $customer = Customer::findOne($id);
     * $customer->name = $name;
     * $customer->email = $email;
     * $customer->update();
     * ```
     * 
     * 参数：
     *   $runValidation  | bool  | 是否在保存之前进行属性有效性验证（调用[[\yii\base\Model::validate()|validate()]]方法）。
     *                             默认为`true`。如果属性有效性验证失败，则活动记录不会保存到数据库中，此方法也将返回`false`。
     *   $attribute      | array | 需要保存的属性。默认为`null`，意味着从数据库中读取的所有属性都将保存。 
     * 结果：
     *   int|bool 受影响的行数，验证失败、更新进程异常停止或者其他异常导致的更新失败，会返回`false`。
     *            需要注意的是，在记录更新操作执行成功的情况下，受影响的行数是有可能为0的。
     *            即无法以结果为0来判断更新操作执行失败。
     */
    public function update($runValidation = true, $attributeNames = null);

    /**
     * Deletes the record from the database.
     *
     * @return int|bool the number of rows deleted, or `false` if the deletion is unsuccessful for some reason.
     * Note that it is possible that the number of rows deleted is 0, even though the deletion execution is successful.
     *
     * 从数据库中删除当前的活动记录数据。
     *
     * 结果：
     *   int|bool 删除的行条数，当由于异常导致删除不成功时，会返回`false`。
     *            需要注意的是，在记录删除操作执行成功的情况下，受影响的行数是有可能为0的。
     *            即无法以结果为0来判断删除操作执行失败。
     */
    public function delete();

    /**
     * Returns a value indicating whether the current record is new (not saved in the database).
     * @return bool whether the record is new and should be inserted when calling [[save()]].
     *
     * 返回一个值，表示当前活动记录是否是全新的数据，即在数据库中没有记录的数据。
     * 结果：
     *   bool | 活动记录是否是全新的数据，在调用[[save()]]方法时，全新则插入，旧数据则更新。
     */
    public function getIsNewRecord();

    /**
     * Returns a value indicating whether the given active record is the same as the current one.
     * Two [[getIsNewRecord()|new]] records are considered to be not equal.
     * @param static $record record to compare to
     * @return bool whether the two active records refer to the same row in the same database table.
     * 
     * 给定的活动记录，是否与当前的活动记录相同。
     * 两个[[getIsNewRecord()|new]]记录，并不对等。
     * 参数：
     *   $record | static | 需要被比较的活动记录
     * 结果：
     *   bool | 两个活动记录是否引用的同一个数据表中的同一行数据。
     */
    public function equals($record);

    /**
     * Returns the relation object with the specified name.
     * A relation is defined by a getter method which returns an object implementing the [[ActiveQueryInterface]]
     * (normally this would be a relational [[ActiveQuery]] object).
     * It can be declared in either the ActiveRecord class itself or one of its behaviors.
     * @param string $name the relation name, e.g. `orders` for a relation defined via `getOrders()` method (case-sensitive).
     * @param bool $throwException whether to throw exception if the relation does not exist.
     * @return ActiveQueryInterface the relational query object
     *
     * 返回指定名称的关联对象。
     * 一个关联对象通过在ActiveRecord类中的getter方法来定义，此方法将会返回一个实现[[ActiveQueryInterface]]接口协议的对象（通常会返回一个关联的
     * [[ActiveQuery]]对象）。
     * 此关联对象可以在ActiveRecord类中声明，也可以在其行为中声明。
     * 参数：
     *   $name           | string | 关联名称，例如关联对象`orders`通过`getOrders()`方法来定义（大小写敏感）。
     *   $throwException | bool   | 如果此关联对象不存在，是否报错。
     * 结果：
     *   ActiveQueryInterface实例 | 关联查询对象
     */
    public function getRelation($name, $throwException = true);

    /**
     * Populates the named relation with the related records.
     * Note that this method does not check if the relation exists or not.
     * @param string $name the relation name, e.g. `orders` for a relation defined via `getOrders()` method (case-sensitive).
     * @param ActiveRecordInterface|array|null $records the related records to be populated into the relation.
     * @since 2.0.8
     *
     * 填充指定的关系相关的记录。
     * 需要注意的是此方法并不会检查指定的关联关系是否存在。
     * 参数：
     *   $name    | string                           | 关联名称，例如关联对象`orders`通过`getOrders()`方法来定义（大小写敏感）。
     *   $records | ActiveRecordInterface|array|null | 填充进关系对象的相关的活动记录。
     * $since 2.0.8
     */
    public function populateRelation($name, $records);

    /**
     * Establishes the relationship between two records.
     *
     * The relationship is established by setting the foreign key value(s) in one record
     * to be the corresponding primary key value(s) in the other record.
     * The record with the foreign key will be saved into database without performing validation.
     *
     * If the relationship involves a junction table, a new row will be inserted into the
     * junction table which contains the primary key values from both records.
     *
     * This method requires that the primary key value is not `null`.
     *
     * @param string $name the case sensitive name of the relationship, e.g. `orders` for a relation defined via `getOrders()` method.
     * @param static $model the record to be linked with the current one.
     * @param array $extraColumns additional column values to be saved into the junction table.
     * This parameter is only meaningful for a relationship involving a junction table
     * (i.e., a relation set with [[ActiveQueryInterface::via()]]).
     *
     * 在两个活动记录之间建立关系。
     * 通过将一个记录中的外键值设置为另一个记录中的相应主键值来建立该关系。
     * 具有外键的记录将保存到数据库中而不执行验证。
     *
     * 如果关系涉及联结表，则会在联结表中插入一个新行，其中包含两个记录中的主键值。
     * 此方法要求主键值不是“null”。
     * 
     * 参数：
     *   $name         | string | 关联名称，例如关联对象`orders`通过`getOrders()`方法来定义（大小写敏感）。
     *   $model        | static | 将与当前活动记录关联的活动记录对象。
     *   $extraColumns | array  | 保存到中间表中的额外列值。这个参数只有在两个预关联对象存在中间表的情况下有意义
     *                            （即一个关联关系通过[[ActiveQueryInterface::via()]]来设置）。
     */
    public function link($name, $model, $extraColumns = []);

    /**
     * Destroys the relationship between two records.
     *
     * The record with the foreign key of the relationship will be deleted if `$delete` is true.
     * Otherwise, the foreign key will be set `null` and the record will be saved without validation.
     *
     * @param string $name the case sensitive name of the relationship, e.g. `orders` for a relation defined via `getOrders()` method.
     * @param static $model the model to be unlinked from the current one.
     * @param bool $delete whether to delete the model that contains the foreign key.
     * If false, the model's foreign key will be set `null` and saved.
     * If true, the model containing the foreign key will be deleted.
     * 
     * 销毁两个活动记录之间的关系。
     * 如果参数`$delete`为`true`，则在关联关系中，拥有外键的一个活动记录将被删除。
     * 否则，外键将会被设置为`null`且活动记录将会在不执行验证的情况下保存。
     * 
     * 参数：
     *   $name   | string | 关联名称，例如关联对象`orders`通过`getOrders()`方法来定义（大小写敏感）。
     *   $model  | static | 将与当前活动记录接触关联的活动记录对象。
     *   $delete | bool   | 是否删除包含外键的模型。
     *                      false: 模型的外键将会被设置为`null`并保存。
     *                      true:  包含外键的模型将会被删除。
     */
    public function unlink($name, $model, $delete = false);

    /**
     * Returns the connection used by this AR class.
     * @return mixed the database connection used by this AR class.
     *
     * 返回此AR类的数据库连接。
     * 结果：
     *   无确定类型 | 此AR类使用的数据库连接。
     */
    public static function getDb();
}
