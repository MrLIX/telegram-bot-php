<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "feedback".
 *
 * @property int $id
 * @property string $user_id
 * @property string $message
 * @property int $created_at
 * @property int $updated_at
 */
class Feedback extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'feedback';
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
            [['message'], 'string'],
            [['created_at', 'updated_at'], 'integer'],
            [['user_id'], 'string', 'max' => 255],
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
            'message' => Yii::t('app', 'Message'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    public function saveFeedback($bot, $message, $chatID)
    {
        $text = $message->getText();
        $feedback = self::findOne(['user_id' => $chatID]);;
        if (!$feedback){
            $feedback = new Feedback();
            $feedback->user_id = $chatID;
        }
        $feedback->message = $text;
        $feedback->save(false);

        // Send to admin
        $setting = Settings::findOne(1);
        if($setting->send_feedback_to_admin == Settings::SEND){
            $admin = $this->getAdmin();
            $name = $message->getChat()->getFirstname();
            $username = $message->getChat()->getUsername();
            $answer = "Пользователь " . $name . " оставил отзыв.". "
Отзыв: " . $text . "
Кантакты: @" . $username;
            $bot->sendMessage($admin->user_id, $answer, 'HTML');
        }
    }

    /** Get Admin info [user id]
     * @return object
     */
    public function getAdmin()
    {
        $user = User::findOne(1);
        return $user;
    }

    public function getUser()
    {
        return $this->hasOne(User::className(),['chat_id' => 'user_id']);
    }
}
