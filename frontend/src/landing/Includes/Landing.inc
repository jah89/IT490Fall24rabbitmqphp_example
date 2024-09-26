<?php
//frontend main landing page

abstract class Landing {

    /**
    *Displays main landing page.
    *@return void
    */

    public static function displayLanding() {
        ?>

        <!DOCTYPE html>
        <html lang="en">

        <head>
           <?php echo /includes/components/Head::displayHead(); ?> 
        </head>

        <body>
            <h1 class="text-3xl font-bold underline">hello there sports fans, how is your day today?</h1>
            <?php 
            //TO DO: make components for Nav, header, and footer.  
            //TO DO: Make session logic.
            ?>
            <a href="../login/login.php"> Login Here</a>
        </body>
        </html>
    <?php

    } //end of displayLanding()
}