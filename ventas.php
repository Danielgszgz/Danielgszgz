<?php
	//$tiempo_inicial = microtime(true);

    ini_set( 'display_errors', 1 );
    error_reporting( E_ALL );

    //CONEXION Y FUNCIONES
    require 'classDashboard.php';
    $mysqli = conexion();

    //REQUERIMOS EL ARCHIVO FUNCIONESGENERALES
    require '../funciones/funcionesGenerales.php';

    //REQUERIAMOS EL ARCHIVOS CON LAS VARIABLES GENERALES
    require 'varGenerales.php';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Dashboard Ventas | NETT Digital School</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Dashboard comité NETT Digital School" name="description" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="../imgs/icono.ico">

        <!-- Bootstrap Css -->
        <link href="assets/css/bootstrap-dark.min.css" id="bootstrap-stylesheet" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="assets/css/app-dark.min.css" id="app-stylesheet" rel="stylesheet" type="text/css" />
        <!-- CSS comun -->
        <link href="assets/style.css" rel="stylesheet" type="text/css" />

        <!-- SCRIPT CHARTS-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.bundle.js"></script> 

    </head>

    <body data-layout="horizontal" data-topbar="dark">

        <!-- Begin page -->
        <div id="wrapper">

            <div class="content-page">
                <div class="content">

                    <!-- Start Content-->
                    <div class="container-fluid">
                        <a href="menuDashboard.php">Volver a menú</a>
<?php
                        

                        //TITULO
                        titulos('VENTAS');

						//OBTENEMOS LOS OBJETIVOS Y VENTAS DE 19-20
						$ventas24 = ventasAno();
?>
                    	<div class="row">
                    		<div class="col-xl-3"></div>
                            <div class="col-xl-6">
                                <div class="card-box">
                                    <h4 class="header-title mt-0">Ventas 2020 / 2024</h4>

                                    <div class="widget-chart text-center">
                                        <input id="ventas20_24" type="hidden" value="<?php echo $ventas24;?>">
                                        <canvas id="chartVentas20_24" width="100%" height="100%"></canvas>
                                    </div>
                                </div>
                            </div><!-- end col -->
                            <div class="col-xl-3"></div>
                        </div>
                        <!-- end row -->

                        <div class="row" style="padding-bottom:10px;">
                            <div class="col-xl-4 col-md-6"></div><!-- end col -->
                            <div class="col-xl-4 col-md-6" style="text-align:center;">
                                <a href="https://crm.zoho.com/crm/org641230294/tab/Reports/2350694000176581005" style="font-size:12px;">Ver informe ventas 24</a>
                            </div><!-- end col -->
                            <div class="col-xl-4 col-md-6"></div><!-- end col -->
                        </div>
                        <!-- end row -->

                        <div class="row">
                            <div class="col-xl-4 col-md-6">
<?php
                                $negociaciones = negociaciones("WHERE closing_date between '".$fechaInicioSemana."' AND '".$fechaFinSemana."' AND stage ='Cerrado (venta)' AND recolocaci_n=0 AND (type LIKE '%Beca%' OR type LIKE '%Privado%' OR type LIKE '%Online%') AND pagada_1_cuota = 1 AND firmado_convenio = 1 AND firmado_contrato = 1 AND responsable_de_orientaci_n != 'NETT'");
                            
                                //CUADRO CON 3 VARIABLES
                                cuadro3var("SEMANA",$negociaciones,"Ventas");
?>
                            </div><!-- end col -->

                            <div class="col-xl-4 col-md-6">
<?php
                                $negociaciones = negociaciones("WHERE closing_date between '".$fechaInicioMes."' and '".$fechaFinMes."' AND stage ='Cerrado (venta)' AND recolocaci_n=0 AND (type LIKE '%Beca%' OR type LIKE '%Privado%' OR type LIKE '%Online%') AND pagada_1_cuota = 1 AND firmado_convenio = 1 AND firmado_contrato = 1 AND responsable_de_orientaci_n != 'NETT'");
                            
                                //CUADRO CON 3 VARIABLES
                                cuadro3var("MES",$negociaciones,"Ventas");
?>
                            </div><!-- end col -->

                            <div class="col-xl-4 col-md-6">
