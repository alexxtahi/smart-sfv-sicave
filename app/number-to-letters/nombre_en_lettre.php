<?php

/****************************************************************************
*   About       :   Convertit jusqu'à  999 999 999 999 999 (billion)        *
*                   avec respect des accords                                *
*_________________________________________________________________________  *			
*               Transposed from JS to PHP and optimised by                  *
*                 Hamza BENDALI BRAHAM <hbendali@ya.ru>                     *
*       Inspired from GALA OUSSE Brice, nombre_en_lettre.js project         *
*           Github: https://github.com/luxigo/number-to-letters             *
*****************************************************************************
*/

function NumberToLetter($number,$separateur=","){
    $convert = explode($separateur, $number);
    $num[17] = array('zero', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit',
                     'neuf', 'dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize');
                      
    $num[100] = array(20 => 'vingt', 30 => 'trente', 40 => 'quarante', 50 => 'cinquante',
                      60 => 'soixante', 70 => 'soixante dix', 80 => 'quatre vingts', 90 => 'quatre vingt dix');
                                      
    if (isset($convert[1]) && $convert[1] != '') {
      return NumberToLetter($convert[0]).' et '.NumberToLetter($convert[1]);
    }
    if ($number < 0) return 'moins '.NumberToLetter(-$number);
    if ($number < 17) {
      return $num[17][$number];
    }
    elseif ($number < 20) {
      return 'dix '.NumberToLetter($number-10);
    }
    elseif ($number < 100) {
      if ($number%10 == 0) {
        return $num[100][$number];
      }
      elseif (substr($number, -1) == 1) {
        if( ((int)($number/10)*10)<70 ){
          return NumberToLetter((int)($number/10)*10).' et un';
        }
        elseif ($number == 71) {
          return 'soixante et onze';
        }
        elseif ($number == 81) {
          return 'quatre vingt un';
        }
        elseif ($number == 91) {
          return 'quatre vingt onze';
        }
      }
      elseif ($number < 70) {
        return NumberToLetter($number-$number%10).' '.NumberToLetter($number%10);
      }
      elseif ($number < 80) {
        return NumberToLetter(60).' '.NumberToLetter($number%20);
      }
      else {
        return NumberToLetter(80).' '.NumberToLetter($number%20);
      }
    }
    elseif ($number == 100) {
      return 'cent';
    }
    elseif ($number < 200) {
      return NumberToLetter(100).' '.NumberToLetter($number%100);
    }
    elseif ($number < 1000) {
      return NumberToLetter((int)($number/100)).' '.NumberToLetter(100).($number%100 > 0 ? ' '.NumberToLetter($number%100): '');
    }
    elseif ($number == 1000){
      return 'mille';
    }
    elseif ($number < 2000) {
      return NumberToLetter(1000).' '.NumberToLetter($number%1000).' ';
    }
    elseif ($number < 1000000) {
      return NumberToLetter((int)($number/1000)).' '.NumberToLetter(1000).($number%1000 > 0 ? ' '.NumberToLetter($number%1000): '');
    }
    elseif ($number == 1000000) {
      return 'un million';
    }
    elseif ($number < 2000000) {
      return NumberToLetter(1000000).' '.NumberToLetter($number%1000000);
    }
    elseif ($number < 1000000000) {
      return NumberToLetter((int)($number/1000000)).' million'.($number%1000000 > 0 ? ' '.NumberToLetter($number%1000000): '');
    }
}//-----------------------------------------------------------------------