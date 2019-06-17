<?php

namespace app\controllers;

use app\models\Back;
use app\models\Categories;
use app\models\Feedback;
use app\models\Settings;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\Update;
use yii\helpers\Url;
use TelegramBot\Api\Client;
use TelegramBot\Api\Exception;


class SiteController extends AuxiliaryController
{

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $bot = $this->getBotClient();
        try {
            $_this = $this;
            $bot->command('start', function ($message) use ($bot, $_this) {
                $_this->start($bot, $message);
            });

            // Inline Keyboard
            $bot->on(function (Update $update) use ($bot) {
                $callback = $update->getCallbackQuery();
                $message = $callback->getMessage();
                $chatID = $message->getChat()->getId();
                $data = $callback->getData();

                $product = $this->getKeyboards()->findProductFromInlineKeyboard($data);
                $method = $this->getKeyboards()->findInlineKeyboard($data);
                if ($method) {
                    $this->{$method}($bot, $message, $chatID);
                } elseif ($product && !$method) {
                    $this->addOrDeleteProductFromCart($bot, $message, $chatID, $product);
                } else { // if don't find keyboard, redirect to main
                    $this->mainMenu($bot, $message, $chatID);
                };
                $bot->answerCallbackQuery($callback->getId());

            }, function (Update $update) {
                $callback = $update->getCallbackQuery();
                if (is_null($callback) || !strlen($callback->getData()))
                    return false;
                return true;
            });

            // Reply Keyboard
            $bot->on(function (Update $update) use ($bot) {
                $message = $update->getMessage();
                $mtext = $message->getText();
                $chatID = $message->getChat()->getId();
                $method = $this->getKeyboards()->findKeyboard($message);
                if ($method) {
                    $this->{$method}($bot, $message, $chatID);
                } else {
                    $back = $this->getBack($chatID);
                    if ($back === 'fromOrderToCategories') {
                        $this->sendPhoneNumber($bot, $message, $chatID);
                    } else {   // if don't find keyboard, redirect to main
                        $this->mainMenu($bot, $message, $chatID);
                    }
                }
            }, function (Update $update) {
                $message = $update->getMessage();
                if (is_null($message))
                    return false;
                return true;
            });

            $bot->run();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return 'run';
    }

//  -------------------  METHODS ----------------

    /** /start
     * @param BotApi $bot
     */
    public function start($bot, $message, $chatID = null)
    {
        if (!$this->getUser($message->getChat()->getId())) {
            $this->getNewUser()->saveNewUser($message);
        }
        $answer = "Добро пажаловать <b>" . $message->getChat()->getFirstname() . "</b>
" . $this->getDefaultText()->start_text;
        $bot->sendMessage($message->getChat()->getId(), $answer, 'HTML', null, null, $this->getKeyboards()->getLanguages());
    }

    /** "Назад"
     * @param $bot
     * @param $message
     * @param $chatID
     */
    public function backButton($bot, $message, $chatID)
    {
        $back = $this->getBack($chatID);
        $methods = Back::find()->where(['status' => Back::BACK])->all();
        foreach ($methods as $method) {
            if ($method->name == $back) {
                $this->{$method->methods}($bot, $message, $chatID);
            }
        }
    }

    /** Main menu
     * @param BotApi $bot
     * @param $message
     * @param $chatID
     */
    public function mainMenu($bot, $message, $chatID)
    {
        $this->setBack($chatID, 'start');
        $lang = $this->getLang($chatID);
        $answer = $this->getDefaultText()->{'main_text_' . $lang};
        $keyboard = $lang == 'ru' ? $this->getKeyboards()->getChooseRu() : $this->getKeyboards()->getChooseUz();

        $bot->sendMessage($chatID, $answer, 'HTML', null, null, $keyboard);
    }

    /** "🇷🇺 Русский"
     * @param BotApi $bot
     * @param $chatID
     */
    public function chooseLangRU($bot, $message, $chatID)
    {
        $this->setBack($chatID, 'start', 'ru');
        $answer = $this->getDefaultText()->start_ru_text;

        $bot->sendMessage($chatID, $answer, 'HTML', null, null, $this->getKeyboards()->getChooseRu());
    }

    /** "🇺🇿 O'zbekcha"
     * @param BotApi $bot
     * @param $chatID
     */
    public function chooseLangUz($bot, $message, $chatID)
    {
        $this->setBack($chatID, 'start', 'uz');
        $answer = $this->getDefaultText()->start_uz_text;

        $bot->sendMessage($chatID, $answer, 'HTML', null, null, $this->getKeyboards()->getChooseUz());
    }

