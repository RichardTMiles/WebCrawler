<?php
/** Richard Miles 1-20-19
 *  11035527
 *
 *  Dependencies include PHP, Slenium , lynx, and Firefox + Geckodriver (you can change the browser in the code below)
 *  Brew is not required as it is a package manager for mac os x
 *
 * Slenium loads a page in the browser and captures full page HTML javascript and ajax have been loaded
 * Slenium is an open source tool you can install with
 *          >> brew install selenium-server-standalone
 *
 * Geckodriver is a dependency for using Slenium in firefox
 *          >> brew install geckodriver
 *
 * Lynx is a web browser run in terminal that will help output the webpage in DOM order without html tags
 *          >> brew install lynx
 *
 * Composer is a package manager and for php and is included in this package. I use facebook's open source
 * webdriver to connect all the peices above.
 *          >> composer require facebook/webdriver
 *
 * USAGE:
 * You must have the selenium-server running on port 44444 (adjustable below).
 *          >> selenium-server -port 44444
 *
 * In practice/field I would have added this using the backtick operator
 *          `selenium-server -port 44444 &`;
 *
 * The code above executes the server and sends it to the background.
 * However you'd hate me for taking up the memory and computing power.
 *
 * You can execute main.php or main.min.php by using the php executible
 *          >> php main.php
 *
 * A side note for anyone who reads this.. the command
 *          >> lsof -i TCP:44444
 *
 * would tell you the process active on the port 44444. Then using the process id
 * you could run the following to remove it.
 *          >> Kill -9 [process_id]
 *
 **/


START:  // for the goto operator

require_once('vendor/autoload.php');    // Composer dependencies - "facebook/webdriver": "^1.5"

/* Load a page in browser and capture full page after javascript and ajax loaded
 * CNN has the most excessive javascript / ajax requests, 15% of which point to nothing.
 * It can (at times) take over 60s to load with no guarantee that all request will be successful.
 * In the minified version I ignore timeout error using the @ operator. If data captured is not
 * the required Travel, Health, and Tec than start the script over again with goto.
 *
 * For completeness I choose to leave the try catch block in this document for reference porpoises.
 *  PHP is awesome.
 */

try {
    $selenium = Facebook\WebDriver\Remote\RemoteWebDriver::create("http://localhost:44444/wd/hub", ["platform" => "Mac OS X", "browserName" => "firefox", "version" => "latest"], 60 * 1000, 60 * 1000);

    $selenium->get("https://www.cnn.com");
} catch (Error | Exception $e) {
    // $e->getMessage();               -- assuming you have required dependencies, it's a timeout error
    goto START;
}

$data = $selenium->getPageSource();

$selenium->quit();                                      // Close the browser opened by lynx

#print_r($data);

file_put_contents("cnn.html", $data);

$html = `lynx -accept_all_cookies -dump cnn.html`;      // get link titles

#print_r($html);

preg_match('/Tech\s[\n\s\w\t:|.,?()$#%\-\']+(?=\[)/', $html, $tech);   // regex rocks..

preg_match('/Health\s[\n\s\w\t:|.,?()$#%\-*\'\[\]]{40,}(?=(Tech)|(Entertainment))/', $html, $health);

preg_match('/Tech\s[\n\s\w\t:|.,?()$#%\-\']{40,}(?=(CNN Travel ))/', $html, $travel);

# print_r($html);

if (empty($health) || empty($tech) || empty($travel)) {       // bc cnn sticks
    goto START;                                             // The goto operator is bad practice on the web ( but not cli :P )
}

print_r($health);     // print respective arrays captured by regex

print_r($tech);

print_r($travel);


/** This is a closure that returns itself. Parameters in the use brackets are captured by value on definition.
 * $var will end up running three times in this script, but is simplified in the main.min.php
 *
 * @param array $matches -- an array with a singular string value containing the article titles
 * @return callable $var   -- this function.. so I can do the ()()() thing at the end.
 */
($var = function ($matches) use (&$var) {
    $matches = array_reverse(array_values(array_unique(explode("\n", trim($matches[0])))));
    foreach ($matches as $title) {
        if (strlen($title = trim($title)) > 6 && !strpos($title, 'CNN') && substr($title, -3) !== '...') {
            if ($title[0] === '[') {
                $title = substr($title, 5);
            }
            print $title . PHP_EOL;
        }
    }
    return $var;
})($tech)($health)($travel);


