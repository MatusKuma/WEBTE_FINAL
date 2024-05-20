Projekt sa zameriava na vytvorenie responzívnej webovej aplikácie s integrovanou používateľskou príručkou a rozličnými úrovňami používateľských rolí: neprihlásený používateľ, prihlásený používateľ a administrátor. Každá rola má prístup k odlišným funkcionalitám aplikácie, čo je detailne vysvetlené v príručke, ktorú je možné exportovať do PDF.

Aplikácia podporuje vytváranie videa, ktoré dokumentuje jej funkčnosť. Ak niektorá funkcia nie je na videu prezentovaná, považuje sa za nerealizovanú. Hlavná stránka umožňuje prihlásenie a vstup pomocou kódu pre zobrazenie hlasovacej otázky. Neprihlásený používateľ môže pristupovať k hlasovacej otázke buď skenovaním QR kódu alebo zadáním vstupného kódu. Po odpovedi je presmerovaný na stránku zobrazujúcu výsledky, odkiaľ sa môže vrátiť na hlavnú stránku.

Prihlásený používateľ má možnosť meniť svoje heslo, definovať nové hlasovacie otázky a nastaviť ich ako aktívne. Môže tiež generovať QR kódy a jedinečné kódy pre každú otázku, pričom má na výber z dvoch typov otázok: s výberom správnej odpovede alebo s otvorenou odpoveďou. Výsledky hlasovania môžu byť zobrazené buď ako nečíslovaný zoznam alebo vo forme "word cloudu".

Administrátor má rovnaké právomoci ako prihlásený používateľ, ale navyše môže spravovať otázky všetkých používateľov a má možnosť filtrovať otázky podľa predmetu a dátumu vytvorenia. Môže tiež meniť role ostatných používateľov a spravovať ich údaje.

Využité prostriedky:

Vendor/qrcode: Táto knižnica bola použitá na generovanie QR kódov, ktoré umožňujú jednoduché a rýchle skenovanie informácií o hlasovacích otázkach.

FPDF: Pomocou tejto knižnice bolo implementované exportovanie používateľskej príručky do PDF formátu. Toto riešenie zabezpečuje, že príručka je ľahko dostupná a prenosná v tlačenej forme.

DataTables: Knižnica DataTables bola použitá pre pokročilé funkcie pri zobrazovaní tabuliek, ako sú vyhľadávanie, stránkovanie a zoraďovanie dát, čím sa zlepšuje používateľská interaktivita so stránkou.

Toastr knižnica: Na zobrazovanie notifikácií a hlášok používateľom sa využíva knižnica Toastr, ktorá umožňuje elegantné a efektívne informovanie používateľov o rôznych akciách alebo chybách.

jQuery: Tento framework bol využitý pre zjednodušenie manipulácie s DOM, spracovanie udalostí, animácie a ajaxové volania, zvyšujúci efektivitu a čitateľnosť kódu.

Docker: Aplikácia bola kontajnerizovaná pomocou Dockeru, čo zjednodušuje nasadenie a zaručuje konzistenciu prostredia medzi vývojom a produkciou.

Bootstrap: Tento front-end framework bol použitý pre dizajn a zabezpečenie responzivity stránky, vďaka čomu je aplikácia prístupná a použiteľná na rôznych zariadeniach a rozlíšeniach.

Verzionovací systém GitHub: Pre správu verzií a spoluprácu na projekte bol použitý GitHub, čo umožnilo efektívnu kooperáciu a sledovanie zmien v projekte.
