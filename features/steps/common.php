<?php

$steps->Given('/^I\'m on ([^\']*)$/', function($world, $page) {
    $page = $world->__getPath($page);

    $world->client->request('GET', $page);
    $world->__getClientProperties();
});

$steps->Given('/^I am on ([^\']*)$/', function($world, $page) use ($steps) {
    $steps->Given("I'm on $page", $world);
});

$steps->When('/^I go to ([^\']*)$/', function($world, $page) use ($steps) {
    $steps->Given("I'm on $page", $world);
});

$steps->When('/^I visit ([^\']*)$/', function($world, $page) use ($steps) {
    $steps->Given("I'm on $page", $world);
});

$steps->When('/^I click on link \'([^\']*)\'$/', function($world, $link) {
    assertNotNull($world->page,"No webpage loaded");

    $link = $world->page->selectLink($link)->link();
    $world->client->click($link);
    $world->__getClientProperties();
});

$steps->When('/^I follow \'([^\']*)\'$/', function($world, $link) use ($steps) {
    $steps->When("I click on link '$link'", $world);
});

$steps->When('/^I click on link \'([^\']*)\' within \'([^\']*)\'$/', function($world, $link, $selector) {
    assertNotNull($world->page,"No webpage loaded");

    $link = $world->page->filter($selector)->selectLink($link)->link();
    $world->client->click($link);
    $world->__getClientProperties();
});

$steps->When('/^I follow \'([^\']*)\' within \'([^\']*)\'$/', function($world, $link, $parent) use ($steps) {
    $steps->When("I click on link '$link' within '$selector'", $world);
});

$steps->When('/^I fill in \'([^\']*)\' with \'([^\']*)\' in form \'([^\']*)\'$/', function($world, $field, $value, $form) {
    assertNotNull($world->page,"No webpage loaded");

    $form = $world->__getForm($form);
    $form[$field]->setValue($value);
});

$steps->When('/^I fill in \'([^\']*)\' for \'([^\']*)\' in form \'([^\']*)\'$/', function($world, $field, $value, $form) use ($steps) {
    $steps->When("I fill in '$field' with '$value' in form '$form'", $world);
});

$steps->When('/^I tick checkbox \'([^\']*)\' in form \'([^\']*)\'$/', function($world, $field, $form) {
    assertNotNull($world->page,"No webpage loaded");

    $form = $world->__getForm($form);
    $form[$field]->tick();
});

$steps->When('/^I check \'([^\']*)\' in form \'([^\']*)\'$/', function($world, $field, $form) use ($steps) {
    $steps->When("I tick checkbox '$field' in form '$form'", $world);
});

$steps->When('/^I untick checkbox \'([^\']*)\' in form \'([^\']*)\'$/', function($world, $field, $form) {
    assertNotNull($world->page,"No webpage loaded");

    $form = $world->__getForm($form);
    $form[$field]->untick();
});

$steps->When('/^I uncheck \'([^\']*)\' in form \'([^\']*)\'$/', function($world, $field, $form) use ($steps) {
    $steps->When("I untick checkbox '$field' in form '$form'", $world);
});

$steps->When('/^I select \'([^\']*)\' in \'([^\']*)\' in form \'([^\']*)\'$/', function($world, $value, $field, $form) {
    assertNotNull($world->page,"No webpage loaded");

    $form = $world->__getForm($form);
    $form[$field]->select($value);
});

$steps->When('/^I select \'([^\']*)\' in radio \'([^\']*)\' in form \'([^\']*)\'$/', function($world, $value, $field, $form) use ($steps) {
    $steps->When("I select '$value' in '$field' in form '$form'", $world);
});

$steps->When('/^I select \'([^\']*)\' in selectbox \'([^\']*)\' in form \'([^\']*)\'$/', function($world, $value, $field, $form) use ($steps) {
    $steps->When("I select '$value' in '$field' in form '$form'", $world);
});

$steps->When('/^I attach the file \'([^\']*)\' to \'([^\']*)\' in form \'([^\']*)\'$/', function($world, $file, $field, $form) use ($steps) {
    assertNotNull($world->page,"No webpage loaded");

    assertFileExists($file);

    $form = $world->__getForm($form);
    $form[$field]->upload($file);
});

