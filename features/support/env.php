<?php

require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';
require_once 'goutte.phar';

include 'includes.php';
include 'paths.php';

/**
 * @var Client $world->client
 * Virtual browser
 */
$world->client   = new \Goutte\Client;
/**
 * @var Crawler $world->page
 * Main crawler of page
 */
$world->page     = null;
/**
 * @var Response $world-> response
 * Response of virtual client
 */
$world->response = null;
/**
 * @var string $world->output
 * Returned page text
 */
$world->output   = null;
/**
 * @var array $world->forms
 * Array of forms
 */
$world->forms = array();

/**
 * Returns node by parrent and number
 *
 * @param Crawler $parrent
 *  Parrent crawler element
 * @param int $position
 *  Position of requested node
 *
 * @return DOMNode
 */
$world->__getNode = function($parent,$position) use ($world) {
    foreach ($parent as $i => $node) {
        if ($i == $position) return $node;
    }
    return null;
};
/**
 * Returns named form
 *
 * @param string $form
 *  Name of form (value/id of submit button)
 *
 * @return Form
 */
$world->__getForm = function($form) use ($world) {
    if (isset($world->forms[$form])) {
        return $world->forms[$form];
    } else {
        $_form = $world->page->selectButton($form)->form();
        $world->forms[$form] = $_form;
        return $_form;
    }
};
/**
 * Returns named path
 *
 * @param string $path
 *  Name of path (defined in [features_folder]/support/paths.php)
 *
 * @return string
 */
$world->__getPath = function($path) use ($world) {
    assertArrayHasKey($path,$world->paths,
        "Unknown path '$path'. You can define it in [features_folder]/support/paths.php");

    return $world->paths[$path];
};
/**
 * Defines properties of virtual browser
 */
$world->__getClientProperties = function() use ($world) {
    $world->page     = $world->client->getCrawler();
    $world->response = $world->client->getResponse();
    $world->output   = $world->response->getContent();
    $world->forms    = array();
};

?>

