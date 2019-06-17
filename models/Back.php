<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "back".
 *
 * @property int $id
 * @property string $name
 * @property string $methods
 * @property int $status
 */
class Back extends \yii\db\ActiveRecord
{

    const MENU = 1;
    const BACK = 0;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'back';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['name', 'methods'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'methods' => Yii::t('app', 'Methods'),
            'status' => Yii::t('app', 'Status'),
        ];
    }
}
