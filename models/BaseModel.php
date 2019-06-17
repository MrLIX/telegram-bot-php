<?php

namespace app\models;

use trntv\filekit\behaviors\UploadBehavior;
use Yii;
use yii\db\ActiveRecord;

/**
 *
 * @property mixed $name
 * @property mixed $description
 * @property mixed $title
 * @property mixed $content
 */
class BaseModel extends ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    const FILE_UPLOAD_URL = ['/admin/file-storage/upload'];
    const MAX_FILE_UPLOAD_SIZE = 2000000;
    const MAX_UPLOAD_FILE = 16;

    public $base_file;
    public $base_files;


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
     * @return array
     */
    public function rules()
    {
        return [
            [['base_file', 'base_files'], 'safe'],
        ];
    }

    public function getOrderList()
    {
        return [
            '1' => '1',
            '2' => '2',
            '3' => '3',
            '4' => '4',
            '5' => '5',
            '6' => '6',
            '7' => '7',
            '8' => '8',
            '9' => '9',
            '10' => '10',
            '11' => '11',
            '12' => '12',
            '13' => '13',
            '14' => '14',
            '15' => '15',
            '16' => '16',
            '17' => '17',
            '18' => '18',
            '19' => '19',
            '20' => '20',
        ];
    }


}