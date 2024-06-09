<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR);

include_once '../vendor/clases/classInitializer.php';

use com\zoho\api\authenticator\OAuthBuilder;
use com\zoho\api\authenticator\store\DBBuilder;
use com\zoho\api\authenticator\store\FileStore;
use com\zoho\crm\api\InitializeBuilder;
use com\zoho\crm\api\UserSignature;
use com\zoho\crm\api\dc\USDataCenter;
use com\zoho\api\logger\LogBuilder;
use com\zoho\api\logger\Levels;
use com\zoho\crm\api\SDKConfigBuilder;
use com\zoho\crm\api\HeaderMap;
use com\zoho\crm\api\ParameterMap;
use com\zoho\crm\api\layouts\Layout;
use com\zoho\crm\api\record\APIException;
use com\zoho\crm\api\record\RecordOperations;
use com\zoho\crm\api\record\ResponseWrapper;
use com\zoho\crm\api\record\SearchRecordsParam;
use com\zoho\crm\api\util\Choice;
use com\zoho\crm\api\record\Comment;
use com\zoho\crm\api\record\Consent;
use com\zoho\crm\api\attachments\Attachment;
use com\zoho\crm\api\record\ImageUpload;
use com\zoho\crm\api\users\User;
use com\zoho\crm\api\record\UpdateRecordHeader;
use com\zoho\crm\api\record\ActionWrapper;
use com\zoho\crm\api\record\BodyWrapper;
use com\zoho\crm\api\record\FileDetails;
use com\zoho\crm\api\record\LineItemProduct;
use com\zoho\crm\api\record\LineTax;
use com\zoho\crm\api\record\Participants;
use com\zoho\crm\api\record\PricingDetails;
use com\zoho\crm\api\record\RecurringActivity;
use com\zoho\crm\api\record\RemindAt;
use com\zoho\crm\api\record\SuccessResponse;
use com\zoho\crm\api\tags\Tag;
use com\zoho\crm\api\record\{ApplyFeatureExecution, Cases, Solutions, Accounts, Campaigns, Calls, Leads, Tasks, Deals, Sales_Orders, Contacts, Quotes, Events, Price_Books, Purchase_Orders, Vendors};
use com\zoho\crm\api\record\Tax; 

class OfertaInfojobs{

	public static function searchRecords(string $moduleAPIName,$contactoCorreo,$contactoTelefono){

		$recordOperations = new RecordOperations();
		$paramInstance = new ParameterMap();
		$paramInstance->add(SearchRecordsParam::criteria(), "((Email:equals:$contactoCorreo) or (Phone:equals:$contactoTelefono) or (Mobile:equals:$contactoTelefono))");
		$headerInstance = new HeaderMap();

		$response = $recordOperations->searchRecords($moduleAPIName, $paramInstance, $headerInstance);
		$estadoZoho = "";
		if($response != NULL){
			$responseHandler = $response->getObject();
			if($responseHandler instanceof ResponseWrapper){
				$responseWrapper = $responseHandler;
				$records = $responseWrapper->getData();
				if($records != null){
					$recordClass = 'com\zoho\crm\api\record\Record';
					foreach($records as $record){
						//To get particular field value
						$idZoho = $record->getKeyValue("id");// FieldApiName
						$estadoZoho = $record->getKeyValue("Estado");// FieldApiName
						$Insert_mod_hoy = $record->getKeyValue("Insert_mod_hoy");// FieldApiName
                    }
                }
            }
        }

        $estadoZoho = serialize($estadoZoho);
        $state = strpos($estadoZoho,'";s');
        $estadoZoho = substr($estadoZoho, $state, -3);
        $state = strpos($estadoZoho,':"');
        $estadoZoho = substr($estadoZoho, $state+2);
        //echo $estadoZoho."<br>";

        if ($estadoZoho == "Cupón Beca" || $estadoZoho == "Descartado" || $estadoZoho == "Descartado NETT" || $estadoZoho == "Descartado no contesta" || $estadoZoho == "Descartado CURSO" || $estadoZoho == "Descartado PRIVADO" || $estadoZoho == "No disponible" || $estadoZoho == "Baja PRE MASTER"){
        	// var_dump($response);// die();
        	if($Insert_mod_hoy == date('Y-m-d')){
        		echo "<b><span style='color:red;'>Esta persona ya se inserto hoy: ".$Insert_mod_hoy."</span></b><br>";
        	}
        	return $response;
        }else if($estadoZoho == "0:"){
        	return "No existe-".$idZoho;
        }else{
            echo "<b>Su estado es: ".$estadoZoho." así que lo dejo como está.</b> Solo voy a modificar campo fecha.</br></br>";
            return "No insertar-".$idZoho;
        }
	}

