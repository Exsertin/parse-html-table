<?php

use miloschuman\highcharts\Highcharts;
use miloschuman\highcharts\HighchartsAsset;
HighchartsAsset::register($this)->withScripts(['highstock', 'modules/exporting', 'modules/drilldown']);
/* @var $this yii\web\View */

$this->title = 'Highcharts';

echo Highcharts::widget([
    'options' => [
        'title' => ['text' => 'Balance'],
        'series' => $series
    ]
]);