$steps->When('/^I fill in following in form \'([^\']*)\':$/', function($world, $form, $table) use ($steps) {
    assertNotNull($world->page,"No webpage loaded");

    $_form = $world->__getForm($form);
    $table = $table->getRows();

    foreach ($table as $row) {
        $field = $row[0];
        $value = $row[1];

        switch(get_class($_form[$field])){
            //Checkboxes, radios, selectboxes
            case "Symfony\Component\DomCrawler\Field\ChoiceFormField":
                if ($_form[$field]->getType() == "checkbox") {
                    if ($value == 0 || $value == 'false' || $value == 'FALSE')
                        $steps->When("I tick checkbox '$field' in form '$form'", $world);
                    else
                        $steps->When("I untick checkbox '$field' in form '$form'", $world);
                } else {
                    $steps->When("I select '$value' in '$field' in form '$form'", $world);
                }
                break;
            //File fields
            case "Symfony\Component\DomCrawler\Field\FileFormField":
                $steps->When("I attach the file '$value' to '$field' in form '$form'", $world);
                break;
            //Textfields, textareas
            default:
                $steps->When("I fill in '$field' with '$value' in form '$form'", $world);
        }
    }
});

$steps->When('/^I submit form \'([^\']*)\'$/', function($world, $form) {
    assertNotNull($world->page,"No webpage loaded");

    $form = $world->__getForm($form);
    $world->client->submit($form);
    $world->__getClientProperties();
});

$steps->Then('/^I should see \'([^\']*)\'$/', function($world, $needle) {
    assertNotNull($world->page,"No webpage loaded");
    assertContains($needle,$world->output);
});

$steps->Then('/^I should see \'([^\']*)\' within \'([^\']*)\'$/', function($world, $needle, $selector) {
    assertNotNull($world->page,"No webpage loaded");
    $node = $world->page->filter($selector)->text();
    assertContains($needle,$node);
});

$steps->Then('/^I should see \/([^\/]*)\/$/', function($world, $needle) {
    assertNotNull($world->page,"No webpage loaded");
    assertRegExp('/'.$needle.'/',$world->output);
});

$steps->Then('/^I should see \/([^\/]*)\/ within \'([^\']*)\'$/', function($world, $needle, $selector) {
    assertNotNull($world->page,"No webpage loaded");
    $node = $world->page->filter($selector)->text();
    assertRegExp('/'.$needle.'/',$node);
});

$steps->Then('/^I should not see \'([^\']*)\'$/', function($world, $needle) {
    assertNotNull($world->page,"No webpage loaded");
    assertNotContains($needle,$world->output);
});

$steps->Then('/^I should not see \'([^\']*)\' within \'([^\']*)\'$/', function($world, $needle, $selector) {
    assertNotNull($world->page,"No webpage loaded");
    $node = $world->page->filter($selector)->text();
    assertNotContains($needle,$node);
});

$steps->Then('/^I should not see \/([^\/]*)\/$/', function($world, $needle) {
    assertNotNull($world->page,"No webpage loaded");
    assertNotRegExp('/'.$needle.'/',$world->output);
});

$steps->Then('/^I should not see \/([^\/]*)\/ within \'([^\']*)\'$/', function($world, $needle, $selector) {
    assertNotNull($world->page,"No webpage loaded");
    $node = $world->page->filter($selector)->text();
    assertNotRegExp('/'.$needle.'/',$node);
});

$steps->Then('/^the \'([^\']*)\' field in form \'([^\']*)\' should constain \'([^\']*)\'$/', function($world, $field, $form, $needle) {
    assertNotNull($world->page,"No webpage loaded");
    $form = $world->__getForm($form);
    assertContains($needle,$form[$field]->getValue());
});

$steps->Then('/^the \'([^\']*)\' field in form \'([^\']*)\' should not constain \'([^\']*)\'$/', function($world, $field, $form, $needle) {
    assertNotNull($world->page,"No webpage loaded");
    $form = $world->__getForm($form);
    assertNotContains($needle,$form[$field]->getValue());
});

$steps->Then('/^the \'([^\']*)\' field in form \'([^\']*)\' should be blank$/', function($world, $field, $form) {
    assertNotNull($world->page,"No webpage loaded");
    $form = $world->__getForm($form);
    assertEmpty($form[$field]->getValue(),"Field is not blank");
});

$steps->Then('/^the \'([^\']*)\' field in form \'([^\']*)\' should be not blank$/', function($world, $field, $form) {
    assertNotNull($world->page,"No webpage loaded");
    $form = $world->__getForm($form);
    assertNotEmpty($form[$field]->getValue(),"Field is blank");
});


?>

