<?php
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: sign-in.html");
    exit();
}

$email = $_SESSION['email'];
$name = ucwords(str_replace('.', ' ', explode('@', $email)[0]));
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Espace Étudiant</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;300;400;700;900&display=swap" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/slick.css"/>
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
            <nav class="navbar navbar-expand-lg">
                <div class="container">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <a class="navbar-brand " href="index.html">
                        <strong><span>Espace </span> <span style="color: #a80404">Etudiant</span></strong>
                    </a>

                    <div class="d-lg-none">
                        <a href="sign-in.html" class="bi-person custom-icon me-3"></a>

                     
                    </div>

                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav mx-auto">
                            <li class="nav-item">
                                <a class="nav-link active" href="index.html">Accueil</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="cour.html">Cours</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="td e tp.html">TD/TP et Examen</a>
                            </li>

                     
                            <li class="nav-item">
                                <a class="nav-link" href="contact.html">Contact</a>
                            </li>
                        </ul>

                        <div class="d-none d-lg-block">
                            <a href="sign-in.html" class="bi-person custom-icon me-3"> </a>

                        </div>
                    </div>
                </div>
            </nav>

            <header class="site-header section-padding-img site-header-image">
                <div class="container">
                    <div class="row">

                        <div class="col-lg-10 col-12 header-info">
                            <h3>
                                <span class="d-block text-primary">Filière de Licence Fondamentale</span>
                                <span class="d-block text-dark">Ingénierie des Systèmes d’Information</span>
                                 <span class="d-block text-dark">S1 / S2 / S3 / S4 / S5 / S6</span>
                            </h3>
                        </div>
                    </div>
                </div>
               <div class="container">
                 <img src="images/TD ET TP EXAM/about.png" class="header-image img-fluid" alt="">
               </div>
                
            </header>

    <section class="products section-padding">
        <div class="container">
            <div class="row">
                <!-- الفصل الأول -->
                <div class="col-12 mb-4">
                    <h3 class="text-primary mb-4">Semestre 1</h3>
                    <div class="product-thumb">
                        <div class="product-info d-flex">
                            <div class="w-100">
                                <?php
                                $semester1_courses = [
                                    'M11. Analyse 1',
                                    'M12. Algèbre 1',
                                    'M13. Mécanique du Point',
                                    'M14. Thermodynamique',
                                    'M15. Informatique 1:Algorithmique 1/Python',
                                    'M16. Langues Etrangères 1',
                                    'M17. Méthodologie de Travail Universitaire'
                                ];

                                foreach ($semester1_courses as $course) {
                                    echo "<div class='course-block mb-4'>";
                                    echo "<h5 class='course-title'>$course</h5>";
                                    
                                    // استرجاع محتوى المقرر من قاعدة البيانات
                                    $stmt = $pdo->prepare("SELECT cc.*, am.module_code 
                                        FROM course_content cc 
                                        INNER JOIN assigned_modules am ON cc.module_id = am.id 
                                        WHERE am.module_code = ? OR am.module_name = ? 
                                        ORDER BY cc.content_type");
                                        
                                    $module_code = explode('. ', $course)[0];
                                    $module_name = explode('. ', $course)[1];
                                    $stmt->execute([$module_code, $module_name]);
                                    $resources = $stmt->fetchAll();

                                    echo "<div class='resource-buttons'>";
                                    // تنظيم المحتوى حسب النوع
                                    foreach ($resources as $resource) {
                                        switch ($resource['content_type']) {
                                            case 'course':
                                                echo "<a href='" . e($resource['file_path']) . "' class='btn btn-sm btn-primary me-2'>
                                                    <i class='bi bi-file-earmark-text'></i> درس " . e($resource['title']) . "
                                                </a>";
                                                break;
                                            case 'exercise':
                                                echo "<a href='" . e($resource['file_path']) . "' class='btn btn-sm btn-success me-2'>
                                                    <i class='bi bi-journal-check'></i> تمرين " . e($resource['title']) . "
                                                </a>";
                                                break;
                                            case 'exam':
                                                echo "<a href='" . e($resource['file_path']) . "' class='btn btn-sm btn-danger me-2'>
                                                    <i class='bi bi-file-text'></i> امتحان " . e($resource['title']) . "
                                                </a>";
                                                break;
                                        }
                                    }
                                    echo "</div>";
                                    echo "</div>";
                                    echo "<hr>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- كرر نفس البنية للفصول من 2 إلى 6 مع تغيير المقررات المناسبة -->
            </div>
        </div>
    </section>

    <footer>
      <div class="footer_section layout_paddin">
         <div class="container">
            <div class="footer_section_2">
               <div class="row">
                  <div class="col-lg-3 margin_top">
                     <div class="call_text"><a href="#"><img src="images/media/call-icon1.png"><span class="padding_left_15">  +212 535 21 19 76</span></a></div>
                     <div class="call_text"><a href="#"><img src="images/media/call-icon1.png"><span class="padding_left_15">  +212 5 35 21 19 78</span></a></div>
                     <div class="call_text"><a href="#"><img src="images/media/mail-icon1.png"><span class="padding_left_15"> support.fpt@usmba.ac.ma</span></a></div>
                  </div>
                  <div class="col-lg-3">
                     <div class="information_main">
                        <h4 class="information_text">Informations</h4>
                        <p class="many_text">Faculté Polydisciplinaire de Taza, Université Sidi Mohamed Ben Abdallah</p>
                     </div>
                  </div>
                  <div class="col-lg-3 col-md-6">
                     <div class="information_main">
                        <h4 class="information_text">liens utiles</h4>
                        <div class="footer_menu">
                           <ul>
                              <li><a href="index.html">Accueil</a></li>
                              <li><a href="td e tp.html">Cours</a></li>
                              <li><a href="td e tp.html">TD et TP</a></li>
                              <li><a href="td e tp.html">Examen</a></li>
                              <li><a href="contact.html">Contact</a></li>
                           </ul>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-3">
                     <div class="information_main">
                        <div class="footer_logo"><a href="index.html">espace Etudiant</a></div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- footer section end -->
      <!-- copyright section start -->
      <div class="copyright_section">
         <div class="container">
            <p class="copyright_text">© 202(5)   <a href="#">SAID el KHOUDAR</a></p>
         </div>
      </div>
      <!-- copyright section end --> 
    </footer>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/custom.js"></script>
</body>
</html>