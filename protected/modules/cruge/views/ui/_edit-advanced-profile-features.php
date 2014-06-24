<?php 
	/*
		este es un subform, incluido dentro de:	usermanagementupdate.php
		
		se encarga de presentar opciones avanzadas del usuario que solo son realizables
		bajo un ROLE llamado 'admin'
		
		recibe argumentos:
		
		$model: instancia de ICrugeStoredUser, cuyos campos personalizados estan disponibles.
		$form: el formulario mayor
	*/

	Yii::app()->clientScript->registerCoreScript('jquery');
	$editimgurl = Yii::app()->user->ui->getResource("update.png");
	$handimgurl = Yii::app()->user->ui->getResource("hand.png");
	$handoffimgurl = Yii::app()->user->ui->getResource("hand-off.png");
	
	$tit1 = "title='".CrugeTranslator::t("editar relacion")."'";
	$tit2 = "title='".CrugeTranslator::t("asignar")."'";
	$tit3 = "title='".CrugeTranslator::t("revocar")."'";
	$editicon = "<img {$tit1} class='iconhelp edit-icon' src='{$editimgurl}'>";
	$editiconHidden = "<img {$tit1} style='display: none;' class='iconhelp edit-icon' src='{$editimgurl}'>";
	$granticonOn = "<img {$tit2} class='iconhelp grant-icon' src='{$handimgurl}'>";
	$granticonOff = "<img {$tit3} class='iconhelp grant-icon' src='{$handoffimgurl}'>";
?>
<div class='row form-group'>
	<h6><?php echo ucfirst(CrugeTranslator::t("opciones avanzadas"));?></h6>
	<div class='field-group'>
		<div class='col'>
			<?php echo $form->labelEx($model,'state'); ?>
			<?php echo $form->dropDownList($model,'state'
				,Yii::app()->user->um->getUserStateOptions()
				); ?>
			<?php echo $form->error($model,'state'); ?>
		</div>
		
		<?php if($model->state == CRUGEUSERSTATE_NOTACTIVATED){?>
		<div>
			<script>
				function fnSuccess(data){
					$('#resendStatus').html(data);
					setTimeout(function(){ $('#resendStatus').html(""); },3000);
				}
			</script>
			<?php echo CHtml::ajaxbutton(
				ucfirst(CrugeTranslator::t("reenviar correo de activacion"))
				,Yii::app()->user->ui->getAjaxResendRegistrationEmailUrl($model->getPrimaryKey())
				,array('success'=>'js:fnSuccess')
			); ?>
			<p class='hint' id='resendStatus'><?php echo ucfirst(	
				CrugeTranslator::t("esta accion creara una nueva clave."));?></p>
		</div>
		<?php }else{ ?>
			<input type='button' 
				value='<?php echo ucfirst(CrugeTranslator::t("reenviar correo de activacion"))?>'
				disabled='disabled'
			>
		<?php } ?>
	</div>
		
	<?php 
		/*
			funciones de asignacion de roles al usuario
		*/
	?>	
	<div class='field-group'>
	
		<h5><?php echo ucfirst(CrugeTranslator::t("asignacion de roles"));?></h5>
		<p class='hind'>
			<?php echo ucfirst(CrugeTranslator::t("haga click en un rol para asignarlo o removerlo."));?>
		</p>
		
		<ul class='auth-item'>
		<?php 
			$rbac = Yii::app()->user->rbac;
			// consulta la lista de roles asignados al usuario
			$listaRolesAsignados = $rbac->getAuthAssignments($model->getPrimaryKey());
			// lista todos los roles y marca aquellos asignados al usuario
			// los roles que se encuentren en la listaRolesAsignados tendran dos
			// acciones: editar la relacion y asignar/revocar.
			//
			// asignar/revocar:  cuando se haga click en esta imagen entonces el rol sera
			//                   asignado o revocado.
			//
			// editar relacion:  cuando se haga click aqui se editara la relacion, dandole
			//                   un bizRule para que sea evaluada por rbac.checkAccess
			//
			// es importante recordar aca que un rol puede ser asignado o revocado a una persona,
			// por eso el icono siempre esta activo para realizar esta operacion, no asi la
			// edicion de la relacion, que solo debe hacerse si realmente existe la relacion
			// porque esta edicion se hace sobre el registro de CAuthAssignment creado
			// cuando se asigna el rol a este usuario.
			//
			// mas abajo se hara una funcion jQuery que reconocera el click de cada icono
			// para lanzar dos actions via ajax.
			//
			$loader = "<span class='loader'></span>";
			foreach($rbac->roles as $rol){
				
				$checked='';
				//$edit="";
				$edit = $editiconHidden;
				$grant=$granticonOn;
				foreach($listaRolesAsignados as $ra){
					if($ra->itemName === $rol->name)
						{
							// el item esta asignado al usuario
							// se editan estas variables para que el <LI> tenga los iconos
							// respectivos.
							$checked='checked';
							$edit = $editicon;
							$grant=$granticonOff;
							break;
						}
				}	
				echo "<li class='{$checked}' alt='".$rol->name."'>"
					.$rol->name.$grant.$edit.$loader."</li>";
					
				// para efectos de UI solamente, el boton edit debe estar invisible pero existente
				// si el role no esta asignado.
				
				
			}
		?>
		</ul>
		<p class='hint'><?php echo CrugeTranslator::t("notese que a los usuarios se le asignan solo roles, esto es por cuestiones de facilitar la asignacion sobre todo en escenarios de alto volumen de usuarios.");?></p>
	</div>	
</div>

