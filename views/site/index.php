<?php
use yii\widgets\ActiveForm;
use yii\bootstrap\Html;
?>

<div class="jumbotron">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
<div>
    <?= $form->field($model, 'file')
        ->fileInput(['class' => 'btn btn-primary'])
        ->label('Load html') ?>
</div>
    <?= Html::submitButton('Submit', ['class' => 'btn btn-lg btn-success'])?>

    <?php ActiveForm::end() ?>
</div>


