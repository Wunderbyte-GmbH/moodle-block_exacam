
how to install:

* add the code below to the bottom of your moodle/config.php
    ```php
    require __DIR__.'/blocks/exacam/include.php';
    ```

* add the quiz setting to moodle:

    in file ```/mod/quiz/mod_form.php``` 
    ```php
        // below line 285:
        $mform->addElement('header', 'security', get_string('extraattemptrestrictions', 'quiz'));
        
        // add:
        // [gtn]
        $mform->addElement('selectyesno', 'useexacam', 'Überwachung mittels Webcam aktivieren');
        $mform->setDefault('useexacam', @$quizconfig->useexacam);
        // [/gtn]
    ```