<?php 
	/*
		programacion en jquery para asignar o revocar un rol a un usuario, funciona asi:
		
		se recorre cada LI de la lista UL auth-item, cada LI contiene un atributo ALT
		con el nombre del CAuthItem. Puede que este asignado o puede que no, eso depende
		del atributo "checked" del LI, el cual es establecido mas arriba al LI *solo si* el
		CAuthItem que este LI representa esta asignado al usuario ($model) bajo un CAuthAssignment
		
		entonces, se recorre cada LI, dentro de este LI hay:
		
			1. un icono de clase: edit-icon
				al hacerse click en este icono debe lanzar la edicion del CAuthAssignment
				basicamente para editar solo el BizRule. 
				este icono solo aparece si hay un CAuthAssignment, es decir, solo si 
				el rol esta asignado al usuario.
				
			2. un icono de clase: grant-icon
				este icono siempre esta presente, es para asignar o revocar el rol al usuario.
				al asignar el rol via ajax, el LI tendra marcado el atributo "checked" y por 
				tanto una clase css que lo identifica.
	*/
?>
<script>
	$('ul.auth-item li').each(function(){
		var li = $(this);
		var authitem = li.attr('alt');
		var action = '<?php echo Yii::app()->user->ui->getRbacAjaxAssignmentUrl()?>';
		var actionGetBz = '<?php echo Yii::app()->user->ui->getRbacAjaxGetAssignmentBzUrl()?>';
		var actionSetBz = '<?php echo Yii::app()->user->ui->getRbacAjaxSetAssignmentBzUrl()?>';
		var loadingUrl = '<?php echo Yii::app()->user->ui->getResource('loading.gif'); ?>';
		
		
		li.find('.grant-icon').css("cursor","pointer");
		li.find('.edit-icon').css("cursor","pointer");
		
		li.find('.grant-icon').click(function(){
			var _li = $(this).parent();
			var setFlag = _li.hasClass('checked') ? false : true;
			var grantjsondata = "{ \"authitem\": \""+authitem+"\" , "
			+"\"userid\": \"<?php echo $model->getPrimaryKey();?>\" , \"setflag\": "+setFlag+" }";	
			var loader = _li.find('span.loader');
			
			loader.html("<img src='"+loadingUrl+"'>");
			$('#_errorResult').html("");
			jQuery.ajax({
				url: action,
				type: 'post',
				async: true,
				contentType: "application/json",
				data: grantjsondata,
				success: function(data, textStatus, jqXHR){
					loader.html("");
					// si se pudo realizar la accion, aqui data trae un objeto json con la data del // item
					if(data.result == true){
						_li.addClass("checked");
						li.find('.grant-icon').attr("src","<?php echo $handoffimgurl; ?>");
						li.find('.edit-icon').show();
					}else{
						_li.removeClass("checked");
						li.find('.grant-icon').attr("src","<?php echo $handimgurl; ?>");
						li.find('.edit-icon').hide();
					}
				},
				error: function(jqXHR, textStatus, errorThrown){
					//$('#_errorResult').html("Ocurrio un error:<hr>"+jqXHR.responseText);
					$('#_errorResult').html("<p class='auth-item-error-msg'>no se pudo aplicar</p>");
					$('#_errorResult').show("slow");
					setTimeout(function(){
						$('#_errorResult').hide("slow");
						$('#_errorResult').html("");
					},3000);
					loader.html("");
				},
			});
		});// grant-icon
		
		
		
		li.find('.edit-icon').click(function(){

			var _li = $(this).parent();
			var estaAsignado = _li.hasClass('checked') ? true : false;
			if(estaAsignado == false)
				return;
			
			var bzjsondata = "{ \"authitem\": \""+authitem+"\" , "
			+"\"userid\": \"<?php echo $model->getPrimaryKey();?>\" }";	
			var loader = _li.find('span.loader');
			loader.html("<img src='"+loadingUrl+"'>");
			$('#_errorResult').html("");
			
			
			var _pideBiz = function(data, textStatus, jqXHR){
					loader.html("");
					// ha llegado el businessRule del CAuthAssignment
					var resp = prompt("Business Rule",data.bz);
					if(resp){
					 // begin if resp
					 
						// le envia de vuelta el business rule modificado
						var bzjsondataSave = "{ \"authitem\": \""+authitem+"\" , "
						+"\"userid\": \"<?php echo $model->getPrimaryKey();?>\" , \"bz\": \""+resp+"\" }";	
					 
						var options2 = {
							url: actionSetBz,
							type: 'post',
							async: true,
							contentType: "application/json",
							data: bzjsondataSave,
							success: function(){
								//listo, business rule modificado via ajax
							},
							error: function(jqXHR, textStatus, errorThrown){
								$('#_errorResult').html("<p class='auth-item-error-msg'>no se pudo leer</p>");
								$('#_errorResult').show("slow");
								setTimeout(function(){
									$('#_errorResult').hide("slow");
									$('#_errorResult').html("");
								},3000);
								loader.html("");
							},
						}
						jQuery.ajax(options2);
					}// if resp
			}
			
			var options = {
				url: actionGetBz,
				type: 'post',
				async: true,
				contentType: "application/json",
				data: bzjsondata,
				success: _pideBiz,
				error: function(jqXHR, textStatus, errorThrown){
					$('#_errorResult').html("<p class='auth-item-error-msg'>no se pudo leer</p>");
					$('#_errorResult').show("slow");
					setTimeout(function(){
						$('#_errorResult').hide("slow");
						$('#_errorResult').html("");
					},3000);
					loader.html("");
				},
			}			
			// solicita el business rule de este CAuthAssignment
			jQuery.ajax(options);
			
		});//edit-icon

		
	});
</script>
<div id='_errorResult'></div>
