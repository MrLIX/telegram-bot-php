<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "orders".
 *
 * @property int $id
 * @property string $user_id
 * @property string $phone
 * @property string $lng
 * @property string $lat
 * @property string $address
 * @property string $delivery_type
 * @property int $amount
 * @property int $delivery_date
 * @property int $created_at
 * @property int $updated_at
 *
 * @property OrderProducts[] $orderProducts
 */
class Orders extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orders';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className()
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['amount', 'delivery_date', 'created_at', 'updated_at'], 'integer'],
            [['user_id', 'phone', 'address', 'delivery_type','lat','lng'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'phone' => Yii::t('app', 'Phone'),
            'address' => Yii::t('app', 'Address'),
            'lat' => Yii::t('app', 'Latitude'),
            'lng' => Yii::t('app', 'Longitude'),
            'delivery_type' => Yii::t('app', 'Delivery Type'),
            'amount' => Yii::t('app', 'Amount'),
            'delivery_date' => Yii::t('app', 'Delivery Date'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderProducts()
    {
        return $this->hasMany(OrderProducts::className(), ['order_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(),['chat_id' => 'user_id']);
    }
}
