<?php 
$this->widget('zii.widgets.CListView', array(
	'id'=>'list-auth-items',
    'dataProvider'=>$dataProvider,
	'afterAjaxUpdate'=>'crugeListAuthItemFunctions',
    'itemView'=>'_authitem',
    'sortableAttributes'=>array(
        'name',
    ),
));	
	$url_updater = CHtml::normalizeUrl(array('/cruge/ui/ajaxrbacitemdescr'));
	$loading = Yii::app()->user->ui->getResource('loading.gif');
	$loading = "<img src='{$loading}'>";
?>
<script>
	crugeListAuthItemFunctions = function(){
	$('#list-auth-items .referencias').each(function(){
		$(this).click(function(){
			$(this).parent().find('ul').toggle('slow');
		});
	});
	// actualizador de la descripcion del authitem en base a reglas de 
	// sintaxis.
	$('#list-auth-items select').each(function(){
		$(this).change(function(){
			var action = $(this).val();
			var itemname = $(this).attr('alt');
			if(action != ''){
				// hace la actualizacion via ajax y actualiza la descripcion
				// del item
				var url = '<?php echo $url_updater; ?>';
				var descrSpan = $(this).parent().parent().find('span.description');
				descrSpan.html("<?php echo $loading;?>");
				$.ajax({ url: url, cache: false, dataType: 'json', type: 'post', 
					data: { action: action, itemname: itemname },
					success: function(data){ descrSpan.html(data['description']); },
					error: function(e){ descrSpan.html(
						'error: '+e.responseText); }
				});
			}
		});
	}); }
	crugeListAuthItemFunctions();
</script>
