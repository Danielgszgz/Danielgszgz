<?php
  	ini_set( 'display_errors', 1 );
  	error_reporting( E_ERROR );
	//$tiempo_inicial = microtime(true);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/custom.css">

    <link href="https://fonts.googleapis.com/css?family=Exo:400,700,900" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <title>Beca TALENTic</title>
</head>
<?php
	
	$date = date("Y-m-d");
	$quince = date("Y-m-d",strtotime($date."- 15 days"));
	
	// LLAMAMOS AL ARCHIVO DE CONEXIÓN Y FUNCIONES
	require 'classMatch.php';

	$mysqli = conexion();

	//Llamamos a MatchVisto.php
	//require_once 'matchVisto.php';

	//DEPENDIENDO DE LA VARIABLE QUE SE LE PASE POR GET, LA GUARDAMOS EN UNA VARIABLE NUEVA
	if(isset($_GET["empresa"])){ $laempresa = $_GET["empresa"]; }

	if(isset($_GET["negociacion"])){ $lanegociacion = $_GET["negociacion"]; }

  	$nombreNegociacion = "";
  	$estadoCont = "";
  	$provincia = "";


  	// SI SE HA PASADO EL PARAMETRO NEGOCIACION
	if (isset($lanegociacion)){
		//OBTENEMOS LOS DATOS DE LA NEGOCIACIÓN
		$negociacion = negociacion($lanegociacion);

		$idempresa = $negociacion['account_name'];
		$provincia = $negociacion['provincia'];
		$perf = str_replace("[","", $negociacion['perfil']);$perf = str_replace("]","", $perf);$perf = str_replace('"','', $perf);
		$perfiles = explode(",",$perf);
		$tipoNeg = $negociacion['type'];
		$quiereRemotos = $negociacion['quiere_remotos'];

		//OBTENEMOS LOS DATOS DE SU EMPRESA
		$empresa = empresa($idempresa);
		$nombreEmpresa = $empresa['account_name'];

		$contactos = null;

    	//OBTENEMOS EL NOMBRE DE LA NEGOCIACION
    	$nombreNegociacion =  $negociacion['deal_name'];

    	//COMPROBAMOS SI LA NEGOCIACION ES DE BECA O DE BOLSA DE EMPLEO
    	if($tipoNeg != "Bolsa de trabajo"){ $estadoCont = "Pendiente de beca"; }else{ $estadoCont = "Bolsa de trabajo"; }

    	//OBTENEMOS LOS ALUMNOS QUE CUMPLAN "PROVINCIA, ESTADO DE NEGOCIACION Y PERFILES"
    	if($quiereRemotos == 1){
    		$contactos = alumnoCondicion4($provincia,$estadoCont,$perfiles,$quiereRemotos);
    	}else{
    		$contactos = alumnoCondicion4($provincia,$estadoCont,$perfiles);
    	}

	}else if(isset($laempresa)){
		//OBTENEMOS LOS DATOS DE LA EMPRESA
		$empresa = empresa($laempresa);
		$empresaID = $empresa['idEmpresa'];
		$nombreEmpresa = $empresa['account_name'];
		$provincia = $empresa['provinciaEmpresa'];
		$perfiles = explode("-",$empresa['perfilBuscado']);

		//COMO NO EXISTE NEGOCIACION, GUARDAMOS EL NOMBRE VACIO
  		$nombreNegociacion = "Sin negociacion";

  		//OBTENEMOS LOS ALUMNOS QUE CUMPLAN "PROVINCIA, ESTADO DE NEGOCIACION Y PERFILES"
		$contactos = alumnoCondicion($provincia,"Pendiente de Beca",$perfiles);

	}else{
		//EN CASO DE QUE NO SE PASE NINGUN PARAMETRO SE MOSTRARA EL SIGUIENTE MENSAJE
		echo "No se ha pasado ningun parametro.";
	}

	//OBTENEMOS LA ORIENTADORA DE LA EMPRESA Y SUS DATOS
	$orientadora = orientadora($empresa['responsable_de_orientaci_n_2']);

	//GUARDAMOS EN UNA VARIABLE EL ID DE LA EMPRESA
	if (isset($lanegociacion)){ $empresaID = $idempresa; }else{ $empresaID = $laempresa; }

