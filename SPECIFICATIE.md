# Specificatie - Profiluri AI iiRomanesti

## Scop

Sistemul de profiluri AI standardizeaza continutul generat pentru modulul iiRomanesti AI SEO fara reguli hardcodate in generator sau in Prompt Builder.

## Profil implementat

Este implementat doar profilul `iiromanesti_ro`, pentru magazinul `iiromanesti.ro`, versiunea `1.0.0`, cu limba romana fara diacritice.

## Componente

- `StoreProfileInterface`: contractul comun pentru profiluri.
- `StoreProfileManager`: registru si selector de profil activ pentru context multistore/multilang.
- `IiromanestiRoProfile`: profil predefinit pentru iiromanesti.ro.
- `PromptBuilder`: construieste promptul final din reguli de siguranta, profil, date reale, campuri selectate, audit si schema JSON.
- `AdminIiromanestiAiSeoProfilesController`: pagina Back Office de vizualizare profil.

## Reguli functionale

Profilul impune reguli pentru identitate, denumire produs, descrieri HTML, meta title, meta description, etichete, imagini, caracteristici si conversie. Materialul, broderia, originea, metoda de fabricatie, marimile, livrarea, stocul si calitatea sunt folosite numai cand exista date confirmate in context.

## Versionare

Profilul expune versiunea curenta, data ultimei modificari si istoricul modificarilor. Structura permite revenirea la versiuni anterioare cand profilurile vor deveni editabile.

## Limitari intentionate

Nu sunt implementate profilurile pentru alte magazine, invatarea automata din modificarile utilizatorului, procesarea in masa, editorul complet de profil si sincronizarea externa.
