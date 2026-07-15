# iiRomanesti AI SEO

Modul PrestaShop 1.7.8.11 pentru fundatia tehnica a viitoarelor functionalitati SEO asistate de AI.

## Functionalitati incluse in versiunea 0.1.0

- Structura standard de modul PrestaShop.
- Instalare si dezinstalare cu valori implicite de configurare.
- Meniu principal **iiRomanesti AI** in Back Office.
- Pagina Dashboard cu statistici citite direct din baza de date PrestaShop.
- Pagina de configurare in Back Office.
- Campuri pentru activare modul, furnizor AI, model AI, cheia API, timeout, nivel jurnal, perioada de pastrare a jurnalelor si numarul maxim de versiuni istoric.
- Validari explicite pentru toate campurile.
- Salvarea cheii API in configurarea PrestaShop, fara afisare in formular si fara scriere in jurnale.
- Test de conexiune API catre endpointul de modele OpenAI, fara generare de continut.
- Jurnalizare de baza pentru instalare, dezinstalare, salvare configurare, test conexiune si erori.
- Compatibilitate multistore prin API-ul `Configuration` PrestaShop si compatibilitate multilang prin helper-ul standard de formular.
- Protectie CSRF prin tokenul `AdminModules` si verificare de permisiune pentru angajatul Back Office.

## Instalare

1. Copiaza directorul `iiromanesti_ai_seo` in directorul `modules/` al magazinului PrestaShop.
2. In Back Office, mergi la **Modules > Module Manager**.
3. Cauta **iiRomanesti AI SEO**.
4. Apasa **Install**.
5. Deschide pagina de configurare a modulului.

## Configurare

Completeaza urmatoarele campuri:

- **Activare modul**: activeaza sau dezactiveaza modulul.
- **Furnizor AI**: in aceasta etapa este disponibil doar OpenAI.
- **Model AI**: numele modelului folosit pentru testul de conexiune.
- **Cheia API**: cheia furnizorului AI. Daca lasi campul gol la salvare, cheia existenta ramane neschimbata.
- **Timeout API**: interval intre 5 si 120 secunde.
- **Nivel jurnal**: `error`, `info` sau `debug`.
- **Perioada pastrare jurnale**: interval intre 1 si 365 zile.
- **Numar maxim versiuni istoric**: interval intre 1 si 100.


## Build pachet ZIP

Arhiva ZIP nu se versionaza in repository. Pastreaza in Git doar codul sursa si genereaza pachetul de instalare local, atunci cand ai nevoie de distributie.

### Windows

```powershell
./build.ps1
```

### Linux / macOS

```bash
./build.sh
```

Ambele scripturi creeaza automat arhiva:

```text
dist/iiromanesti_ai_seo-v0.1.0.zip
```

Nu adauga arhive `.zip` in commituri. Fisierul generat este ignorat de Git.

## Testare manuala

1. Instaleaza modulul din Back Office.
2. Verifica meniul **iiRomanesti AI > Dashboard** si cardurile pentru produse, categorii si campuri SEO lipsa.
3. Salveaza configurarea cu valori valide.
4. Verifica mesajul de succes in limba romana.
5. Introdu valori invalide pentru timeout, perioada de pastrare sau istoric si verifica mesajele de eroare.
6. Introdu o cheie API valida si apasa **Testeaza conexiunea API**.
7. Verifica jurnalele PrestaShop pentru evenimentele modulului si confirma ca cheia API nu apare in loguri.
8. Dezinstaleaza modulul si confirma stergerea configurarilor si a meniului Back Office.

## Ce nu este inclus inca

- Butoane de generare SEO in pagina produsului.
- Modificari asupra produselor.
- Prompturi functionale de generare continut.
- Audit Google.
- Audit de conversie.
- Curatare automata efectiva a jurnalelor vechi.
- Management al istoricului de versiuni SEO.

## Verificari recomandate pentru dezvoltatori

```bash
php -l iiromanesti_ai_seo.php
php -l controllers/admin/AdminIiromanestiAiSeoController.php
php -l controllers/admin/AdminIiromanestiAiSeoDashboardController.php
php -l classes/Logger.php
php -l classes/ApiClient.php
```