?>

<body>
	<header id="header">
       	<div class="container d-flex justify-content-center">
        	<img src="../imgs/nett_logo.svg">
       	</div>
	</header>
	<section id="empresa">
		<div class="container">
			<div class="row">
				<div class="col-md-6">
					<div class="row">
	                 	<div class="col-auto d-flex align-items-center">
	                 		<!-- MOSTRAMOS LOS DATOS DE LA EMPRESA CON HTML -->
		                   	<div class="datos-empresa">
		                     	<h1><?php echo $empresa['account_name'];?></h1>
		                     	<h3><i class="material-icons">place</i> <?php echo $provincia;?></h3>
		                     	<h5>
<?php
									// MOSTRAMOS LOS PERFILES QUE PIDE LA EMPRESA
			                		foreach ($perfiles as $perfil) {
			                			if($perfil != ""){ echo str_replace("\\","",$perfil)." | "; }
			                		}
?>
								</h5>
			                </div>
	            		</div>
	        		</div>
	    		</div>
	    		<div class="col-md-6">
	        		<div class="row">
		                <div class="col-auto d-flex align-items-center">
		                   	<div class="logo-empresa">
		                   		<!-- MOSTRAMOS UNA FOTO DE LA ORIENTADORA -->
		                    	<img style="border-radius: 150px;" src="../imgs/<?php echo $orientadora['fotoResponsable'];?>">
		                   	</div>
		                </div>
		                <div class="col-auto d-flex align-items-center">
		                   	<div class="datos-empresa">
		                   		<!-- MOSTRAMOS LOS DATOS DE LA ORIENTADORA, TELEFONO, MAIL, ... -->
		                     	<h1>Contacta con tu orientador/a</h1>
		                     	<h3><i class="material-icons">explore</i><?php echo $orientadora['nombreResponsable'];?></h3>
		                     	<h4><a class="mailto" href="mailto:<?php echo $orientadora['mailResponsable'];?>"><?php echo $orientadora['mailResponsable'];?></a> | <a class="tel" href="tel:<?php echo $orientadora['telefonoResponsable']?>"><?php echo $orientadora['telefonoResponsable'];?></a></h4>
		                   	</div>
		                </div>
		            </div>
		        </div>
	        </div>
	    </div>
	</section>
	     <section id="listado-alumnos">
	       <div class="container">
	         <div class="row">
	           <div class="col-12 d-flex justify-content-center">
<?php
				// PONEMOS UN TITULO DIFERENTE DEPENDIENDO DE SI ES UNA NEGOCIACION DE BECA O BOLSA DE EMPLEO
				if ($estadoCont != "Bolsa de trabajo"){
	            	echo "<h3>Candidatos Beca TALENTic recomendados</h3>";
	            }else{
	            	echo "<h3>Exalumnos de NETT en busqueda de trabajo</h3>";
	            }
?>
	           </div>
	           <div class="col-12">
	           <ul>
