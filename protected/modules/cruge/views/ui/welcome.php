<?php
// llamada cuando el actionRegistration ha insertado a un usuario
?>
<div class='form'>
    <h1><?php echo CrugeTranslator::t("Bienvenido");?></h1>

    <p><b><?php echo CrugeTranslator::t('registration', 'The account has been created!'); ?></b></p>
    <p><?php echo CrugeTranslator::t('registration', 'Click here to login using new credentials:'); ?>
        <?php echo Yii::app()->user->ui->loginLink; ?>
    </p>
</div>