    public static function crearRecord(string $moduleAPIName, $nombreContacto, $contactoCorreo, $contactoTelefono, $estado, $tituloOferta, $prov3) {

        $recordOperations = new RecordOperations();
        $bodyWrapper = new BodyWrapper();
        $records = array();
        $recordClass = 'com\zoho\crm\api\record\Record';
        $record1 = new $recordClass();
        $apply_feature_execution = new ApplyFeatureExecution();
        $apply_feature_execution->setName("layout_rules");
        $apply_feature_list = array();
        array_push($apply_feature_list,$apply_feature_execution);
        $bodyWrapper->setApplyFeatureExecution($apply_feature_list);

        $record1->addKeyValue("Last_Name", $nombreContacto);
        $record1->addKeyValue("Email", $contactoCorreo);
        if ($contactoTelefono != NULL) { $record1->addKeyValue("Phone", $contactoTelefono); }
        if ($contactoTelefono != NULL) { $record1->addKeyValue("Mobile", $contactoTelefono); }
        $record1->addKeyValue("Estado", new Choice($estado));
        $record1->addKeyValue("Responsable_de_orientaci_n", new Choice("NETT"));
        $record1->addKeyValue("Tipo_de_contacto", new Choice("Alumno"));
        $record1->addKeyValue("Origen", new Choice("infojobs"));
        $record1->addKeyValue("Origen1", new Choice("Infojobs"));
        $record1->addKeyValue("tituloOferta", $tituloOferta);
		$record1->addKeyValue("Insert_mod_hoy", date("Y-m-d"));

        //Decodificar la entidad HTML
    	$prov4 = html_entity_decode($prov3);
        $record1->addKeyValue("Provincia", new Choice($prov4));

        $tagList = array();
        $tag = new Tag();
        $tag->setName("");
        array_push($tagList, $tag);
        $record1->setTag($tagList);
        array_push($records, $record1);
        $bodyWrapper->setData($records);
        $trigger = array("approval", "workflow", "blueprint");
        $bodyWrapper->setTrigger($trigger);
        $headerInstance = new HeaderMap();
        $crearResponse = $recordOperations->createRecords($moduleAPIName, $bodyWrapper, $headerInstance);
        
        if($crearResponse != null){
            if($crearResponse->isExpected()){
                $actionHandler = $crearResponse->getObject();
                if($actionHandler instanceof ActionWrapper){
                    $actionWrapper = $actionHandler;
                    $actionResponses = $actionWrapper->getData();
                    foreach($actionResponses as $actionResponse){
                        if($actionResponse instanceof SuccessResponse){
                            $successResponse = $actionResponse;
                            if($successResponse->getStatus()->getValue()=="success"){
                            	echo "<span style='color:green;'>Se ha insertado correctamente</span></br></br>";
                            }else{
                            	echo "<span style='color:red;'>Algo ha fallado</span></br></br>";
                            }
                            //echo("Status: " . $successResponse->getStatus()->getValue() . "\n");
                            echo("Code: " . $successResponse->getCode()->getValue() . "\n");
                            echo("Details: " );
                            foreach($successResponse->getDetails() as $key => $value){
                                echo($key . " : "); print_r($value); echo("\n");
                            }
                            echo("Message: " . $successResponse->getMessage()->getValue() . "\n");
                        }else if($actionResponse instanceof APIException){
                            $exception = $actionResponse;
                            echo("Status: " . $exception->getStatus()->getValue() . "\n");
                            echo("Code: " . $exception->getCode()->getValue() . "\n");
                            echo("Details: " );
                            foreach($exception->getDetails() as $key => $value){
                                echo($key . " : " . $value . "\n");
                            }
                            echo("Message: " . $exception->getMessage()->getValue() . "\n");
                        }
                    }
                }else if($actionHandler instanceof APIException){
                    $exception = $actionHandler;
                    echo("Status: " . $exception->getStatus()->getValue() . "\n");
                    echo("Code: " . $exception->getCode()->getValue() . "\n");
                    echo("Details: " );
                    foreach($exception->getDetails() as $key => $value){
                        echo($key . " : " . $value . "\n");
                    }
                    echo("Message: " . $exception->getMessage()->getValue() . "\n");
                }
            }else{
                print_r($crearResponse);
            }
        }
    }

