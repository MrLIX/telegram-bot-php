<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "settings".
 *
 * @property int $id
 * @property string $bot_token
 * @property string $channel_id
 * @property string $start_text
 * @property string $start_ru_text
 * @property string $start_uz_text
 * @property string $feedback_text_ru
 * @property string $feedback_text_uz
 * @property string $main_text_ru
 * @property string $main_text_uz
 * @property string $categories_ru
 * @property string $categories_uz
 * @property string $from_feedback_to_main_ru
 * @property string $from_feedback_to_main_uz
 * @property string $send_feedback_to_admin
 * @property string $cart_uz
 * @property string $cart_ru
 * @property string $enter_address_ru
 * @property string $enter_address_uz
 * @property string $enter_phone_ru
 * @property string $enter_phone_uz
 * @property string $thanks_to_order_uz
 * @property string $thanks_to_order_ru
 */
class Settings extends \yii\db\ActiveRecord
{

    const SEND = 1;
    const DONTSEND = 0;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'settings';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['start_text', 'start_ru_text', 'start_uz_text','feedback_text_ru','feedback_text_uz','main_text_ru','main_text_uz'], 'string'],
            [['from_feedback_to_main_ru','from_feedback_to_main_uz','categories_ru','categories_uz','cart_uz','cart_ru'],'string'],
            [['enter_phone_ru','enter_phone_uz','enter_address_ru','enter_address_uz','channel_id','confirm_order_ru','confirm_order_uz'],'string'],
            [['thanks_to_order_ru','thanks_to_order_uz'],'string'],
            [['bot_token'], 'string', 'max' => 255],
            [['send_feedback_to_admin'],'integer']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'bot_token' => Yii::t('app', 'Bot Token'),
            'channel_id' => Yii::t('app', 'Order send channel id'),
            'start_text' => Yii::t('app', 'Start Text'),
            'start_ru_text' => Yii::t('app', 'Start Ru Text'),
            'start_uz_text' => Yii::t('app', 'Start Uz Text'),
            'feedback_text_ru' => Yii::t('app', 'Feedback Text Ru'),
            'feedback_text_uz' => Yii::t('app', 'Feedback Text Uz'),
            'main_text_ru' => Yii::t('app', 'Main Text Ru'),
            'main_text_uz' => Yii::t('app', 'Main Text Uz'),
            'categories_uz' => Yii::t('app', 'Categories Uz'),
            'categories_ru' => Yii::t('app', 'Categories Ru'),
            'from_feedback_to_main_ru' => Yii::t('app', 'From Feedback to Main menu Ru'),
            'from_feedback_to_main_uz' => Yii::t('app', 'From Feedback to Main menu Uz'),
            'send_feedback_to_admin' => Yii::t('app', 'Send Feedback to admin?'),
            'cart_ru' => Yii::t('app', 'Text when choose product count Ru'),
            'cart_uz' => Yii::t('app', 'Text when choose product count Uz'),
            'enter_phone_ru' => Yii::t('app', 'Enter phone number Ru'),
            'enter_phone_uz' => Yii::t('app', 'Enter phone number Uz'),
            'enter_address_ru' => Yii::t('app', 'Enter address Ru'),
            'enter_address_uz' => Yii::t('app', 'Enter address Uz'),
            'confirm_order_ru' => Yii::t('app', 'Confirm text Ru'),
            'confirm_order_uz' => Yii::t('app', 'Confirm text Uz'),
            'thanks_to_order_ru' => Yii::t('app', 'After Order Text Ru'),
            'thanks_to_order_uz' => Yii::t('app', 'After Order Text Uz'),
        ];
    }
}