    /** Feedback
     * @param BotApi $bot
     * @param $message
     * @param $chatID
     */
    public function feedback($bot, $message, $chatID)
    {
        $lang = $this->getLang($chatID);
        $this->setBack($chatID, 'main');
        $answer = $this->getDefaultText()->{'feedback_text_' . $lang};

        $bot->sendMessage($chatID, $answer, 'HTML', null, null, $this->getKeyboards()->feedback($lang));

    }

    /** Main menu
     * @param BotApi $bot
     * @param $message
     * @param $chatID
     */
    public function fromFeedbackToMain($bot, $message, $chatID)
    {
        $this->setBack($chatID, 'main');
        $lang = $this->getLang($chatID);
        $answer = $this->getDefaultText()->{'from_feedback_to_main_' . $lang};
        $bot->sendMessage($chatID, $answer, 'HTML');
        $feedback = new Feedback();
        $feedback->saveFeedback($bot, $message, $chatID);
        $this->mainMenu($bot, $message, $chatID);
    }

    /** "🛍 Заказать"
     * @param BotApi $bot
     * @param $message
     * @param $chatID
     */
    public function categories($bot, $message, $chatID)
    {
        $lang = $this->getLang($chatID);
        $this->setBack($chatID, 'main');
        $answer = $this->getDefaultText()->{'categories_' . $lang};

        $bot->sendMessage($chatID, $answer, 'HTML', null, null, $this->getKeyboards()->getCategories($lang));
    }

    /**
     * @param $bot
     * @param $message
     * @param $chatID
     */
    public function oneCategory($bot, $message, $chatID)
    {
        $lang = $this->getLang($chatID);
        $this->setBack($chatID, 'categories');
        $category = $this->findCategoryByName($message->getText(), $lang);
        $this->saveLastCategoryId($chatID, $category->id);
        $this->sendCategoriesList($bot, $chatID, $lang, $category);
    }

    /** Parent Category list
     * @param $bot
     * @param $message
     * @param $chatID
     */
    public function parentCategory($bot, $message, $chatID)
    {
        $lang = $this->getLang($chatID);
        $this->setBack($chatID, 'backToCategory');
        $categoryId = $this->getCategoryId($chatID);
        $category = $this->findCategoryByName($message->getText(), $lang, $categoryId);
        $this->saveLastCategoryId($chatID, $category->id);
        $this->sendCategoriesList($bot, $chatID, $lang, $category);
    }

    /** Back to Category from Sub category
     * @param $bot
     * @param $message
     * @param $chatID
     */
    public function backToCategory($bot, $message, $chatID)
    {
        $lang = $this->getLang($chatID);
        $this->setBack($chatID, 'categories');
        $categoryId = $this->getCategoryId($chatID);
        $category = Categories::findOne($categoryId);
        $this->saveLastCategoryId($chatID, $category->id);
        $this->sendCategoriesList($bot, $chatID, $lang, $category);
    }

    /**
     * @param BotApi $bot
     * @param $message
     * @param $chatID
     */
    public function products($bot, $message, $chatID)
    {
        $lang = $this->getLang($chatID);
        $this->setBack($chatID, 'backToCategory');
        $categoryId = $this->getUser($chatID)->getLastCategoryId();
        $product = $this->findProductByName($message->getText(), $lang, $categoryId);

        if (!empty($product->image)) {
            $image = $this->basePhotoUrl . $product->image;
            $description = strip_tags($product->{'image_description_' . $lang});
            $bot->sendPhoto($chatID, $image, $description);
        }
//
        $answer = '<b>' . $product->{'name_' . $lang} . '</b>
' . strip_tags($product->{'description_' . $lang}) . '
' . $this->getCurrency($product->price, $lang);
        $bot->sendMessage($chatID, $answer, 'HTML');
        $answer = $this->getChooseCountText($lang);
        $bot->sendMessage($chatID, $answer, 'HTML', null, null, $this->getKeyboards()->oneProductKeyboard($product, $lang));

    }

    /**
     * @param BotApi $bot
     * @param $message
     * @param $chatID
     * @param $product
     */
    public function addOrDeleteProductFromCart($bot, $message, $chatID, $product)
    {
        $lang = $this->getLang($chatID);
        $this->setBack($chatID, 'backToCategory');

        if (!empty($product['deletedId'])) {
            $this->deleteProductFromDate($chatID, $product['deletedId']);
            $this->cart($bot, $message, $chatID);
        } else {
            $cart = $this->saveToCart($chatID, $product);
            if ($cart) {
                $answer = $this->getDefaultText()->{'cart_' . $lang};
                $bot->sendMessage($chatID, $answer, 'HTML');
            }
            $this->categories($bot, $message, $chatID);
        }


    }

