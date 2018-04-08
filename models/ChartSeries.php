<?php
/**
 * Created by PhpStorm.
 * User: work
 * Date: 08.04.18
 * Time: 10:31
 */

namespace app\models;

use yii\base\Model;

class ChartSeries extends Model
{
    public $name;
    public $data;

    public function rules()
    {
        return [
            [['name', 'data'], 'required'],
            ['name', 'string', 'max' => 50],
            ['data', 'default', 'value' => []],
        ];
    }
}