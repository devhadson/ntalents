<?php
  session_start();
  require_once('../config/dbconfig.php');

   if (isset($_POST['submit'])) 
   {
    if (isset($_POST['email'], $_POST['password']) && !empty($_POST['email']) && !empty($_POST['password'])) 
    {
      $vEmail       = trim($_POST['email']);
      $vPassword    = trim($_POST['password']);
      
      /* Iniciar el cifrado del password en base a  PASSWORD_BCRYPT*/
      $options      = array("cost"=>4);
      $hashCodePass = password_hash($vPassword,PASSWORD_BCRYPT,$options);
      
      /* FILTER_VALIDATE_EMAIL comprueba si la variable $vEmail es una dirección de email válida*/
      if(filter_var($vEmail, FILTER_VALIDATE_EMAIL))       
      {
        /* Script SQL para realizar la validación acerca de la existencia de 
        registros de email que se pretende registrar*/
        $sql      = "select * from users where email = :pemail";
        $script   = $pdo->prepare($sql);
        $sqlEmail = ['pemail'=>$vEmail];
        $script->execute($sqlEmail);/* Ejecutar (DML) SELECT */

        /* Sí el email no existe se procederé registrar en la base de datos, CASO CONTRARIO
           se muestra mensaje en pantalla El correo electrónco ya se encuentra registrado */
        if($script->rowCount() == 0)
        {
          /* Se válida la longitud de caracteres ingresados, si igual o mayor a 6 caracteres se proceder 
             Para estandarizar o mejorar el ingreso de la constraseña o password Te sugiero revisar el siguiente TUTORIAL:
             PHP - Validar una password (buscarlo con ese nombre en el blog.hadsonpar.com)*/
          if(strlen($vPassword) >= 6){

            $vNameUse     = strstr($vEmail, '@',true);/* se extrae sólo el nombre de usuario */
            $vidusertype  = 1;/* Usuario de tipo cliente */
            /*Script SQL para iniciar la registrar */
            $sql      = "insert into users (user, email, password, idusertype) values (:puser, :pemail, :ppassword, :pidusertype)";
            try{
              $handle   = $pdo->prepare($sql);

              $sqlParams = [':puser'=>$vNameUse,
                            ':pemail'=>$vEmail,
                            ':ppassword'=>$hashCodePass,
                            ':pidusertype'=>$vidusertype];
              $handle->execute($sqlParams);   /* Ejecutar (DML) INSERT */

              $success = 'User created successfully'; /* MENSAJE EN PANTALLA: CONFIRMACION DE INSERCIÓN */
            }
            catch(PDOException $e)
            {
              $errors[] = $e->getMessage();/* ERROR: CAPTURAR EXCEPCIÓN EN PANTALLA */
            }                

          }else{
            $valEmail    = $vEmail;
            $valPassword = $vPassword;
            $alertMessage2 = 'Please enter at least 6 characters';/* ALERTA EN PANTALLA */
          }
        }
        else
        {
          $alertEmail   = 'Email is already registered';/* ALERTA EN PANTALLA */
        }
      }else{
        $valEmail    = '';
        $valPassword = $vPassword;
        $errors[] = 'Please enter a valid email address'; /* ERROR: ALERTA EN PANTALLA */
      }
    }
    else
    {
      /* Validación de INPUTs email y password
        isset: Valida si la variable esta definida.
        empty: Valida si la variable esta vacia*/

      if(!isset($_POST['email']) || empty($_POST['email'])) /* Si no esta denifida y vacia */
      {
          $alertMessage1 = 'Please enter a valid email address';/* ALERTA EN PANTALLA */
      }
      else
      {
          $valEmail = $_POST['email'];
      }

      if(!isset($_POST['password']) || empty($_POST['password'])) /* Si no esta denifida y vacia */
      {
          $alertMessage2 = 'Please enter a password';/* ALERTA EN PANTALLA */
      }
      else
      {          
          $valPassword = $_POST['password'];          
      }
    }
  }

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Sign up| New Talents</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="../assets/img/favicon.png" rel="icon">

  <!-- Google Fonts -->  
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->  
  <link href="../assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="../assets/css/style.css" rel="stylesheet">

  <!-- =======================================================
