<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false) {
    header("Location: index.php");
    exit;
}

if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
    header("Location: admin.php");
    exit;
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>WEBTE FINAL</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-image: url('design/background4.jpg'); 
            background-size: cover; 
            background-position: center center; 
            background-attachment: fixed;
            color: #B0A8B9; 
            font-family: "Chakra Petch", sans-serif; 
            font-weight: 600; 
        }
        .navbar {
            background-color: #320636;
            padding: 10px 15px;
            font-size: 18px; 
            font-weight: 700; 
            text-transform: uppercase; 
            border-bottom: 3px solid #564366; 
        }
        a, .btn {
            color: #D8BFD8; 
            transition: color 0.3s; 
        }
        a:hover, .btn:hover {
            color: #ffffff;
        }
        .btn {
            border-color: #C996CC;
            background-color: transparent;
            padding: 8px 12px;
        }

        .container {
            border: 3px solid #564366;
            border-radius: 30px; 
            padding: 35px; 
            margin-top: 25px; 
            background-color: #320636;
            box-shadow: 0 16px 32px rgba(0,0,0,0.5); 
        }
        h2 {
            border-bottom: 5px solid #564366; 
            border-top: 5px solid #564366; 
            color: #e0cee0;
            border-radius: 25px;
            padding-top: 5px;
            padding-bottom: 5px;
            margin-bottom: 25px;
        }

        .container li {
            font-size: 15px; 
            line-height: 1.8; 
            margin-bottom: 2px;
        }

        .nav-link i {
            margin-right: 5px; 
            font-size: 1.5rem; 
        }
        #toast-container > .toast-success {
        background-image: none !important;
        background-color: #28a745 !important; 
        }

        #toast-container > .toast-error {
            background-image: none !important;
            background-color: #dc3545 !important; 
        }

    </style>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">WEBTE FINAL</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="find_question.php">Find question</a></li>
                <li class="nav-item"><a class="nav-link" href="add_question_user.php">Add question</a></li>
                <li class="nav-item"><a class="nav-link" href=<?php echo "view_questions_user.php?user_id=" . $_SESSION["user_id"] ?>>My questions</a></li>
                <li class="nav-item"><a class="nav-link" href="q&a_to_csv.php">Export Q&A</a></li>
                <li class="nav-item"><a class="nav-link" href="manual_to_pdf.php" target="_blank">Export Manual</a></li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link">
                        <i class="fa fa-user"></i> <?php echo $_SESSION["username"]; ?>
                    </a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href=<?php echo "change_password.php?user_id=" . $_SESSION["user_id"]; ?>>
                    <i class="fa-solid fa-key"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>
            </li>
            </ul>
        </div>
    </div>
</nav>



    <div class="container mt-3" id="manual">
        
        <div class="row">

        <div class="col-md-4">
            <h2 class="text-center">Neprihlásený používateľ</h2>
            <ul>
                <li>Dokáže sa registrovať a prihlásiť do systému po vykliknutí daného linku v navigácií</li>
                <li>Vie vyhladať otázku 3 spôsobmi a to naskenovaním QR kódu, vyhľadaním cez URL alebo zadaním 5-miestneho kódu</li>
                <li>Vie exportovať príručku do PDF</li>
            </ul>
        </div>


        <div class="col-md-4">
            <h2 class="text-center">Prihlásený používateľ</h2>
            <ul>
                <li>Vie vyhladať otázku 3 spôsobmi a to naskenovaním QR kódu, vyhľadaním cez URL alebo zadaním 5-miestneho kódu</li>
                <li>Dokáže pridať 2 typy otázok:
                    <ul>
                        <li>Otázka s výberom odpovedí, vie zadať 4 rôzne možnosti a označiť, ktoré z nich sú správne</li>
                        <li>Otvorená otázka, vie zadať aký typ vyhodnocovania bude otázka obsahovať</li>
                    </ul>
                </li>
                <li>Vie si zobraziť všetky vlastné otázky a vie nad nimi robiť tieto operácie:
                    <ul>
                        <li>Editácia</li>
                        <li>Duplikovanie</li>
                        <li>Vymazanie</li>
                        <li>Ukončiť hlasovanie</li>
                    </ul>
                </li>
                <li>Vie si exportovať vlastné otázky do CSV súboru</li>
                <li>Vie exportovať príručku do PDF</li>
                <li>Vie si zmeniť heslo</li>
                <li>Odhlásiť sa</li>
            </ul>
        </div>

        <div class="col-md-4">
            <h2 class="text-center">Admin</h2>
            <ul>
                <li>Vie vyhladať otázku 3 spôsobmi a to naskenovaním QR kódu, vyhľadaním cez URL alebo zadaním 5-miestneho kódu</li>
                <li>Dokáže špecifikovať v koho mene vytvára otázku</li>
                <li>Dokáže pridať 2 typy otázok:
                    <ul>
                        <li>Otázka s výberom odpovedí, vie zadať 4 rôzne možnosti a označiť, ktoré z nich sú správne</li>
                        <li>Otvorená otázka, vie zadať aký typ vyhodnocovania bude otázka obsahovať</li>
                    </ul>
                </li>
                <li>Vie si zobraziť všetkých používateľov a robiť nad nimi tieto operácie:
                    <ul>
                        <li>Zobrazenie všetkých otázok daného používateľa</li>
                        <li>Editácia daného používateľa</li>
                        <li>Vymazanie daného používateľa</li>
                        <li>Zmena hesla danému používateľovi</li>
                        <li>Zmeniť status na admina danému používateľovi</li>
                    </ul>
                </li>
                <li>Vie pridávať užívateľov</li>
                <li>Vie exportovať príručku do PDF</li>
                <li>Vie si exportovať všetky otázky všetkých používateľov do CSV súboru</li>
                <li>Odhlásiť sa</li>
            </ul>
        </div>
    </div>
</div>
    <script src="script.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        // toastr nastavenia
        toastr.options = {
            "positionClass": "toast-bottom-right", // tu sa meni pozicia toastr
        };

        <?php if (isset($_SESSION["toast_success"])) : ?>
            toastr.success('<?php echo $_SESSION["toast_success"]; ?>');

            <?php unset($_SESSION["toast_success"]); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION["toast_error"])) : ?>
            toastr.error('<?php echo $_SESSION["toast_error"]; ?>');

            <?php unset($_SESSION["toast_error"]); ?>
        <?php endif; ?>
    </script>
</body>

</html>