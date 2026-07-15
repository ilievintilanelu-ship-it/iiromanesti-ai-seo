<?php

define('_PS_VERSION_', '1.7.8.11');

require_once dirname(__DIR__) . '/classes/StoreProfileInterface.php';
require_once dirname(__DIR__) . '/classes/IiromanestiRoProfile.php';
require_once dirname(__DIR__) . '/classes/PromptBuilder.php';

$profile = new IiromanestiAiSeoIiromanestiRoProfile();
$builder = new IiromanestiAiSeoPromptBuilder();

function assertContainsText($needle, $haystack, $message) {
    if (strpos($haystack, $needle) === false) {
        fwrite(STDERR, $message . "\n");
        exit(1);
    }
}

$cases = array(
    'produs de dama' => array('name' => 'Ie dama', 'features' => array(array('name' => 'Gen', 'value' => 'Dama'))),
    'produs de barbati' => array('name' => 'Camasa barbati', 'features' => array(array('name' => 'Gen', 'value' => 'Barbati'))),
    'produs pentru copii' => array('name' => 'Ie copii', 'features' => array(array('name' => 'Gen', 'value' => 'Copii'))),
    'set pentru cuplu' => array('name' => 'Set cuplu', 'features' => array(array('name' => 'Tip produs', 'value' => 'Set'))),
    'produs cu informatii complete' => array('name' => 'Ie dama bumbac alb', 'features' => array(array('name' => 'Material', 'value' => 'Bumbac'), array('name' => 'Culoare', 'value' => 'Alb'))),
    'produs cu material lipsa' => array('name' => 'Ie traditionala', 'features' => array()),
    'produs fara marimi' => array('name' => 'Camasa traditionala', 'combinations' => array()),
);

foreach ($cases as $label => $context) {
    $prompt = $builder->buildProductPrompt($profile, $context, array('product_name', 'long_description_html'), array('case' => $label));
    assertContainsText('PROFIL ACTIV: iiromanesti.ro', $prompt, 'Promptul nu foloseste profilul corect pentru ' . $label);
    assertContainsText('nu inventa', $prompt, 'Promptul nu previne inventarea datelor pentru ' . $label);
    assertContainsText('Nu modifica produsul', $prompt, 'Promptul nu blocheaza modificarea fara confirmare pentru ' . $label);
}

$rules = $profile->getRules();
foreach (array('Identitate', 'Denumire produs', 'Descriere scurta', 'Descriere lunga', 'Meta si etichete', 'Imagini', 'Caracteristici', 'Conversie') as $section) {
    if (!isset($rules[$section])) {
        fwrite(STDERR, 'Sectiune lipsa in profil: ' . $section . "\n");
        exit(1);
    }
}

if ($profile->getVersion() !== '1.0.0' || count($profile->getVersionHistory()) < 1) {
    fwrite(STDERR, "Versionarea profilului este invalida.\n");
    exit(1);
}

echo "Profile and prompt static tests passed.\n";
