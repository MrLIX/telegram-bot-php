<?php

namespace app\models;

use trntv\filekit\behaviors\UploadBehavior;
use Yii;

/**
 * This is the model class for table "products".
 *
 * @property int $id
 * @property int $category_id
 * @property string $name_ru
 * @property string $name_uz
 * @property string $description_ru
 * @property string $description_uz
 * @property string $image_description_ru
 * @property string $image_description_uz
 * @property string $image
 * @property int $price
 * @property int $status
 * @property int $order
 *
 * @property Cart[] $carts
 * @property OrderProducts[] $orderProducts
 * @property Categories $category
 */
class Products extends \yii\db\ActiveRecord
{
    public $base_file;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'products';
    }
    /**
     * @return array
     */
    public function behaviors ()
    {
        return [
            [
                'class' => UploadBehavior::className(),
                'attribute' => 'base_file',
                'pathAttribute' => 'image',
                'baseUrlAttribute' => false
            ]
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id', 'price', 'status', 'order'], 'integer'],
            [['name_ru'], 'required'],
            [['description_ru', 'description_uz','image_description_uz','image_description_ru'], 'string'],
            [['name_ru', 'name_uz', 'image'], 'string', 'max' => 255],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Categories::className(), 'targetAttribute' => ['category_id' => 'id']],
            [['base_file', 'base_files'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'category_id' => Yii::t('app', 'Category ID'),
            'category.name_ru' => Yii::t('app', 'Category'),
            'name_ru' => Yii::t('app', 'Name Ru'),
            'name_uz' => Yii::t('app', 'Name Uz'),
            'description_ru' => Yii::t('app', 'Description Ru'),
            'description_uz' => Yii::t('app', 'Description Uz'),
            'image_description_uz' => Yii::t('app', 'Image Description Uz'),
            'image_description_ru' => Yii::t('app', 'Image Description Uz'),
            'image' => Yii::t('app', 'Image'),
            'base_file' => Yii::t('app', 'Image'),
            'price' => Yii::t('app', 'Price'),
            'status' => Yii::t('app', 'Status'),
            'order' => Yii::t('app', 'Order'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCarts()
    {
        return $this->hasMany(Cart::className(), ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderProducts()
    {
        return $this->hasMany(OrderProducts::className(), ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Categories::className(), ['id' => 'category_id']);
    }
}