<?php
                                $negociaciones = negociaciones("WHERE closing_date between '".$fechaInioAno."' and '".$fechaFinAno."' AND stage ='Cerrado (venta)' AND recolocaci_n=0 AND (type LIKE '%Beca%' OR type LIKE '%Privado%' OR type LIKE '%Online%') AND pagada_1_cuota = 1 AND firmado_convenio = 1 AND firmado_contrato = 1 AND responsable_de_orientaci_n != 'NETT'");
                            
                                //CUADRO CON 3 VARIABLES
                                cuadro3var("AÑO",$negociaciones,"Ventas");
?>
                            </div><!-- end col -->
                        </div>
                        <!-- end row -->
<?php
                        //OBTENEMOS LAS NEGOCIACIONES POR CADA ORIENTADORA EN UN ARRAY
                        $negociacionesOrientadoraSemana = negociacionOrientadoras($orientadoras,$fechaInicioSemana,$fechaFinSemana);

                        //OBTENEMOS LAS NEGOCIACIONES POR CADA ORIENTADORA EN UN ARRAY
                        $negociacionesOrientadoraMes = negociacionOrientadoras($orientadoras,$fechaInicioMes,$fechaFinMes);

                        //OBTENEMOS LAS NEGOCIACIONES POR CADA ORIENTADORA EN UN ARRAY
                        $negociacionesOrientadoraAno = negociacionOrientadoras($orientadoras,$fechaInioAno,$fechaFinAno);
?>

                        <div class="row">
                            <div class="col-xl-4">
                                <div class="card-box">
                                    <h4 class="header-title mt-0">Ventas Semana</h4>
                                    <div class="widget-chart text-center">
                                        <input id="ventasSemanaOrientadora" type="hidden" value="<?php echo $negociacionesOrientadoraSemana;?>">
                                        <canvas id="chartVentasSemanaOrient" width="100%" height="100%"></canvas>
                                    </div>
                                </div>
                            </div><!-- end col -->

                            <div class="col-xl-4">
                                <div class="card-box">
                                    <h4 class="header-title mt-0">Ventas Mes</h4>
                                    <div class="widget-chart text-center">
                                        <input id="ventasMesOrientadora" type="hidden" value="<?php echo $negociacionesOrientadoraMes;?>">
                                        <canvas id="chartVentasMesOrient" width="100%" height="100%"></canvas>
                                    </div>
                                </div>
                            </div><!-- end col -->

                            <div class="col-xl-4">
                                <div class="card-box">
                                    <h4 class="header-title mt-0">Ventas Año</h4>
                                    <div class="widget-chart text-center">
                                        <input id="ventasAnoOrientadora" type="hidden" value="<?php echo $negociacionesOrientadoraAno;?>">
                                        <canvas id="chartVentasAnoOrient" width="100%" height="100%"></canvas>
                                    </div>
                                </div>
                            </div><!-- end col -->

                        </div>
                        <!-- end row -->
<?php
                        //TITULO
                        titulos('VENTAS MEDIAS ÚLTIMOS 3 MESES');
                        $hoy = date('Y/m/d');
?>
                        <div class="row">
                            <div class="col-xl-3 col-md-6">
<?php
                                $negOrientadora3meses = negocOrientadora('Belen',$fecha3meses,$hoy);
                                cuadroOrientadoraVentas("belen.png","Belen",$negOrientadora3meses);
?>
                            </div><!-- end col -->

                            <div class="col-xl-3 col-md-6">
<?php
                                $negOrientadora3meses = negocOrientadora('Carmen Abad',$fecha3meses,$hoy);
                                cuadroOrientadoraVentas("carmen.png","Carmen",$negOrientadora3meses);
?>
                            </div><!-- end col -->

                            <div class="col-xl-3 col-md-6">
<?php
                                $negOrientadora3meses = negocOrientadora('Estela',$fecha3meses,$hoy);
                                cuadroOrientadoraVentas("estela.png","Estela",$negOrientadora3meses);
?>
                            </div><!-- end col -->

                            <div class="col-xl-3 col-md-6">
<?php
                                $negOrientadora3meses = negocOrientadora('Irene',$fecha3meses,$hoy);
                                cuadroOrientadoraVentas("irene.png","Irene",$negOrientadora3meses);
?>
                            </div><!-- end col -->

                            <div class="col-xl-3 col-md-6">
