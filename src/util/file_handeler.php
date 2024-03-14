<?php

/* 
    Process an HTML file by converting {{}} to PHP tags.
    Returns the converted content.

    /: The forward slashes / mark the beginning and end of the regular expression pattern.
    \s*: This matches zero or more whitespace characters.
    \(: This matches the opening parenthesis ( character.
    \s*: This matches zero or more whitespace characters.
    (.*?): This is a capturing group ((.*?)) that matches any character (.) zero or more times (*), but as few times as possible due to the lazy quantifier ?. This captures the content inside the parentheses after @if.
    [^)]+: This matches one or more characters that are not a closing parenthesis ).
    \s*: This matches zero or more whitespace characters.
    \)\s*: This matches the closing parenthesis ) followed by zero or more whitespace characters.
    [^}@]: This matches any character that is not a closing curly brace } or the @ symbol.
    }: This matches the closing curly brace } character.
    \n*: This matches zero or more newline characters.
    @: This matches the literal @ symbol.
*/
$patterns = array(
    '/<!--(.*?)--!>/' => " ", // delete all the connected content in html
    '/{{(.*?)}}/' => "<?php $1 ?>", // Define patterns for {{}}
    '/@if\s*\((.*)\)\s*\n*/' => "<?php if(\$1): ?> \n", // Define the pattern for matching @if(condition) blocks
    '/@endif/s' => "<?php endif; ?>", // Define the pattern for matching @if(condition) { ... }@ blocks
    '/@elseif\s*\((.*)\)\s*\n*/' => "<?php elseif(\$1): ?> \n", // Define the pattern for matching @elseif(condition) { ... }@ blocks
    '/@else(?!\w)/' => "<?php else: ?> \n", // Define the pattern for matching @else { ... }@ blocks

    '/@switch\s*\(\s*(.*?)\s*\)\s*\n*\s*@case\s*(.*?)\s*\n/' => "<?php switch (\$1): \n case \$2: ?>",
    '/@case\s*(.*?)\s*\n/' => "<?php case \$1 : ?>",
    '/@break\s*\n/' => "<?php break; ?>\n",
    '/@default\s*\n/' => "<?php default: ?>\n",

    '/@endforeach(?!\w)/' => "<?php endforeach; ?>",
    '/@endfor(?!\w)/' => "<?php endfor; ?>",
    '/@endswitch(?!\w)/' => "<?php endswitch; ?>",
    '/@endwhile(?!\w)/' => "<?php endwhile; ?>",
    
    '/@(.*?)\s*\((.*?)\)\s*\n/' => "<?php \$1(\$2): ?>\n", // standalone dunction
    '/@(.*?)\s*\n/' => "<?php \$1; ?>",

    // "while" => "/@while\((.*?)\) {(.+?)}@/s", // Define the pattern for matching @while(condition) { ... }@ blocks
    // "for" => "/@for\((.*?);(.*?);(.*?)\) {(.+?)}@/s", // Define the pattern for matching @for(init; condition; increment) { ... }@ blocks
    // "switch" => "/@switch\((.*?)\) {(.+?)}@/s", // Define the pattern for matching @switch(expression) { ... }@ blocks
);


function processHTMLFile($filePath)
{
    global $patterns;

    $content = file_get_contents($filePath);

    // Loop through each pattern and convert the brackets to PHP syntax
    foreach ($patterns as $pattern => $replacement) {
        $content = convertToPHP($pattern, $replacement, $content);
    }

    return $content;
}

/* 
This File has function of all the files related task

*/

/**
 * Convert HTML Files to PHP 
 * @param mixed $pattern takes pattern example - '/{{(.*?)}}/'
 * @param mixed $replacement - replacement for the pattern - "<?php $1 ?>"
 * @param mixed $input - the string you want to change the file
 */
function convertToPHP($pattern, $replacement, &$input)
{
    $output = preg_replace($pattern, $replacement, $input);
    return $output;
}

/**
 * Creates a folder/directory if it doesn't exist already.
 *
 * @param string $directory The path of the folder to be created.
 * @return void
 */
function createFolder($directory)
{
    $folders = explode('/', $directory);
    $path = '';
    foreach ($folders as $folder) {
        $path .= $folder . '/';
        if (!is_dir($path)) {
            mkdir($path, 0777);
        }
    }
}