<?php echo $form->textFieldRow($model,'name'); ?>
<?php echo $form->dropDownListRow($model,'parentId',$model->getParentOptionTree()); ?>
<?php echo $form->dropDownListRow($model,'type',$model->getTypeOptions()); ?>
<?php echo $form->checkBoxRow($model,'published') ?>
