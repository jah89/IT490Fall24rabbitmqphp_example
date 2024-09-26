<?php
/**
 * Head for all html docs
*/

//namespace NBA\Frontend\Includes\Components;

abstract class Head {
    

public static function displayHead() {
    ?>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title> NBA Fantasy Lookup Tool</title>
        <meta name="description" content="A tool to research NBA Players' Stats">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="../../css/output.css">

        <?php

    } //end of displayHead
} //end of class
?>