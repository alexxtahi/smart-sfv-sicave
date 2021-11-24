# NombreEnLettres

### JS PHP Lib, Convertisseur de nombres et chiffre en lettres
Pour convertir et écrire les nombres en français en toutes lettres à partir de nombres écrits en chiffres en base 10.

Ce convertisseur de chiffres en lettres est pratique pour écrire les nombres en lettre et les chiffres en Français ou pour vérifier l'orthographe d'un nombre.

## JavaScript
```js
var nombre_en_lettre = require('./nombre_en_lettre.js');

console.log(nombre_en_lettre("12 236.24"));
// douze mille deux cent trente-six virgule vingt-quatre

console.log(nombre_en_lettre("85454.98", "Dinnar", "Centimes"));
// quatre-vingt-cinq mille quatre cent cinquante-quatre Dinnar et quatre-vingt-dix-huit Centimes

```

## PHP
```php
include_once("nombre_en_lettre.php");

echo NumberToLetter("12 236.24") ."\r\n";
// douze mille deux cent trente-six virgule vingt-quatre

echo NumberToLetter("85454.98", "Dinnar", "Centimes") ."\r\n";
// quatre-vingt-cinq mille quatre cent cinquante-quatre Dinnar et quatre-vingt-dix-huit Centimes

```