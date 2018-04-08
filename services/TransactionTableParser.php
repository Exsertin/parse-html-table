<?php
/**
 * Created by PhpStorm.
 * User: work
 * Date: 07.04.18
 * Time: 22:38
 */

namespace app\services;

use app\models\TransactionModel;
use yii\base\BaseObject;
use yii\web\BadRequestHttpException;

/**
 * The class parses the table and returns TransactionModel[]
 */
class TransactionTableParser extends BaseObject implements ServiceInterface
{
    private const INDEX_TYPE = 2;

    /** @var \DOMDocument $_dom */
    private $_dom;

    public function __construct(\DOMDocument $dom, array $config = [])
    {
        $this->_dom = $dom;
        parent::__construct($config);
    }

    /**
     * @return TransactionModel[][]
     * @throws BadRequestHttpException
     */
    public function run(): array
    {
        try {
            $transactions = [];
            $table = $this->_dom->getElementsByTagName('table1')->item(0);
            $type = null;

            /** @var \DOMElement $tr */
            foreach ($table->getElementsByTagName('tr') as $tr) {

                $firstChild = $tr->firstChild;
                $lastChild = $tr->lastChild;
                $cols = $firstChild->getAttribute('colspan');

                if ($cols > 12) {
                    $type = trim($firstChild->nodeValue);
                }

                $model = new TransactionModel();
                $model->id = $firstChild->nodeValue;
                $model->profit = str_replace(' ', '', $lastChild->nodeValue);
                $model->type = $tr->childNodes[self::INDEX_TYPE]->nodeValue ?? null;

                if (!$model->validate() || $type === null) {
                    unset($model);
                    continue;
                }

                $transactions[$type][] = $model;
            }
        } catch (\Throwable $e) {
            throw new BadRequestHttpException('Incorrect file');
        }

        return $transactions;
    }
}