<?php
			// EN EL CASO DE QUE NO HAYA NINGUN CANDIDATO MOSTRAMOS EL SIGUIENTE MENSAJE
			if(mysqli_num_rows($contactos) == 0){
?>
				<li class="new">
					<div class="alumno">
						<h2 style="font-size: 20px;">Actualmente no hay ning&uacuten candidato en <?php echo $provincia;?> con el perfil que has demandado, iniciamos la búsqueda del alumno con el perfil que necesitas y te avisaremos cuando lo encontremos, no obstante, puedes ponerte en contacto con tu orientadora si lo prefieres para redefinir ese perfil.</h3>
					</div>
				</li>
<?php
			}else{
				$nombreContactN = "";
				while($contacto = $contactos->fetch_array()){
					// COMPROBAMOS SI EL CANDIDATO ESTA PENDIENTE DE BECA DESDE HACE MENOS DE 15 DÍAS
					// EN CASO AFIRMATIVO, EN LA FOTO PONDRA EL MENSAJE "NEW"
		            if($contacto['pendiente_de_beca_desde'] > $quince){
		                $elnuevo = '<div class="nuevo-label pulse">NEW</div>';
		            }else{
		            	$elnuevo = '';
		            	
		            }

		            $nombreNegociacionN = "";
		            //COMPROBAMOS SI EXISTE EL PROCESO
		            $idAlumno = $contacto['id'];

		            // GUARDAMOS EN UNA VARIABLE EL NOMBRE COMPLETO DEL CANDIDATO
				  	$nombreContactN = $contacto['first_name']." ".$contacto['last_name'];

		            if (isset($lanegociacion)){

		            	$idEmpresa = $idempresa;
		            	//OBTENEMOS EL NOMBRE DE LA NEGOCIACION
		            	$negociacion = negociacion($lanegociacion);
						$nombreNegociacionN = $negociacion['deal_name'];
						$typeNegociacion = $negociacion['type'];

		            }else{
		            	$idEmpresa = $empresaID;

		       			//OBTENEMOS EL NOMBRE DE LA NEGOCIACION, SI CUMPLE UNOS REQUISITOS (COMO QUE LA NEGOCIACION ESE ABIERTA)
		       			$negociacion = negociacionCondiciones2($idEmpresa);
		       			$nombreNegociacionN = $negociacion['deal_name'];
		            }

		            unset($proc2);
		            // OBTENEMOS LOS PROCESOS DE LA NEGOCIACION
		            $sqlProc2 = "SELECT id FROM zoho_cases WHERE alumno = ".$contacto['id']." AND negociaci_n = ".$negociacion['id'];
    				$proc2 = $mysqli->query($sqlProc2);
    				
    				while($resultProc2 = $proc2->fetch_array()){
    					// EN CASO DE QUE EXISTA UN PROCESO DE ESA NEGOCIACION Y ESE CANDIDATO, SE MOSTRARA AL LADO DE LA FOTO DEL CANDIDATO "YA VISTO"
    					if(isset($resultProc2)){
		    				$elnuevo = '<div class="nuevo-label" style="color:#EA4553;background-color:#0000001f;">Ya visto</div>';
		    			}
    				}

		            //CONTAMOS LOS PROCESOS DEL ALUMNO
		            // $procesos = procesosEntrevista($idAlumno);

		            // $procesosAlumno = 0;

		            // if(mysqli_num_rows($procesos) > 0){
		            // 	$procesosAlumno = mysqli_num_rows($procesos);
		            // }else{
		            // 	$procesosAlumno = 0;
		            // }

		            // OBTENEMOS LA RUTA DONDE SE HA GUARDADO EL CURRICULUM EN PDF DEL CANDIDATO    

					$elcv = str_replace(' ', '_', $nombreContactN).".pdf";
		            $elcv= $_SERVER['DOCUMENT_ROOT']."/cm-zoho/cvs/".$elcv;
					if(file_exists($elcv)){
						$elcvurl= "https://crm.nettformacion.com/cm-zoho/cvs/".str_replace(' ', '_', $nombreContactN).".pdf";
					}else{
						$elcv = str_replace(' ', '', $nombreContactN).".pdf";
		           		$elcv= $_SERVER['DOCUMENT_ROOT']."/cm-zoho/cvs/".$elcv;
		            	$elcvurl= "https://crm.nettformacion.com/cm-zoho/cvs/".str_replace(' ', '', $nombreContactN).".pdf";
					}
					
      				// $idcontacto = $contacto->getEntityId();
?>
					<!-- MOSTRAMOS UN CUADRO CON LOS DATOS DE CADA CANDIDATO -->
					<li class="new">
						<div class="alumno">
							<?php echo $elnuevo;?>
							<div class="row">
								<div class="col-md-2">
									<div class="avatar">
<?php
										$NombreCompleto = str_replace(" ", "_",$contacto['first_name']."_".$contacto['last_name']);
					                    $ruta_imagen = "../imgs/contacts/".$NombreCompleto.".png";
					                    // AQUI MOSTRAMOS UNA FOTO DEL CANDIDATO.
					                   	// EN CASO DE QUE SEA NUEVO O YA ESTE VISTO, SE INDICARA
					                   	// SI NO HAYA FOTO, SE MOSTRARA UNA IMAGEN POR DEFECTO
					                    if(file_exists($ruta_imagen)){
?>
											<img style="border-radius: 150px;" src="<?php echo $ruta_imagen;?>">
<?php
										}else{
											$NombreCompleto = str_replace(" ", "",$contacto['first_name']."".$contacto['last_name']);
											$ruta_imagen = "../imgs/contacts/".$NombreCompleto.".png";
											if(file_exists($ruta_imagen)){
?>
											<img style="border-radius: 150px;" src="<?php echo $ruta_imagen;?>">
<?php
											}else{
?>
												<img src="../imgs/avatar-m.svg">
<?php
											}
										}
?>
									</div>
			                   	</div>
			                   	<div class="col-md-4">
			                    	<div class="datos">
			                    		<!-- IMPRIMIMOS EL NOMBRE COMPLETO DEL CONTACTO -->
			                    		<div class="name"><u><?php echo $nombreContactN;?></u></div>
<?php
										// IMPRIMIMOS DESDE CUANDO SE ENCUENTRA DISPONIBLE EL CONTACTO
										if($contacto['disponible_desde'] > date("Y-m-d")){
?>
											<div class="procesos"><?php echo "Disponible desde ".date("d/m/Y", strtotime($contacto['disponible_desde']));?></div>
<?php
                        				}

                        				// IMPRIMIMOS CUANTO TIEMPO LE QUEDA DE BECA (EN CASO DE QUE SEA RECOLOCADO, TENDRA MENOS TIEMPO)
                        				if ($estadoCont != "Bolsa de trabajo"){
											if($contacto['tiempo_restante_beca'] != ""){
?>
												<div class="procesos">Beca de <?php echo $contacto['tiempo_restante_beca'];?></div>
<?php
											}
										}
										
										// SI TIENE DISPONIBILIDAD PARA PRACTICAS
										if($contacto['disponibilidad_para_pr_cticas']!= null OR $contacto['disponibilidad_para_pr_cticas']!= ""){
?>
											<div class="procesos">Disponible para prácticas: <?php echo $contacto['disponibilidad_para_pr_cticas'];?></div>
<?php
										}

										// SI TIENE DISPONIBILIDAD DE COCHE
										if($contacto['coche']== 1){
?>
											<div class="procesos">Dispone de coche <img style="width:20px;margin-top:-10px;" src="../imgs/coche.png"></div>
<?php
										}

										// UBICACIÓN PRÁCTICAS
										if($contacto['ubicaci_n_pr_cticas']!= ""){
?>
											<div class="procesos"><strong>Ubicación prácticas:</strong> <?php echo $contacto['ubicaci_n_pr_cticas'];?></div>
<?php
										}
?>
									</div>
			                    </div>
			                    <div class="col-md-3">
			                       	<div class="perfil">
<?php
										if($contacto['m_curso1'] != ""){
											if($contacto['m_curso1'] == "MÁSTER EN DESARROLLO WEB AVANZADO"){
?>
												<div class="procesos"><strong><?php echo $contacto['m_curso1']." (6 meses)";?></strong></div>
<?php
											}else{
?>
												<div class="procesos"><strong><?php echo $contacto['m_curso1'];?></strong></div>
<?php
											}		
										}
										// MOSTRAMOS LOS PERFILES DEL CANDIDATO A LA DERECHA DE LA FOTO
										$perfilContacto = $contacto['perfil_de_alumno'];
										$perfilContacto = str_replace("[","",$perfilContacto);$perfilContacto = str_replace("]","",$perfilContacto);$perfilContacto = str_replace('"','',$perfilContacto);$perfilContacto = str_replace('\\','',$perfilContacto);
									    echo str_replace(",", " | ", $perfilContacto);
?>
									</div>
			                   	</div>
			                   	<div class="col-md-3 justify-content-center">
			                    	<div class="buttons">
<?php
								// MOSTRAMOS EL BOTÓN DE VER CURRICULUM
								// EN CASO DE QUE NO SE PUEDA OBTENER EL CURRICULUM, SALDRA UN BOTÓN CON EL TEXTO "ESPERANDO CURRICULUM"
								if(file_exists($elcv)){
?>
				                    <form method="POST" action="verCurriculum.php" target="_blank">
				                       <input type="hidden" name="elcvurl" value="<?php echo $elcvurl;?>">
				                       <input type="hidden" name="orientadora" value="<?php echo $orientadora['nombreResponsable'];?>">
				                       <input type="hidden" name="idEmpresa" value="<?php echo $empresaID;?>">
				                       <input type="hidden" name="negociacion" value="<?php echo $nombreNegociacionN;?>">
				                       <input type="hidden" name="idNegociacion" value="<?php echo $lanegociacion;?>">
				                       <input type="hidden" name="idAlumno" value="<?php echo $idAlumno;?>">
				                       <input type="hidden" name="typeNegociacion" value="<?php echo $typeNegociacion;?>">
				                       <button class="btn btn-primary">Ver Curriculum</button>
				                    </form>
<?php
								}else{
?>
									<a href="#" target="_blank" class="btn btn-primary disabled">Esperando curriculum</a>
<?php
								}
								$nombreContacto = str_replace(" ", "_", $contacto['first_name']." ".$contacto['last_name']);
								//EL BOTÓN PEDIR ENTREVISTA SOLO APARECERA SI NO ES NEGOCIACIÓN DE BOLSA DE TRABAJO
								if ($estadoCont != "Bolsa de trabajo"){
?>
									<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#Modal<?php echo $nombreContacto;?>">Pedir entrevista</button>
									<!-- Contesta a este mismo correo para pedir una entrevista -->
<?php
								}
?>
									</div>
			                   </div>
			                 </div>
			               </div>
			             </li>
<?php
       			}
       		}