    public static function updateRecord($moduleAPIName, $recordId, $tituloOferta, $estado){
		$recordOperations = new RecordOperations();
        $request = new BodyWrapper();
        $records = array();
        $recordClass = 'com\zoho\crm\api\record\Record';
        $record1 = new $recordClass();
		$apply_feature_execution = new ApplyFeatureExecution();
		$apply_feature_execution->setName("layout_rules");
        $apply_feature_list = array();
		array_push($apply_feature_list,$apply_feature_execution);
		$request->setApplyFeatureExecution($apply_feature_list);

        /*Aqui añadimos los campos nuevos*/
        $record1->addKeyValue("Estado",new Choice($estado));
        $record1->addKeyValue("Origen", new Choice("infojobs"));
        $record1->addKeyValue("Origen1", new Choice("Infojobs"));
        $record1->addKeyValue("tituloOferta", $tituloOferta);
        $record1->addKeyValue("Insert_mod_hoy", date("Y-m-d"));

        $tagList = [];
		$tag = new Tag();
		$tag->setName("");
		array_push($tagList, $tag);
		$record1->setTag($tagList);
		array_push($records, $record1);
		$request->setData($records);
		$trigger = array("approval", "workflow", "blueprint");
		$request->setTrigger($trigger);
        $headerInstance = new HeaderMap();
		// $headerInstance->add(UpdateRecordHeader::XEXTERNAL(), "Quotes.Quoted_Items.Product_Name.Products_External");
		$response = $recordOperations->updateRecord($recordId, $moduleAPIName, $request, $headerInstance);
		if($response != null){
			if($response->isExpected()){
				$actionHandler = $response->getObject();
				if($actionHandler instanceof ActionWrapper){
					$actionWrapper = $actionHandler;
					$actionResponses = $actionWrapper->getData();
					foreach($actionResponses as $actionResponse){
						if($actionResponse instanceof SuccessResponse){
							$successResponse = $actionResponse;
							if($successResponse->getStatus()->getValue()=="success"){
                            	echo "<span style='color:green;'>Se ha actualizado correctamente</span></br></br>";
                            }else{
                            	echo "<span style='color:red;'>Algo ha fallado</span></br></br>";
                            }
							//echo("Status: " . $successResponse->getStatus()->getValue() . "\n");
							echo("Code: " . $successResponse->getCode()->getValue() . "\n");
							echo("Details: " );
							foreach($successResponse->getDetails() as $key => $value){
								echo($key . " : ");
								print_r($value);
								echo("\n");
							}
							echo("Message: " . $successResponse->getMessage()->getValue() . "\n");
						}else if($actionResponse instanceof APIException){
							$exception = $actionResponse;
							echo("Status: " . $exception->getStatus()->getValue() . "\n");
							echo("Code: " . $exception->getCode()->getValue() . "\n");
							echo("Details: " );
							foreach($exception->getDetails() as $key => $value){
								echo($key . " : " . $value . "\n");
							}
							echo("Message: " . $exception->getMessage()->getValue() . "\n");
						}
					}
				}else if($actionHandler instanceof APIException){
					$exception = $actionHandler;
					echo("Status: " . $exception->getStatus()->getValue() . "\n");
					echo("Code: " . $exception->getCode()->getValue() . "\n");
					echo("Details: " );
					foreach($exception->getDetails() as $key => $value){
						echo($key . " : " . $value . "\n");
					}
					echo("Message: " . $exception->getMessage()->getValue() . "\n");
				}
			}else{
				print_r($response);
			}
		}
    }

