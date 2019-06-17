<?php

namespace app\models;

use trntv\filekit\behaviors\UploadBehavior;
use Yii;

/**
 * This is the model class for table "categories".
 *
 * @property int $id
 * @property string $name_ru
 * @property string $name_uz
 * @property string $icon
 * @property string $description_ru
 * @property string $description_uz
 * @property string $image_description_uz
 * @property string $image_description_ru
 * @property string $image
 * @property int $status
 * @property int $order
 * @property int $parent_id
 *
 * @property Categories $parent
 * @property Categories[] $categories
 * @property Products[] $products
 */
class Categories extends \yii\db\ActiveRecord
{
    public $base_file;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'categories';
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
            [['name_ru'], 'required'],
            [['description_ru', 'description_uz','image_description_uz','image_description_ru'], 'string'],
            [['status', 'order', 'parent_id'], 'integer'],
            [['name_ru', 'name_uz', 'icon', 'image'], 'string', 'max' => 255],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Categories::className(), 'targetAttribute' => ['parent_id' => 'id']],
            [['base_file'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name_ru' => Yii::t('app', 'Name Ru'),
            'name_uz' => Yii::t('app', 'Name Uz'),
            'icon' => Yii::t('app', 'Icon'),
            'description_ru' => Yii::t('app', 'Description Ru'),
            'description_uz' => Yii::t('app', 'Description Uz'),
            'image_description_uz' => Yii::t('app', 'Image Description Uz'),
            'image_description_ru' => Yii::t('app', 'Image Description Ru'),
            'image' => Yii::t('app', 'Image'),
            'base_file' => Yii::t('app', 'Image'),
            'status' => Yii::t('app', 'Status'),
            'order' => Yii::t('app', 'Order'),
            'parent_id' => Yii::t('app', 'Parent ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Categories::className(), ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Categories::className(), ['parent_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Products::className(), ['category_id' => 'id']);
    }
}
