<li?php include "../.configFinal.php" ; session_start(); if (isset($_SESSION["loggedin"]) &&
    $_SESSION["loggedin"]===true) { if (isset($_SESSION["admin"]) && $_SESSION["admin"]===true) { header("Location:
    admin.php"); exit; } else { header("Location: logged_in.php"); exit; } } ?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>WEBTE FINAL</title>
        <link rel="stylesheet" href="style.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    </head>

    <body>
        <div class="navigation_bar">
            <div class="navbar">
                <a href="find_question.php">Find question</a>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            </div>
        </div>
        <div class="manual">
            <h2>Neprihlásený používateľ</h2>
            <ul>
                <li>Sa dokáže registrovať sa a prihlásiť do systému po vykliknutí daného linku v navigácií</li>
                <li>Vie vyhladať otázku 3. spôsobmi a to naskenovaním QR kódu, vyhľadaním cez URL alebo zadaním
                    5-miestneho kódu</li>
                <li>Vie exportovať príručku do PDF</li>
            </ul>
            <h2>Prihlásený používateľ</h2>
            <ul>
                <li>Vie vyhladať otázku 3. spôsobmi a to naskenovaním QR kódu, vyhľadaním cez URL alebo zadaním
                    5-miestneho kódu</li>
                <li>Dokáže pridať 2 typy otázok:
                    <ul>
                        <li>Otázka s výberom odpovedí, vie zadať 4 rôzne možnosti a označiť, ktoré z nich sú správne
                        </li>
                        <li>Otvorená otázka, vie zadať aký typ vyhodnocovania bude otázka obsahovať</li>
                    </ul>
                </li>
                <li>Vie si zobraziť všetky vlastné otázky a vie nad nimi robiť tieto operácie
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
            <h2>Admin</h2>
            <ul>
                <li>Vie vyhladať otázku 3. spôsobmi a to naskenovaním QR kódu, vyhľadaním cez URL alebo zadaním
                    5-miestneho kódu</li>
                    <li>Dokáže špecifikovať v koho mene vytvára otázku</li>   
                <li>Dokáže pridať 2 typy otázok:
                    <ul>
                        <li>Otázka s výberom odpovedí, vie zadať 4 rôzne možnosti a označiť, ktoré z nich sú správne
                        </li>
                        <li>Otvorená otázka, vie zadať aký typ vyhodnocovania bude otázka obsahovať</li>
                    </ul>
                </li>
                <li>Vie si zobraziť všetkých používateľov a robiť nad nimi tieto operácie
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


        <script src="script.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
        <script>
            // toastr nastavenia
            toastr.options = {
                "positionClass": "toast-top-right", // tu sa meni pozicia toastr
            };

            <?php if (isset($_SESSION["toast_success"])): ?>
                toastr.success('<?php echo $_SESSION["toast_success"]; ?>');

                <?php unset($_SESSION["toast_success"]); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION["toast_error"])): ?>
                toastr.error('<?php echo $_SESSION["toast_error"]; ?>');

                <?php unset($_SESSION["toast_error"]); ?>
            <?php endif; ?>
        </script>
    </body>

    </html>