    public static function updateRecord2($moduleAPIName, $recordId){
		$recordOperations = new RecordOperations();
        $request = new BodyWrapper();
        $records = array();
        $recordClass = 'com\zoho\crm\api\record\Record';
        $record1 = new $recordClass();
		$apply_feature_execution = new ApplyFeatureExecution();
		$apply_feature_execution->setName("layout_rules");
        $apply_feature_list = array();
		array_push($apply_feature_list,$apply_feature_execution);
		$request->setApplyFeatureExecution($apply_feature_list);

        /*Solo modificamos el campo Insert_mod_hoy, para saber que se ha intentado modificar*/
        $record1->addKeyValue("Insert_mod_hoy", date("Y-m-d"));

        $tagList = [];
		$tag = new Tag();
		$tag->setName("");
		array_push($tagList, $tag);
		$record1->setTag($tagList);
		array_push($records, $record1);
		$request->setData($records);
		$trigger = array("approval", "workflow", "blueprint");
		$request->setTrigger($trigger);
        $headerInstance = new HeaderMap();
		// $headerInstance->add(UpdateRecordHeader::XEXTERNAL(), "Quotes.Quoted_Items.Product_Name.Products_External");
		$response = $recordOperations->updateRecord($recordId, $moduleAPIName, $request, $headerInstance);
		if($response != null){
			if($response->isExpected()){
				$actionHandler = $response->getObject();
				if($actionHandler instanceof ActionWrapper){
					$actionWrapper = $actionHandler;
					$actionResponses = $actionWrapper->getData();
					foreach($actionResponses as $actionResponse){
						if($actionResponse instanceof SuccessResponse){
							$successResponse = $actionResponse;
							if($successResponse->getStatus()->getValue()=="success"){
                            	echo "<span style='color:green;'>Se ha actualizado correctamente</span></br></br>";
                            }else{
                            	echo "<span style='color:red;'>Algo ha fallado</span></br></br>";
                            }
							//echo("Status: " . $successResponse->getStatus()->getValue() . "\n");
							echo("Code: " . $successResponse->getCode()->getValue() . "\n");
							echo("Details: " );
							foreach($successResponse->getDetails() as $key => $value){
								echo($key . " : ");
								print_r($value);
								echo("\n");
							}
							echo("Message: " . $successResponse->getMessage()->getValue() . "\n");
						}else if($actionResponse instanceof APIException){
							$exception = $actionResponse;
							echo("Status: " . $exception->getStatus()->getValue() . "\n");
							echo("Code: " . $exception->getCode()->getValue() . "\n");
							echo("Details: " );
							foreach($exception->getDetails() as $key => $value){
								echo($key . " : " . $value . "\n");
							}
							echo("Message: " . $exception->getMessage()->getValue() . "\n");
						}
					}
				}else if($actionHandler instanceof APIException){
					$exception = $actionHandler;
					echo("Status: " . $exception->getStatus()->getValue() . "\n");
					echo("Code: " . $exception->getCode()->getValue() . "\n");
					echo("Details: " );
					foreach($exception->getDetails() as $key => $value){
						echo($key . " : " . $value . "\n");
					}
					echo("Message: " . $exception->getMessage()->getValue() . "\n");
				}
			}else{
				print_r($response);
			}
		}
    }
}

