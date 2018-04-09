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

    private $_account;
    private $_balance = self::DEFAULT_BALANCE;
    private $_stack = [];
    private $_transactions;

    public function __construct(float $account, array $transactions, array $config = [])
    {
        $this->_account = $account;
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
        $this->_balance = $this->_account > 0 ? $this->_account : self::DEFAULT_BALANCE;
    }

    /**
     * @param string           $key
     * @param TransactionModel $model
     */
    private function addBalance(string $key, TransactionModel $model): void
    {
        $this->_stack[$key][] = $this->_balance += $model->profit;
    }
}