* Template Name: nTalents - v1.0.0
* Template URL: https://hadsonpar.com/project/project-website-new-talents/
* Author: hadsonpar.com
* License: https://hadsonpar.com/license/
  ======================================================== -->

</head>

<body>
  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top">
    <div class="container-fluid container-xl d-flex align-items-center justify-content-between">

      <a href="../index.php" class="logo d-flex align-items-center">
        <img src="../assets/img/logo.png" alt="">
        <span style="color: #1b4194; text-align: left;">New Talents</span>
      </a>
      
      <nav id="navbar" class="navbar">
        <ul>          
          <li><a class="getstarted scrollto" href="../login.php">Login</a></li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav><!-- .navbar -->

    </div>
  </header><!-- End Header -->

  <!-- ======= Hero Section ======= -->
  <section id="registre" class="d-flex align-items-center">

    <div class="container">
      <div class="row">
        <div class="col-lg-8 d-lg-flex flex-lg-column justify-content-center align-items-stretch pt-5 pt-lg-0 order-2 order-lg-1" data-aos="fade-up">
          
          <div class="row">
            <div class="col-lg-12">
              <h1>Sign up and be part of the community of the best talents and professionals</h1>        
            </div>
            <div class="col-lg-8">    
            
            <?php 
              /* Sección de alertas (captura de mensajes y errores) */
              if(isset($errors) && count($errors) > 0)
              {
              	foreach($errors as $error_msg)
              	{
              		echo '<div class="alert alert-danger">'.$error_msg.'</div>';
              	}
              }

              if(isset($alertEmail) && !empty($alertEmail))
              {
                echo '<div class="alert alert-warning">'.$alertEmail.'</div>';
              }

              if(isset($success))
              {
                echo '<div class="alert alert-success">'.$success.'</div>';
              }
             ?>

              <form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>" role="form" class="php-email-form" data-aos="fade-up">
                <div class="form-group">
                  <input placeholder="Email" type="email" class="form-control" name="email" id="email" value="<?php echo ($valEmail??'')?>" />
                  <!--<div class="validate"></div>-->
                    <?php
                      echo '<div class="alertMessage">'.($alertMessage1??'').'</div>';
                    ?>
                </div>
                <div class="form-group">
                  <input placeholder="Password" type="password" class="form-control" name="password" id="password" value="<?php echo ($valPassword??'')?>" />
                  <!--<div class="validate"></div>-->
                    <?php
                      echo '<div class="alertMessage">'.($alertMessage2??'').'</div>';
                    ?>
                </div>
                <div class="mb-3">

                  <div class="legal">

                    Click in "Accept and register", you agree to New Talents Terms of Use, <a href="../legal/privacy-policy">Privacy Policy</a> and <a href="../legal/cookie-policy">Cookie Policy</a>.

                  </div>                  

                </div>
                <button class="registre-btn" name="submit" type="submit">Accept and register</button>
                
                  <div class="linea">or</div>
                
                <a href="#" class="google-btn"><i class="bx bxl-google"></i> Register with Google</a>

                <p>Are you already a New Talents user? <a href="../login">Login</a></p>

              </form>
            </div>

          </div>
        </div>        
        
        <div class="col-lg-4 d-lg-flex flex-lg-column align-items-stretch order-1 order-lg-2 hero-img" data-aos="fade-up">
          <div class="registre-img">
            <img src="../assets/img/hero-img.png" class="img-fluid" alt="">
          </div>
        </div>

      </div>
    </div>

  </section><!-- End Hero -->

  <!-- Vendor JS Files -->
  <script src="../assets/vendor/aos/aos.js"></script>
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Template Main JS File -->
  <script src="../assets/js/main.js"></script>

</body>

</html>