?>
				</ul>
           </div>
         </div>
       </div>
     </section>
<?php
	if ($estadoCont != "Bolsa de trabajo"){
?>
	<!-- DATOS E INFORMACIÓN DEL PIE -->
     <section id="puntos">
       <div class="container">
       	<div class="row">
           <div class="col-12">
             <div class="rec-inn ">
             <div class="d-flex justify-content-center">
               <h3>&iquestQu&eacute debes saber?</h3>
             </div>
             <div class="checks-list">
               <ul>
                 <li class="d-flex justify-content-center">
                    <div class="punto">
                        <i class="material-icons">info</i> Recuerda que es un becario y puede que NO tenga experiencia previa. </br>Deber&aacutes destinar un tutor especializado dentro de la empresa para la tutorizaci&oacuten.
                    </div>
                 </li>
               </ul>
             </div>
           </div>
           </div>
         </div>
       </div>
     </section>
     <section id="recordatorio">
       <div class="container">
         <div class="row">
           <div class="col-12">
           	<div class="rec-inn ">
             <div class="d-flex justify-content-center">
               <h3>&iquestEn que consiste la Beca TALENTic para empresas?</h3>
             </div>
             <div class="row">
               <div class="col-lg-4">
                 <div class="dato">
                     <i class="material-icons">done</i>
                     <p><strong>Máster en Marketing, Publicidad y Diseño</strong> y <strong>Máster en Transformación Digital</strong>, con una duración de 12 meses con Beca TALENTic, 489&euro;/mes
                     </p>
                 </div>
               </div>
               <div class="col-lg-4">
                  <div class="dato">
                     <i class="material-icons">done</i>
                     <p><strong>Máster en Desarrollo Web Avanzado</strong>, con una duración de 6 meses con Beca TALENTic, 580&euro;/mes</p>
                 </div>
               </div>
               <div class="col-lg-4">
                   <div class="dato">
                     <i class="material-icons">done</i>
                     <p>Elige <strong>el perfil que necesita</strong> tu empresa.<strong> Solicita candidatos</strong> de la Beca TALENTic, una orientadora te explicar&aacute y guiar&aacute durante el <strong>proceso de selecci&oacuten.</strong></p>
                 </div>
               </div>
             </div>
             <div class="text-center">
                 <a href="https://trajeamedidaformacion.es/beca-talentic/"  target="_blank" class="btn btn-primary">Mas informaci&oacuten</a>
             </div>
           </div>
           </div>
         </div>
       </div>
     </section>
<?php
	}
