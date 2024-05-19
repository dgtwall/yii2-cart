<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product".
 *
 * @property int $id
 * @property int $shopId
 * @property string $name
 * @property int $activityId
 * @property string|null $createdTime
 * @property string|null $updatedTime
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['id', 'shopId', 'skuId', 'activityId'], 'integer'],
            [['createdTime', 'updatedTime'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'shopId' => '店铺ID',
            'skuId' => '规格ID',
            'name' => '商品名称',
            'activityId' => '活动关联ID',
            'createdTime' => '创建时间',
        ];
    }
}
