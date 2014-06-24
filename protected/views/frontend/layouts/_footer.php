<div id="footer">
    <div class="row">
      <div class="span1"></div>
      <div class="span4">
        <?php $this->widget('application.components.frontend.widgets.LanguageSelector', array('type'=>'simple')); ?><br/>
      </div>
      <div class="span7">
        Copyright &copy; <?php echo date('Y'); ?> by My Company.<br/>
        All Rights Reserved.<br/>
        <?php echo Yii::powered(); ?>
      </div>
    </div>
</div><!-- footer -->