?>
     <footer>
     	<div class="text-center"><a href="https://nettdigitalschool.com/aviso-legal-y-cookies/">Aviso legal y política de cookies</a> - <a href="https://nettdigitalschool.com/politica-de-privacidad/">Política de privacidad</a></div>
       <div class="text-center">&copy;Nett Digital School <?php echo date('Y');?></div>
     </footer>

<?php
	// /*MODALES*/ ESTA SECCIÓN SIRVE PARA VER EL POPUP DE PEDIR ENTREVISTA
	$contactos = alumnoCondicion4($provincia,$estadoCont,$perfiles);
   	while($contacto = $contactos->fetch_array()){
   		if(isset($contacto)){
    		$nombreContacto = str_replace(" ", "_", $contacto['first_name']." ".$contacto['last_name']);
?>
			<div class="modal fade" id="Modal<?php echo $nombreContacto;?>" tabindex="-1" role="dialog" aria-labelledby="ModalLabel<?php echo $nombreContacto;?>" aria-hidden="true">
	 	      <div class="modal-dialog" role="document">
	 	        <div class="modal-content">
	 	          <div class="modal-header">
	 	            <h5 class="modal-title" id="exampleModalLabel">&iquestCu&aacutendo te viene mejor tener la entrevista?</h5>
	 	            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	 	              <i class="material-icons">close</i>
	 	            </button>
	 	          </div>
	 	          <div class="modal-body">
				   	<!-- <form id="myForm" class="form" action="mandar_mail.php" method="POST">
						<input type="hidden" name="empresa" value="<?php //echo $empresaID;?>">
						<input type="hidden" name="nombreEmpresa" value="<?php //echo $empresa['account_name'];?>">
						<input type="hidden" name="orientadora" value="<?php //echo $orientadora['mailResponsable'];?>">
						<input type="hidden" name="alumno" value="<?php //echo $nombreContacto;?>">
						<input type="hidden" name="negociacion" value="<?php //echo $lanegociacion;?>">
						<label>Datos entrevistador</label></br>
						<input type="text" class="form-control" name="nombreEntrevistador" placeholder="Nombre entrevistador" required>
						<input type="text" class="form-control" name="movilEntrevistador" placeholder="Movil entrevistador" required>
						<input type="text" class="form-control" name="mailEntrevistador" placeholder="Mail entrevistador" required>
						</br><label>Funciones/puesto</label></br>
						<textarea name="funciones" class="form-control" rows="8" required placeholder="Ej:Gestión de contenidos, gestión de redes sociales, creación y modificación de la web,..."></textarea>
						</br><label>Otros datos</label></br>
						<textarea name="mensaje" class="form-control" rows="8" required placeholder="Ej:Todos los d&iacute;as por la tarde excepto el viernes"></textarea>
						<script src="https://www.google.com/recaptcha/api.js" async defer></script>
						<div style="margin:20px auto;width:304px;" class="g-recaptcha brochure__form__captcha" data-sitekey="6LcDUpglAAAAALuQjzD0mdvE4Nhf7ILANuBpXMNk" data-callback="onSubmit"></div>
						<div class="buttons">
							<button id="enviar" type="submit" class="btn btn-primary" disabled>Enviar</button> 
						</div>
					</form> -->

				<div id='crmWebToEntityForm' class='zcwf_lblLeft crmWebToEntityForm' style='background-color: white;color: black;max-width: 600px;'>
  					<meta name='viewport' content='width=device-width, initial-scale=1.0'>
   					<META HTTP-EQUIV ='content-type' CONTENT='text/html;charset=UTF-8'>
   
					<form id='webform2350694000172764335' action='https://crm.zoho.com/crm/WebForm' name=WebForm2350694000172764335 method='POST' onSubmit='javascript:document.charset="UTF-8"; return checkMandatory2350694000172764335()' accept-charset='UTF-8'>
						<input type='text' style='display:none;' name='xnQsjsdp' value='3f61c4e446b1c67d1c672a04efdfae11a4c51ee768ab90c17a0e0c3345e3a3d8'></input> 
						<input type='hidden' name='zc_gad' id='zc_gad' value=''></input>
						<input type='text' style='display:none;' name='xmIwtLD' value='fe465bf9c691ad7dc6ee2c5a71b1e97159e6cd35b017e36260d9ed05da16e6ad33c43124725241f7daba6fee69fd7989'></input> 
						<input type='text'  style='display:none;' name='actionType' value='Q3VzdG9tTW9kdWxlMjc='></input>
						<input type='text' style='display:none;' name='returnURL' value='https&#x3a;&#x2f;&#x2f;nettdigitalschool.com&#x2f;gracias-matching&#x2f;' > </input>
						<!-- Do not remove this code. -->

						<input type='hidden' id='NAME' name='NAME' value="<?php echo $nombreEmpresa;?>"></input>
						<input type="hidden" name="COBJ27CF9" id="COBJ27CF9" value="<?php echo $nombreContacto;?>"></input>
						<input type="hidden" name="COBJ27CF6" id="COBJ27CF6" value="<?php echo $nombreNegociacion;?>"></input>
						<input type="hidden" name="COBJ27CF5" id="COBJ27CF5" value="<?php echo $orientadora['mailResponsable'];?>"></input>
						<input type='hidden' id='COBJ27CF8' name='COBJ27CF8' value='Matching'>
						<label>Datos entrevistador</label></br>
						<input type="text" class="form-control" name="COBJ27CF7" id="COBJ27CF7" maxlength='255' placeholder="Nombre entrevistador" required>
						<input type="text" class="form-control" name="COBJ27CF3" id="COBJ27CF3" maxlength='255' placeholder="Movil entrevistador" required>
						<input type="text" class="form-control" name="COBJ27CF4" id="COBJ27CF4" maxlength='255' placeholder="Mail entrevistador" required>

						</br><label>Funciones/puesto</label></br>
						<textarea id='COBJ27CF1' name='COBJ27CF1' class="form-control" rows="8" required placeholder="Ej:Gestión de contenidos, gestión de redes sociales, creación y modificación de la web,..."></textarea>
						</br><label>Fecha y lugar de la entrevista</label></br>
						<textarea id='COBJ27CF2' name='COBJ27CF2' class="form-control" rows="8" required placeholder="Ej:Todos los d&iacute;as por la tarde excepto el viernes"></textarea>
						</br><label><b>Es obligatorio realizar una prueba de nivel para que luego no haya problemas</b></br>
						¿Qué pruebas vas a realizar?</label></br>
						<textarea id='COBJ27CF15' name='COBJ27CF15' class="form-control" rows="8" required placeholder="Ej:Test de nivel con 10 preguntas tipo test, ..."></textarea>
						<script src="https://www.google.com/recaptcha/api.js" async defer></script>
						<div style="margin:20px auto;width:304px;" class="g-recaptcha brochure__form__captcha" data-sitekey="6LcDUpglAAAAALuQjzD0mdvE4Nhf7ILANuBpXMNk" data-callback="onSubmit"></div>
						<div class="buttons">
							<!-- <button id="enviar" type="submit" class="btn btn-primary" disabled>Enviar</button> -->
							<input type='submit' id='formsubmit' class='btn btn-primary' value='Enviar' title='Enviar'>
						</div>
					
						<!-- Do not remove this --- Analytics Tracking code starts --><script id='wf_anal' src='https://crm.zohopublic.com/crm/WebFormAnalyticsServeServlet?rid=85e5dde9203168c5398f0a96440f3cae83eed821df888613c039a63cd0c222f15a961b7483fde30e9e14214fbcffd462gid9f3838f20bc8a50e5c3fb1fc1b40911b5fa77e7004272a681d6c6576d75a72c6gid3edb7520fcfc437f0b4714c241e46b27073c40a1b30b64bb738a38282b3cefebgid53216ff2ec50666b780329491bd7980be6f35926535e22991ad2dae75e9ca711&tw=1e81fd0ecc9e55a0f920159bc0ffc17cdf70404194bea6dc39a4399fbbf7897d'></script><!-- Do not remove this --- Analytics Tracking code ends. -->
					</form>
				</div>

					<script>
						function enableSubmitButton() {
							document.getElementById("enviar").disabled = false;
						}

						function onSubmit(token) {
							// Aquí se verifica el token del recaptcha
							// y se habilita el botón de envío del formulario
							enableSubmitButton();
						}

					</script>
	 	          </div>
	 	        </div>
	 	      </div>
	 	    </div>
<?php
		}
	}
?>
	<!-- Optional JavaScript -->
	<!-- jQuery first, then Popper.js, then Bootstrap JS -->
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	</body>
</html>
<?php
	// $tiempo_final = microtime(true);
	// echo $tiempo = $tiempo_final - $tiempo_inicial;
?>