<?php
                                $negOrientadora3meses = negocOrientadora('Isabel',$fecha3meses,$hoy);
                                cuadroOrientadoraVentas("isabel.png","Isabel",$negOrientadora3meses);
?>
                            </div><!-- end col -->

                            <div class="col-xl-3 col-md-6">
<?php
                                $negOrientadora3meses = negocOrientadora('Salcedo',$fecha3meses,$hoy);
                                cuadroOrientadoraVentas("maricarmen.png","M.C.",$negOrientadora3meses);
?>
                            </div><!-- end col -->

                            <div class="col-xl-3 col-md-6">
<?php
                                $negOrientadora3meses = negocOrientadora('Latorre',$fecha3meses,$hoy);
                                cuadroOrientadoraVentas("laura.png","Laura L",$negOrientadora3meses);
?>
                            </div><!-- end col -->

                            <div class="col-xl-3 col-md-6">
<?php
                                $negOrientadora3meses = negocOrientadora('Blasco',$fecha3meses,$hoy);
                                cuadroOrientadoraVentas("lauraB.png","Laura B",$negOrientadora3meses);

                                unset($hoy);
?>
                            </div><!-- end col -->
                        </div>
                        <!-- end row --> 

                        <div class="col-xl-12">
                            <div class="card-box">
                                <div class="row">
                                    <div class="col-xl-6">
                                        <h4 class="header-title mt-0 mb-3">Ventas ultimo año</h4>
                                    </div>
                                    <div class="col-xl-6">                                 
                                        <table style="float:right;">
                                            <tr> 
                                                <td style="padding:0px 5px;">Leyenda</td>
                                                <td style="background-color:#f99696;color:black;padding:0px 5px;">0</td>
                                                <td style="background-color:#fbdba0;color:black;padding:0px 5px;">1</td>
                                                <td style="background-color:#a2fba1;color:black;padding:0px 5px;">2-4</td>
                                                <td style="background-color:#a4fbfd;color:black;padding:0px 5px;">5-6</td>
                                                <td style="background-color:#eba2fb;color:black;padding:0px 5px;">+7</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div style="float:right;"></div>
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0 tableWhite">
                                        <thead>
                                            <tr>
                                                <th></th>
<?php 
                                                foreach ($meses12Atras as &$value) {
?>
                                                    <th><?php echo $value;?></th>
<?php                                                    
                                                }
?>
                                                <th>TOTAL</tr>
                                            </tr>
                                        </thead>
                                        <tbody>
<?php
                                            foreach ($orientadoras as &$orientadora) {
?>
                                                <tr>
<?php                                                    
                                                    if($orientadora == "Carmen Abad"){ echo '<td>Carmen</td>';
                                                    }else{ echo '<td>'.$orientadora.'</td>'; }
                                                    
                                                    $contNeg = 0;
                                                    for ($i=0; $i < 12; $i++) { 
                                                        $tiempo = strtotime("- ".$i." month"); # Restamos -6 meses
                                                        # Y formateamos
                                                        $fechaInicioMesB = date("Y-n-01", $tiempo);
                                                        $fechaFinMesB = date("Y-m-t", strtotime($fechaInicioMesB));
                                                        $negociaciones = $mysqli->query("SELECT id FROM zoho_deals WHERE closing_date between '".$fechaInicioMesB."' and '".$fechaFinMesB."' AND stage ='Cerrado (venta)' AND recolocaci_n=0 AND responsable_de_orientaci_n LIKE '%".$orientadora."%' AND (type LIKE '%Beca%' OR type LIKE '%Privado%' OR type LIKE '%Online%') AND pagada_1_cuota = 1 AND firmado_convenio = 1 AND firmado_contrato = 1");
                                                        $cuantasVentas = $negociaciones->num_rows;
                                                        if($cuantasVentas == 0) {
?>
                                                            <td style="background-color:#f99696;color:black;font-weight:bold;"><?php echo $cuantasVentas;?></td>
<?php
                                                        }elseif ($cuantasVentas == 1) {
?>
                                                            <td style="background-color:#fbdba0;color:black;font-weight:bold;"><?php echo $cuantasVentas;?></td>
<?php
                                                        }elseif ($cuantasVentas >= 2 && $cuantasVentas <= 4) {
?>
                                                            <td style="background-color:#a2fba1;color:black;font-weight:bold;"><?php echo $cuantasVentas;?></td>
<?php
                                                        }elseif ($cuantasVentas >= 5 && $cuantasVentas <= 6) {
?>
                                                            <td style="background-color:#a4fbfd;color:black;font-weight:bold;"><?php echo $cuantasVentas;?></td>
<?php
                                                        }elseif ($cuantasVentas >= 7) {
?>
                                                            <td style="background-color:#eba2fb;color:black;font-weight:bold;"><?php echo $cuantasVentas;?></td>
<?php
                                                        }else{
?>
                                                            <td><?php echo $cuantasVentas;?></td>
<?php
                                                        }

                                                        $contNeg = $contNeg + $cuantasVentas;
                                                    }
                                                    //OBTENEMOS EL TOTAL POR ORIENTADORA
                                                    unset($negociaciones);unset($cuantasVentas);
?>                                                    
                                                    <td style="background-color:rgba(255,255,255,0.1);color:white;font-weight:bold;"><?php echo $contNeg;?></td>
                                                </tr>
<?php
                                            }
