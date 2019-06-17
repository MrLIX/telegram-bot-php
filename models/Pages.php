<?php

namespace app\models;

use trntv\filekit\behaviors\UploadBehavior;
use Yii;

/**
 * This is the model class for table "pages".
 *
 * @property int $id
 * @property string $name_ru
 * @property string $name_uz
 * @property string $icon
 * @property string $descriptions_ru
 * @property string $description_uz
 * @property string $image
 * @property string $url
 */
class Pages extends \yii\db\ActiveRecord
{
    public $base_file;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pages';
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
            [['descriptions_ru', 'description_uz'], 'string'],
            [['name_ru', 'name_uz', 'icon', 'image', 'url'], 'string', 'max' => 255],
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
            'name_ru' => Yii::t('app', 'Name Ru'),
            'name_uz' => Yii::t('app', 'Name Uz'),
            'icon' => Yii::t('app', 'Icon'),
            'descriptions_ru' => Yii::t('app', 'Descriptions Ru'),
            'description_uz' => Yii::t('app', 'Description Uz'),
            'image' => Yii::t('app', 'Image'),
            'base_file' => Yii::t('app', 'Image'),
            'url' => Yii::t('app', 'Url'),
        ];
    }
}
