<?php
/**
 * Created by PhpStorm.
 * User: work
 * Date: 06.04.18
 * Time: 14:36
 */

namespace app\models;


use yii\base\Model;

class TransactionModel extends Model
{
    public const TYPE_BALANCE = 'balance';
    public const TYPE_BUY = 'buy';

    public $id;
    public $profit;
    public $type;

    public function rules()
    {
        return [
            [['id', 'profit'], 'required'],
            ['id', 'integer'],
            ['profit', 'number'],
            ['type', 'string'],
            ['type', 'trim'],
//            ['type', 'in', 'range' => [self::TYPE_BALANCE, self::TYPE_BUY]],
        ];
    }
}