?>
                                            <tr style="background-color:rgba(255,255,255,0.1);">
                                                <td>TOTAL</td>
<?php                                                
                                                $contNegTotal = 0;
                                                for ($i=0; $i < 12; $i++) { 
                                                    $tiempo = strtotime("- ".$i." month"); # Restamos -6 meses
                                                    //Y formateamos
                                                    $fechaInicioMesB = date("Y-n-01", $tiempo);
                                                    $fechaFinMesB = date("Y-m-t", strtotime($fechaInicioMesB));
                                                    $negociaciones = $mysqli->query("SELECT id FROM zoho_deals WHERE closing_date between '".$fechaInicioMesB."' and '".$fechaFinMesB."' AND stage ='Cerrado (venta)' AND recolocaci_n=0  AND (type LIKE '%Beca%' OR type LIKE '%Privado%' OR type LIKE '%Online%') AND pagada_1_cuota = 1 AND firmado_convenio = 1 AND firmado_contrato = 1 AND responsable_de_orientaci_n != 'NETT'");
                                                    $cuantasVentas = $negociaciones->num_rows;
?>
                                                    <td><?php echo $cuantasVentas;?></td>
<?php                                                    
                                                    $contNegTotal = $contNegTotal + $cuantasVentas;
                                                }

                                                unset($cuantasVentas);
?>
                                                <td style="background-color:rgba(255,255,255,0.1);color:white;font-weight:bold;"><?php echo $contNegTotal;?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div><!-- end col -->
<?php
                        //TITULO
                        titulos('VENTAS AÑO POR MASTER');
?>
                        <div class="row">
                            <div class="col-xl-4 col-md-6">
<?php
                                $negociaciones = negociaciones("WHERE closing_date between '".$fechaInioAno."' and '".$fechaFinAno."' AND stage ='Cerrado (venta)' AND recolocaci_n=0 AND (type LIKE '%Beca - MPD%' OR type LIKE '%Privado - MPD%' OR type LIKE '%Online%') AND pagada_1_cuota = 1 AND firmado_convenio = 1 AND firmado_contrato = 1 AND responsable_de_orientaci_n != 'NETT'");
                            
                                //CUADRO CON 3 VARIABLES
                                cuadro3var("MPD 24",$negociaciones,"Ventas");
?>
                            </div><!-- end col -->

                            <div class="col-xl-4 col-md-6">
<?php
                                $negociaciones = negociaciones("WHERE closing_date between '2023-01-01' and '2023-12-31' AND stage ='Cerrado (venta)' AND recolocaci_n=0 AND (type LIKE '%Beca - MPD%' OR type LIKE '%Privado - MPD%' OR type LIKE '%Online%') AND pagada_1_cuota = 1 AND firmado_convenio = 1 AND firmado_contrato = 1 AND responsable_de_orientaci_n != 'NETT'");
                            
                                //CUADRO CON 3 VARIABLES
                                cuadro3var("MPD 23",$negociaciones,"Ventas");
?>
                            </div><!-- end col -->

                            <div class="col-xl-4 col-md-6">
