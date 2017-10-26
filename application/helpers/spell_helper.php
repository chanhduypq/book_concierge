<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function spellCheckWord($word) {
	if (!function_exists('pspell_new')) return $word;
	
    $pspell = pspell_new('en','','','utf-8',PSPELL_BAD_SPELLERS);	
	
    $autocorrect = TRUE;

    // Take the string match from preg_replace_callback's array
    $word = $word[0];
   
    // Ignore ALL CAPS
    if (preg_match('/^[A-Z]*$/',$word)) return $word;

    // Return dictionary words
    if (pspell_check($pspell,$word))
        return $word;

    // Auto-correct with the first suggestion, color green
    if ($autocorrect && $suggestions = pspell_suggest($pspell,$word))
        return current($suggestions);
   
    // No suggestions, color red
    return $word;
}

function spellCheck($string) {
	if (!function_exists('pspell_new')) return $string;
	
    return preg_replace_callback('/\b\w+\b/','spellCheckWord',$string);
}