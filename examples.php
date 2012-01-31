<?php
    // Including class file.
    include_once(dirname(__FILE__).'/class.form.php');

    // Example 1
    // Create a new instans of the class.
    $form = new Form('example1');
    // Fill the form.
    $form->newLineSetting('afterInput');
    $form->start('examples.php?id=1');
    $form->addLabel('name', 'Your name:');
    $form->addTextInput('name');
    $form->addSpamFilter();
    $form->addSubmitEnd();
    // Get the finish form and print it.
    echo $form->get();
    
    // To check/print exemple 1
    if($_REQUEST['id'] == '1') {
        
        echo 'Hello '.$_REQUEST['name'].'!<br/>';
        
        if($_REQUEST['spamresult'] == $_REQUEST['spamfilter']) {
            echo 'No spamming, good!';
        } else {
            echo 'Are you a spammer?';
        }
    }
    
    
    // Example 2
    // Create a pretend error
    $errorArray = array('lastName'=>'You need to write a last name.');
    // Create a new instans of the class.
    $form = new Form('example2', true, $errorArray, 180);
    // Fill the form.
    $form->start('examples.php?id=2', 'post', '_self', 'on', true);
    $form->addLabel('lastName', 'Last name:');
    $form->addTextInput('lastName');
    $form->addLabel('email', 'Email:');
    $form->addInput('email', 'email');
    $form->addLabel('password', 'Password:');
    $form->addPassword('password', 100);
    $form->addLabel('imagefile', 'File:');
    $form->addFileUploader('imagefile');
    $form->addLabel('textarea', 'Text:');
    $value = "Write something here..";
    $form->addTextarea('textarea', $value);
    $form->addLabel('check', 'Pressure checkbox:');
    $form->addCheckbox('check', 'value', 'checkbox', true);
    $form->addNewLine();
    $form->addLabel('select', 'Select:');
    $list = array('mw'=>'mickesweb.se','goo'=>'goo.nu','sok'=>'soksidor.se');
    $form->addSelect('choice', 'choice', $list);
    $form->addSubmitEnd();
    // Get the finish form and print it.
    echo $form->get();
?>