<?php
                                $negociaciones = negociaciones("WHERE closing_date between '2022-01-01' and '2022-12-31' AND stage ='Cerrado (venta)' AND recolocaci_n=0 AND (type LIKE '%Beca - MPD%' OR type LIKE '%Privado - MPD%' OR type LIKE '%Online%') AND pagada_1_cuota = 1 AND firmado_convenio = 1 AND firmado_contrato = 1 AND responsable_de_orientaci_n != 'NETT'");
                            
                                //CUADRO CON 3 VARIABLES
                                cuadro3var("MPD 22",$negociaciones,"Ventas");
?>
                            </div><!-- end col -->

                        </div>
                        <!-- end row -->

                        <div class="row">
                            <div class="col-xl-4 col-md-6">
<?php
                                $negociaciones = negociaciones("WHERE closing_date between '".$fechaInioAno."' and '".$fechaFinAno."' AND stage ='Cerrado (venta)' AND recolocaci_n=0 AND (type LIKE '%Beca - AE%' OR type LIKE '%Beca - TD%' OR type LIKE '%Beca - TGD%' OR type LIKE '%Privado - AE%' OR type LIKE '%Privado - TD%' OR type LIKE '%Privado - TGD%') AND pagada_1_cuota = 1 AND firmado_convenio = 1 AND firmado_contrato = 1 AND responsable_de_orientaci_n != 'NETT'");
                            
                                //CUADRO CON 3 VARIABLES
                                cuadro3var("AE 24",$negociaciones,"Ventas");
?>
                            </div><!-- end col -->

                            <div class="col-xl-4 col-md-6">
<?php
                                $negociaciones = negociaciones("WHERE closing_date between '2023-01-01' and '2023-12-31' AND stage ='Cerrado (venta)' AND recolocaci_n=0 AND (type LIKE '%Beca - AE%' OR type LIKE '%Beca - TD%' OR type LIKE '%Beca - TGD%' OR type LIKE '%Privado - AE%' OR type LIKE '%Privado - TD%' OR type LIKE '%Privado - TGD%') AND pagada_1_cuota = 1 AND firmado_convenio = 1 AND firmado_contrato = 1 AND responsable_de_orientaci_n != 'NETT'");
                            
                                //CUADRO CON 3 VARIABLES
                                cuadro3var("AE 23",$negociaciones,"Ventas");
?>
                            </div><!-- end col -->

                            <div class="col-xl-4 col-md-6">
<?php
                                $negociaciones = negociaciones("WHERE closing_date between '2022-01-01' and '2022-12-31' AND stage ='Cerrado (venta)' AND recolocaci_n=0 AND (type LIKE '%Beca - AE%' OR type LIKE '%Beca - TD%' OR type LIKE '%Beca - TGD%' OR type LIKE '%Privado - AE%' OR type LIKE '%Privado - TD%' OR type LIKE '%Privado - TGD%') AND pagada_1_cuota = 1 AND firmado_convenio = 1 AND firmado_contrato = 1 AND responsable_de_orientaci_n != 'NETT'");
                            
                                //CUADRO CON 3 VARIABLES
                                cuadro3var("AE 22",$negociaciones,"Ventas");
?>
                            </div><!-- end col -->

                        </div>
                        <!-- end row --> 

                        <div class="row">
                            <div class="col-xl-4 col-md-6">
<?php
                                $negociaciones = negociaciones("WHERE closing_date between '".$fechaInioAno."' and '".$fechaFinAno."' AND stage ='Cerrado (venta)' AND recolocaci_n=0 AND (type LIKE '%Beca - DWA%' OR type LIKE '%Privado - DWA%') AND pagada_1_cuota = 1 AND firmado_convenio = 1 AND firmado_contrato = 1 AND responsable_de_orientaci_n != 'NETT'");
                            
                                //CUADRO CON 3 VARIABLES
                                cuadro3var("DWA 24",$negociaciones,"Ventas");
?>
                            <a href="https://crm.zoho.com/crm/org641230294/tab/Reports/2350694000206229001" style="font-size:12px;float:right;">Ver informe 24</a>
                            </div><!-- end col -->
                            

                            <div class="col-xl-4 col-md-6">