require_once '../vendor/autoload.php';

require '../funciones/funcionesGenerales.php'; 

if(isset($_POST['codigo'])){
	$codigoCompleto = $_POST['codigo'];
    //echo $codigoCompleto = $_POST['codigo'];
    //die();

    $tituloOferta = $_POST['tituloOferta'];

    $posTit1 = strpos($tituloOferta,'BECA');
    $posTit2 = strpos($tituloOferta,'PRÁCTICAS');

    if($posTit1>=0 || $posTit2>=0){

	    // OBTENEMOS EL NOMBRE DEL CONTACTO
	    $posNombre1 = strpos($codigoCompleto, '<h1 class="xlarge padding-top contrast">');
		$nombre = substr($codigoCompleto, $posNombre1);
	    $posNombre2 = strpos($nombre, '">');
	    $posNombre3 = strpos($nombre, '</h1>');
	    $contactoNombre = substr($nombre, $posNombre2+2, $posNombre3);
	    $posNombre4 = strpos($contactoNombre, '<p');
	    $nombreContacto = substr($contactoNombre, 0, $posNombre4);
	    $nombreContacto = strip_tags(html_entity_decode($nombreContacto));
		$nombreContacto = strtoupper($nombreContacto);
	    echo "Nombre: ".$nombreContacto."<br><br>";

	    // OBTENEMOS EL CORREO DEL CONTACTO
	    $posCorreo1 = strpos($codigoCompleto, 'color: #ffffff;" href="mailto:');
	    $correo = substr($codigoCompleto, $posCorreo1);
	    $posCorreo2 = strpos($correo, 'mailto:');
	    $posCorreo3 = strpos($correo, '<li');
	    $correoContacto = substr($correo, $posCorreo2+7, $posCorreo3);
	    $contactoCorreo = explode('">',$correoContacto);
	    $mail = $contactoCorreo[0];
	    echo "Mail: ".$mail."<br><br>";    

		// OBTENEMOS EL TELEFONO DEL CONTACTO
		$posTelefono1 = strpos($codigoCompleto, '<li id="preferredPhone" class="iconfont-Phone contrast iconfont-filled user-select">');
		$telefono = substr($codigoCompleto, $posTelefono1);
		$posTelefono2 = strpos($telefono, '">');
		$posTelefono3 = strpos($telefono, '</li>');
		$telefonoContacto = substr($telefono, $posTelefono2+2, $posTelefono3);
		$telefonoContacto = explode('<div class',$telefonoContacto);
		$contactoTelefono = strip_tags($telefonoContacto[0]);
		$contactoTelefono = str_replace(" ","",$contactoTelefono);
	    $contactoTelefono = str_replace("&nbsp;", "", $contactoTelefono); // Quitamos los espacios en blanco
		$primerCaracter = substr($contactoTelefono, 0, 1); // devuelve "f"
	   
		if($primerCaracter == "+"){
	        $contactoTelefono = substr($contactoTelefono, 3, 11);
		}
		$contactoTelefono = substr($contactoTelefono, 0, 9);
	    echo "Teléfono: ".$contactoTelefono."<br><br>";
	   
	    // OBTENEMOS LA FECHA DE NACIMIENTO DEL CONTACTO
	    $posFechaNac1 = strpos($codigoCompleto, 'a&ntilde;os (');
	    $fechaNac = substr($codigoCompleto, $posFechaNac1);
	    $posFechaNac2 = strpos($fechaNac, '(');
	    $posFechaNac3 = strpos($fechaNac, ')');
	    $fechaNacContacto = substr($fechaNac, $posFechaNac2+1, $posFechaNac3);
	    $contactoFechaNac = explode(')',$fechaNacContacto);
	    $fechN = date("d-m-Y",strtotime(str_replace("/","-",$contactoFechaNac[0])));
	    $date = strtotime($fechN);
	    $date = date('d/m/Y h:i:s', $date);
	    //echo "Fecha de nacimiento: ".$date."<br><br>";
	    

	    // OBTENEMOS LA PROVINCIA DEL CONTACTO
	    $posProvincia = strpos($codigoCompleto, 'Direcci');
	    $provincia = substr($codigoCompleto, $posProvincia);
	    $posProvincia2 = strpos($provincia, 'Direcci');
	    $posProvincia3 = strpos($provincia, '<li class="is-print"');
	    $provinciaContacto = substr($provincia, $posProvincia2, $posProvincia3);
	    $prov = preg_replace("/[\r\n|\n|\r]+/", PHP_EOL, strip_tags($provinciaContacto));
	    $prov2 = substr($prov, 0, -1);
	    $prov2 = explode(',',$prov2);
	    $contArray = count($prov2);$contArray = $contArray-1;
	    $prov3 = $prov2[$contArray];
	    $prov3 = substr($prov3, 1); //Quitamos el primer caracter que es un espacio
	   	if($prov3 == "Castell&oacute;n/Castell&oacute;"){
	   		$prov3 = "Castell&oacute;n";
	   	}elseif($prov3 == "Alicante/Alacant"){
	        $prov3 = "Alicante";
	    }elseif($prov3 == "Valencia/Val&egrave;ncia"){
	        $prov3 = "Valencia";
	    }elseif($prov3 == "Álava/Araba"){
	        $prov3 = "Alava";
	    }elseif($prov3 == "Guip&uacute;zcoa/Gipuzkoa"){
	        $prov3 = "Guipuzcoa";  
	    }elseif($prov3 == "Ja&eacute;n"){
	        $prov3 = "Jaen";  
	    }elseif($prov3 == "C&aacute;diz"){
	        $prov3 = "Cadiz";
	    }elseif($prov3 == "M&aacute;laga"){
	        $prov3 = "Malaga";
	    }else if($prov3 == "Vizcaya/Bizkaia"){
	        $prov3 = "Vizcaya";
	    }else if($prov3 == "&Aacute;vila"){
	        $prov3 = "Avila";
	    }else if($prov3 == "&Aacute;lava/Araba"){
	        $prov3 = "Alava";
		}else if($prov3 == "Almer&iacute;a"){
	        $prov3 = "Almeria";
		}else if($prov3 == "Islas Baleares/Illes Balears"){
			$prov3 = "Baleares";	 	           
	    }else{
	        $prov3 = $prov3;
	    }

	    echo "Provincia: ".$prov3."<br><br>";

	    Initialize::initialize();

	    $moduleAPIName = "Contacts";
	    $estadoAnterior = OfertaInfojobs::searchRecords($moduleAPIName,$contactoCorreo[0],$contactoTelefono,$contactoTelefono);
	    $esString = is_string($estadoAnterior);

	    if($esString == 1){
	    	$pos1 = strpos($estadoAnterior, 'No insertar');
	    	$pos2 = strpos($estadoAnterior, 'No existe');

	    	if($pos1 == 0 || $pos2 == 0){
		    	$estadoAnt = explode('-',$estadoAnterior);
		    	$estadoAnterior = $estadoAnt[0];
		    	$idContacto = $estadoAnt[1];
		    }
	    }

	    if($estadoAnterior != "No insertar" AND $estadoAnterior != "No existe"){
	        $responseHandler = $estadoAnterior->getObject();
	        if($responseHandler instanceof ResponseWrapper){
	            $responseWrapper = $responseHandler;
	            $records = $responseWrapper->getData();
	            if($records != null){
	                $recordClass = 'com\zoho\crm\api\record\Record';
	                foreach($records as $record){
	                    $recordId = $record->getId();

	                    $estadostring= serialize($record->getKeyValue("Estado"));
				        $state = strpos($estadostring,'";s');
				        $estadostring = substr($estadostring, $state, -3);
				        $state = strpos($estadostring,':"');
				        $estadostring = substr($estadostring, $state+2);
	        			//echo "ESTADO: ".$estadostring."<br>";    
	                    $NoVolverAMatricularNunca = $record->getKeyValue("No_volver_a_matricular_nunca");

	                    if($NoVolverAMatricularNunca != 1) {
	                    	echo "Existe. Actualizamos el contacto. <br><br><a href='https://crm.nettformacion.com/cm-zoho/portales/infojobs.php'>Meter otro contacto</a><br><br><br><br>";
	                        OfertaInfojobs::updateRecord($moduleAPIName, $recordId, $tituloOferta, "Cupón Beca");

	                    }else{
	                        echo "Marcado como no volver a matricular nunca. <br><br><a href='https://crm.nettformacion.com/cm-zoho/portales/infojobs.php'>Meter otro contacto</a>";
	                        OfertaInfojobs::updateRecord2($moduleAPIName, $recordId);
	                    }
	                }
	            }
	        }
	    }else if($estadoAnterior == "No existe"){
	        echo "<b>No he encontrado el contacto. Lo insertamos.</b><br><br><a href='https://crm.nettformacion.com/cm-zoho/portales/infojobs.php'>Meter otro contacto</a><br><br><br><br>";
	        OfertaInfojobs::crearRecord($moduleAPIName, $nombreContacto, $contactoCorreo[0], $contactoTelefono, "Cupón Beca", $tituloOferta, $prov3);
	    }else{
	    	OfertaInfojobs::updateRecord2($moduleAPIName, $idContacto);
	    	echo "<br><a href='https://crm.nettformacion.com/cm-zoho/portales/infojobs.php'>Meter otro contacto</a><br>";
	    }
	}else{
		echo "Estas insertando una oferta de Bootcamp en Beca.<br> Comprueba el titulo<br>
		Si no es así avisa al responsable de IT.<br>
		<a href='https://crm.nettformacion.com/cm-zoho/portales/infojobs.php'>Meter otro contacto</a>";
	}

}else{
?>
<!DOCTYPE html> 
<html>
	<head>
	    <title>GUARDAR CONTACTOS DE INFOJOBS</title>

	    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

	    <!-- Bootstrap CSS -->
	    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

	    <link href="https://fonts.googleapis.com/css?family=Exo:400,700,900" rel="stylesheet">
	    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

	    <script src="https://cdn.tiny.cloud/1/oar4ufmum1ul0qben68i4c4gale9dz6lolotn8325qnpspdp/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
	    <script>
	          tinymce.init({
	          	selector: "textarea",  // change this value according to your HTML
	            plugins: "code",
	            toolbar: "code",
	            menubar: "none"
	        });
	    </script>
	</head>

	<body>
	    <div class="row">
	        <div class="col-md-1">&nbsp;</div>
	        <div class="col-md-5">
	            <h2>INFOJOBS</h2>
	        </div>
	        <div class="col-md-5">
	            <a href="https://crm.nettformacion.com/cm-zoho/portales/menuCupones.php">Volver al menú</a>
	        </div>
	        <div class="col-md-1">&nbsp;</div>
	    </div>
	    <div class="row">
	        <div class="col-md-1">&nbsp;</div>
	        <div class="col-md-10">
	            <form method="POST" action="#">
	                <input name="tituloOferta" id="tituloOferta" type="text" style="width:100%;" placeholder="Titulo de la oferta">
	                <!-- MUESTRA UN TEXTAREA, DONDE INSERTAR LA PAGINA DESDE DONDE COGERA LOS DATOS DEL CONTACTO -->
	                <textarea name="codigo" rows="20" cols="90"></textarea><br><br>
	                <!-- HAY QUE SELECCIONAR A LA ORIENTADORA A LA QUE VA DIRIGIDA EL CONTACTO -->
	                <input type="submit" value="Meter contacto"><br>
	              </form>
	          </div>
	          <div class="col-md-1">&nbsp;</div>
	    </div>
	</body>
</html>
<?php
}
?>