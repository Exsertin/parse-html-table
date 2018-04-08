<?php
/**
 * Created by PhpStorm.
 * User: work
 * Date: 08.04.18
 * Time: 9:11
 */

namespace app\services;

use app\models\TransactionModel;
use yii\base\BaseObject;

/**
 * BalanceStackCounter returns stack of balance with counting his change
 */
final class BalanceStackCounter extends BaseObject implements ServiceInterface
{
    private const DEFAULT_BALANCE = 0.0;

    private $_balance = self::DEFAULT_BALANCE;
    private $_stack = [];
    private $_transactions;

    public function __construct(array $transactions, array $config = [])
    {
        $this->_transactions = $transactions;
        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function run(): array
    {
        foreach ($this->_transactions as $key => $models) {

            $this->resetBalance();

            foreach ($models as $model) {
                $this->addBalance($key, $model);
            }
        }

        return $this->_stack;
    }

    private function resetBalance(): void
    {
        $this->_balance = self::DEFAULT_BALANCE;
    }

    /**
     * @param string           $key
     * @param TransactionModel $model
     */
    private function addBalance(string $key, TransactionModel $model): void
    {
        $balance = $this->_balance;

        if ($model->type == TransactionModel::TYPE_BALANCE) {
            $balance = $model->profit;
        } else {
            $balance += $model->profit;
        }

        $this->_stack[$key][] = $this->_balance = $balance;
    }
}