<?php
                                $negociaciones = negociaciones("WHERE closing_date between '2023-01-01' and '2023-12-31' AND stage ='Cerrado (venta)' AND recolocaci_n=0 AND (type LIKE '%Beca - DWA%' OR type LIKE '%Privado - DWA%') AND pagada_1_cuota = 1 AND firmado_convenio = 1 AND firmado_contrato = 1 AND responsable_de_orientaci_n != 'NETT'");
                            
                                //CUADRO CON 3 VARIABLES
                                cuadro3var("DWA 23",$negociaciones,"Ventas");
?>
                            <a href="https://crm.zoho.com/crm/org641230294/tab/Reports/2350694000206229010" style="font-size:12px;float:right;">Ver informe 23</a>
                            </div><!-- end col -->

                            <div class="col-xl-4 col-md-6">
<?php
                                $negociaciones = negociaciones("WHERE closing_date between '2022-01-01' and '2022-12-31' AND stage ='Cerrado (venta)' AND recolocaci_n=0 AND (type LIKE '%Beca - DWA%' OR type LIKE '%Privado - DWA%') AND pagada_1_cuota = 1 AND firmado_convenio = 1 AND firmado_contrato = 1 AND responsable_de_orientaci_n != 'NETT'");
                            
                                //CUADRO CON 3 VARIABLES
                                cuadro3var("DWA 22",$negociaciones,"Ventas");
?>
                            <a href="https://crm.zoho.com/crm/org641230294/tab/Reports/2350694000206229025" style="font-size:12px;float:right;">Ver informe 22</a>
                            </div><!-- end col -->
                        </div>
                        <!-- end row -->
                        
<?php
                        //TITULO Vacio
                        titulos('&nbsp;');
?>                        

                        <div class="col-xl-12">
                            <div class="card-box">
                                <div class="row">
                                    <div class="col-xl-6">
                                        <h4 class="header-title mt-0 mb-3">Ventas ultimo año por Máster</h4>
                                    </div>
                                    <div class="col-xl-6">                                 
                                        <table style="float:right;">
                                            <tr> 
                                                <td style="padding:0px 5px;">Leyenda</td>
                                                <td style="background-color:#f99696;color:black;padding:0px 5px;">0</td>
                                                <td style="background-color:#fbdba0;color:black;padding:0px 5px;">1-5</td>
                                                <td style="background-color:#a2fba1;color:black;padding:0px 5px;">6-10</td>
                                                <td style="background-color:#a4fbfd;color:black;padding:0px 5px;">11-15</td>
                                                <td style="background-color:#eba2fb;color:black;padding:0px 5px;">+16</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div style="float:right;"></div>
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0 tableWhite">
                                        <thead>
                                            <tr>
                                                <th></th>
<?php 
                                                foreach ($meses12Atras as &$value) {
?>
                                                    <th><?php echo $value;?></th>
<?php                                                    
                                                }
?>
                                                <th>TOTAL</tr>
                                            </tr>
                                        </thead>
                                        <tbody>
<?php
                                            foreach ($masters as &$master) {
?>
                                                <tr>
<?php                                                    
                                                    echo '<td>'.$master.'</td>'; 
                                                    
                                                    $contNeg = 0;
                                                    for ($i=0; $i < 12; $i++) { 
                                                        $tiempo = strtotime("- ".$i." month"); # Restamos -6 meses
                                                        # Y formateamos
                                                        $fechaInicioMesB = date("Y-n-01", $tiempo);
                                                        $fechaFinMesB = date("Y-m-t", strtotime($fechaInicioMesB));
                                                        $negociaciones = $mysqli->query("SELECT id FROM zoho_deals WHERE closing_date between '".$fechaInicioMesB."' and '".$fechaFinMesB."' AND stage ='Cerrado (venta)' AND recolocaci_n=0 AND type LIKE '%- ".$master."%' AND (type LIKE '%Beca%' OR type LIKE '%Privado%' OR type LIKE '%Online%') AND pagada_1_cuota = 1 AND firmado_convenio = 1 AND firmado_contrato = 1");
                                                        $cuantasVentas = $negociaciones->num_rows;
                                                        if($cuantasVentas == 0) {
?>
                                                            <td style="background-color:#f99696;color:black;font-weight:bold;"><?php echo $cuantasVentas;?></td>
<?php
                                                        }elseif ($cuantasVentas >= 1 && $cuantasVentas <= 5) {
?>
                                                            <td style="background-color:#fbdba0;color:black;font-weight:bold;"><?php echo $cuantasVentas;?></td>
<?php
                                                        }elseif ($cuantasVentas >= 6 && $cuantasVentas <= 10) {
?>
                                                            <td style="background-color:#a2fba1;color:black;font-weight:bold;"><?php echo $cuantasVentas;?></td>
<?php
                                                        }elseif ($cuantasVentas >= 11 && $cuantasVentas <= 15) {
?>
                                                            <td style="background-color:#a4fbfd;color:black;font-weight:bold;"><?php echo $cuantasVentas;?></td>
<?php
                                                        }elseif ($cuantasVentas >= 16) {
?>
                                                            <td style="background-color:#eba2fb;color:black;font-weight:bold;"><?php echo $cuantasVentas;?></td>
<?php
                                                        }else{
?>
                                                            <td><?php echo $cuantasVentas;?></td>
<?php
                                                        }

                                                        $contNeg = $contNeg + $cuantasVentas;
                                                    }
                                                    //OBTENEMOS EL TOTAL POR MASTER
                                                    unset($negociaciones);unset($cuantasVentas);
?>                                                    
                                                    <td style="background-color:rgba(255,255,255,0.1);color:white;font-weight:bold;"><?php echo $contNeg;?></td>
                                                </tr>
<?php
                                            }
