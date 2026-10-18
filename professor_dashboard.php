<?php
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'professor') {
    header("Location: sign-in.html");
    exit();
}

$email = $_SESSION['email'];
$name_parts = explode('.', explode('@', strtolower($email))[0]);
$name = implode(' ', array_map('ucfirst', $name_parts));
$professor_id = $_SESSION['user_id'];

// Récupération des modules assignés
$stmt = $pdo->prepare("SELECT * FROM assigned_modules WHERE professor_id = ?");
$stmt->execute([$professor_id]);
$assigned_modules = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Espace Professeur</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;300;400;700;900&display=swap" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
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
                <?php foreach($assigned_modules as $module): ?>
                <div class="col-12 mb-4">
                    <div class="product-thumb">
                        <div class="btn btn-lg btn-white w-100 shadow-sm py-2 mb-3">
                            <h4 class="mb-0"><?php echo e($module['module_code'] . '. ' . $module['module_name']); ?></h4>
                        </div>
                        <div class="product-info">
                            <div class="row">
                                <!-- Gestion des cours -->
                                <div class="col-md-4 mb-3">
                                    <h5>Cours</h5>
                                    <button class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#uploadModal" data-type="course" data-module="<?php echo $module['id']; ?>">
                                        <i class="bi bi-plus-circle"></i> Ajouter un cours
                                    </button>
                                    <div class="resource-list">
                                        <?php
                                        $stmt = $pdo->prepare("SELECT * FROM course_content WHERE module_id = ? AND content_type = 'course' ORDER BY upload_date DESC");
                                        $stmt->execute([$module['id']]);
                                        $courses = $stmt->fetchAll();
                                        foreach($courses as $course) {
                                            echo "<div class='resource-item'>";
                                            echo "<div class='d-flex justify-content-between align-items-center'>";
                                            echo "<a href='" . e($course['file_path']) . "' class='btn btn-sm btn-outline-primary mb-1'><i class='bi bi-file-earmark-text'></i> " . e($course['title']) . "</a>";
                                            echo "<button class='btn btn-sm btn-danger delete-content' data-id='" . (int) $course['id'] . "'><i class='bi bi-trash'></i></button>";
                                            echo "</div>";
                                            echo "</div>";
                                        }
                                        ?>
                                    </div>
                                </div>

                                <!-- Gestion des TD/TP -->
                                <div class="col-md-4 mb-3">
                                    <h5>TD/TP</h5>
                                    <button class="btn btn-success mb-2" data-bs-toggle="modal" data-bs-target="#uploadModal" data-type="exercise" data-module="<?php echo $module['id']; ?>">
                                        <i class="bi bi-plus-circle"></i> Ajouter un TD/TP
                                    </button>
                                    <div class="resource-list">
                                        <?php
                                        $stmt = $pdo->prepare("SELECT * FROM course_content WHERE module_id = ? AND content_type = 'exercise' ORDER BY upload_date DESC");
                                        $stmt->execute([$module['id']]);
                                        $exercises = $stmt->fetchAll();
                                        foreach($exercises as $exercise) {
                                            echo "<div class='resource-item'>";
                                            echo "<div class='d-flex justify-content-between align-items-center'>";
                                            echo "<a href='" . e($exercise['file_path']) . "' class='btn btn-sm btn-outline-success mb-1'><i class='bi bi-journal-check'></i> " . e($exercise['title']) . "</a>";
                                            echo "<button class='btn btn-sm btn-danger delete-content' data-id='" . (int) $exercise['id'] . "'><i class='bi bi-trash'></i></button>";
                                            echo "</div>";
                                            echo "</div>";
                                        }
                                        ?>
                                    </div>
                                </div>

                                <!-- Gestion des examens -->
                                <div class="col-md-4 mb-3">
                                    <h5>Examens</h5>
                                    <button class="btn btn-danger mb-2" data-bs-toggle="modal" data-bs-target="#uploadModal" data-type="exam" data-module="<?php echo $module['id']; ?>">
                                        <i class="bi bi-plus-circle"></i> Ajouter un examen
                                    </button>
                                    <div class="resource-list">
                                        <?php
                                        $stmt = $pdo->prepare("SELECT * FROM course_content WHERE module_id = ? AND content_type = 'exam' ORDER BY upload_date DESC");
                                        $stmt->execute([$module['id']]);
                                        $exams = $stmt->fetchAll();
                                        foreach($exams as $exam) {
                                            echo "<div class='resource-item'>";
                                            echo "<div class='d-flex justify-content-between align-items-center'>";
                                            echo "<a href='" . e($exam['file_path']) . "' class='btn btn-sm btn-outline-danger mb-1'><i class='bi bi-file-text'></i> " . e($exam['title']) . "</a>";
                                            echo "<button class='btn btn-sm btn-danger delete-content' data-id='" . (int) $exam['id'] . "'><i class='bi bi-trash'></i></button>";
                                            echo "</div>";
                                            echo "</div>";
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
               </div>
              </div>
            </section>

    <!-- Modal pour l'upload des fichiers -->
           <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter un contenu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="uploadForm" action="upload_content.php" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <input type="hidden" name="content_type" id="contentType">
                        <input type="hidden" name="module_id" id="moduleId">
                        <div class="mb-3">
                            <label class="form-label">Titre</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fichier</label>
                            <input type="file" class="form-control" name="file" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Envoyer</button>
                    </div>
                </form>
            </div>
        </div>
           </div>
        
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
                        <p class="many_text">hhhhhhhhh gdgg gfgdg gffdg ggffd gfd gfd gfd gfd gfsgfd fffff fff ffd fffd fdd  jjjjjjjjjjjjjjjjjjjjjjjjjjjjj hggf hggf hggff gff </p>
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
   
         <div class="copyright_section">
         <div class="container">
            <p class="copyright_text">© 202(5)   <a href="#">SAID el KHOUDAR</a></p>
         </div>
          </div>
                 <!-- copyright section end --> 
       </footer>           


    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.min.js"></script>
    <script src="js/professor-dashboard.js"></script>
</body>
</html>