    /**
     * @param BotApi $bot
     * @param $message
     * @param $chatID
     */
    public function cart($bot, $message, $chatID)
    {
        $lang = $this->getLang($chatID);
        $this->setBack($chatID, 'categories');
        $allCart = $this->getCartArray($chatID);
        if ($allCart) {
            $answer = $this->getCartText($allCart, $lang);
            $bot->sendMessage($chatID, $answer, 'HTML', null, null, $this->getKeyboards()->getCartKeyboard($allCart, $lang));
        } else {
            $answer = $lang == 'ru' ? 'Ваша корзина пуста' : 'Savatcha mahsulot qo\'shilmagan';
            $bot->sendMessage($chatID, $answer);
            $this->categories($bot, $message, $chatID);
        }
    }

    /** Clear all products from cart
     * @param $bot
     * @param $message
     * @param $chatID
     */
    public function clearCart($bot, $message, $chatID)
    {
        $this->deleteAllCart($chatID);
        $this->categories($bot, $message, $chatID);
    }

    /**
     * @param BotApi $bot
     * @param $message
     * @param $chatID
     */
    public function orders($bot, $message, $chatID)
    {
        $allCart = $this->getCartArray($chatID);
        $lang = $this->getLang($chatID);
        if ($allCart) {
            $this->setBack($chatID, 'fromOrderToCategories');
            $answer = $this->getDefaultText()->{'enter_address_' . $lang};
            $bot->sendMessage($chatID, $answer, 'HTML', null, null, $this->getKeyboards()->sendLocation($lang));
        } else {
            $answer = $lang == 'ru' ? 'Ваша корзина пуста' : 'Savatchaga mahsulot qo\'shilmagan';
            $bot->sendMessage($chatID, $answer);
            $this->categories($bot, $message, $chatID);
        }
    }

    /**
     * @param BotApi $bot
     * @param $message
     * @param $chatID
     */
    public function sendPhoneNumber($bot, $message, $chatID)
    {
        $this->saveUserLocation($chatID,$message->getLocation(),$message->getText());
        $lang = $this->getLang($chatID);
        $this->setBack($chatID, 'categories');
        $answer = $this->getDefaultText()->{'enter_phone_' . $lang};
        $bot->sendMessage($chatID, $answer, 'HTML',null, null, $this->getKeyboards()->sendPhoneNumber($lang));
    }

    /**
     * @param BotApi $bot
     * @param $message
     * @param $chatID
     */
    public function saveOrder($bot, $message, $chatID)
    {
        $this->saveUserPhone($chatID,$message->getContact());
        $lang = $this->getLang($chatID);
        $this->setBack($chatID, 'categories');
        $answer = $this->getDefaultText()->{'confirm_order_' . $lang};
        $bot->sendMessage($chatID, $answer, 'HTML',null, null, $this->getKeyboards()->confirmOrder($lang));
    }

    /**
     * @param BotApi $bot
     * @param $message
     * @param $chatID
     */
    public function confirmOrder($bot, $message, $chatID)
    {
        $lang = $this->getLang($chatID);
        $this->setBack($chatID, 'categories');
        $order = $this->saveOrders($chatID);
        if($order){
            $settings = Settings::findOne(1);
            $answer = $this->getDefaultText()->{'thanks_to_order_' .$lang};
            $bot->sendMessage($chatID, $answer);
            if($settings->channel_id){
                $answer = $this->sendOrderToChannelText($order, $chatID);
                $bot->sendMessage($settings->channel_id, $answer,'HTML',null,null);
                if (!empty($order->lat) && !empty($order->lng))
                    $bot->sendLocation($settings->channel_id, $order->lat, $order->lng);

            }
        }
        $this->categories($bot, $message, $chatID);
    }


//  -------------------  END METHODS ----------------

    /*
    * Set webhook
    */
    private function actionSetWebhook()
    {
        $tmpUrl = Url::to(['/'], true);
        die();
    }

    /**
     * @return mixed
     */
    private function getBotClient()
    {
        $settings = $this->getDefaultText();
        if ($settings) {
            $bot = new Client($settings->bot_token, null);
            return $bot;
        }
        return null;
    }


}