?>
                                            <tr style="background-color:rgba(255,255,255,0.1);">
                                                <td>TOTAL</td>
<?php                                                
                                                $contNegTotal = 0;
                                                for ($i=0; $i < 12; $i++) { 
                                                    $tiempo = strtotime("- ".$i." month"); # Restamos -6 meses
                                                    //Y formateamos
                                                    $fechaInicioMesB = date("Y-n-01", $tiempo);
                                                    $fechaFinMesB = date("Y-m-t", strtotime($fechaInicioMesB));
                                                    $negociaciones = $mysqli->query("SELECT id FROM zoho_deals WHERE closing_date between '".$fechaInicioMesB."' and '".$fechaFinMesB."' AND stage ='Cerrado (venta)' AND recolocaci_n=0  AND (type LIKE '%Beca%' OR type LIKE '%Privado%' OR type LIKE '%Online%') AND pagada_1_cuota = 1 AND firmado_convenio = 1 AND firmado_contrato = 1 AND responsable_de_orientaci_n != 'NETT'");
                                                    $cuantasVentas = $negociaciones->num_rows;
?>
                                                    <td><?php echo $cuantasVentas;?></td>
<?php                                                    
                                                    $contNegTotal = $contNegTotal + $cuantasVentas;
                                                }

                                                unset($cuantasVentas);
?>
                                                <td style="background-color:rgba(255,255,255,0.1);color:white;font-weight:bold;"><?php echo $contNegTotal;?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div><!-- end col -->
<?php                        
                        //TITULO
                        titulos('OBJETIVOS DE VENTAS');
?>                        
                        <!-- start page title -->
                        <div class="row" style="margin-bottom:20px;">
                            <div class="col-12">
                                <div class="page-title-box d-flex align-items-center justify-content-between">
                                    <h4 class="page-title" style="margin: 0 auto;"><a href="https://docs.google.com/spreadsheets/d/1YqJXwISWYoFoL2KOBV2svDiEqYFBg1galptSBkf_rY0/edit#gid=434936237" target="_blank"><button type="button" class="btn btn-bordered-info btn-lg botonAzul">Ir a la página</button></a></h4>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                    </div>
                    <!-- end row contact fluid-->
<?php
                //FOOTER
                footer();
?>
            </div>
        </div>
        <!-- END wrapper -->

        <!-- Vendor js -->
        <script src="assets/js/vendor.min.js"></script>
        <script src="assets/libs/jquery-knob/jquery.knob.min.js"></script>
        <script src="assets/libs/morris-js/morris.min.js"></script>
        <script src="assets/libs/raphael/raphael.min.js"></script>
        <script src="assets/js/pages/dashboard.init.js"></script>
        <script src="assets/js/app.min.js"></script>

        <script type="text/javascript" src="js/datosDashboardVentas.js"></script>
    </body>
</html>
<?php
	//$tiempo_final = microtime(true);
	//echo $tiempo = $tiempo_final - $tiempo_inicial;
?>