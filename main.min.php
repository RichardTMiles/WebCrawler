<?php START:
require_once 'vendor/autoload.php';
if (file_put_contents('cnn.html', $data = ($selenium = Facebook\WebDriver\Remote\RemoteWebDriver::create('http://localhost:44444/wd/hub', ['platform' => 'Mac OS X', 'browserName' => 'firefox', 'version' => 'latest'], 60 * 1000, 60 * 1000)->get('https://www.cnn.com'))->getPageSource()) && empty($selenium->quit()) && preg_match('/Tech\s[\n\s\w\t:|.,?()$#%\-\']+(?=\[)/', $cnn = `lynx -accept_all_cookies -dump cnn.html`, $tech) && preg_match('/Health\s[\n\s\w\t:|.,?()$#%\-*\'\[\]]{40,}(?=(Tech)|(Entertainment))/', $cnn, $health) && preg_match('/Tech\s[\n\s\w\t:|.,?()$#%\-\']{40,}(?=(CNN Travel ))/', $cnn, $travel) && !empty($health) && !empty($tech) && !empty($travel)) {
    foreach ($matches = array_reverse(array_values(array_unique(explode("\n", $matches = $health[0] . $tech[0] . $travel[0])))) as $title) {
        print (strlen($title = trim($title)) > 6 && !strpos($title, 'CNN') && substr($title, -3) !== '...' && (($title[0] === '[' && ($title = substr($title, 5))) || 1) ? $title . PHP_EOL : '');
    }
} else {
    goto START;
}

// This script is functional and contains three semicolons.. aka can be made into 3 lines (all I wrote). If someone beats this you should show it in class please
// But really brackets are not required in this document. So with a semicolon per line It could have only been 3 lines.
// I stick to following my PSR-2 standards though..

