<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Contact Me</title>
</head>
<body>
    <h1></h1>
    <?php
    function validateInput($data, $fieldName) {
        global $errorCount; 
        if (empty($data)) {
            echo "\"$fieldName\" is a required field.<br />\n";
            ++$errorCount;
            $retval = "";
        }
        else { // Only clean up the input if it isn't empty  
        $retval = trim($data);
        $retval = stripslashes($retval);
        }

        return($retval); }

    function validateEmail($data, $fieldName) {
        global $errorCount;

        if (empty($data)) {
            echo "\"$fieldName\" is a required field.<br />\n";
            ++$errorCount; $retval = "";
        }
        else {
            $retval = filter_var($data, FILTER_SANITIZE_EMAIL);
            if (!filter_var($retval, FILTER_VALIDATE_EMAIL)) {
                echo "\"$fieldName\" is not a valid e-mail address.<br />\n";
            }
        }
    return($retval);
    }
    
    function displayForm($Sender, $Email, $Subject, $Message) {
        ?> <h2 style="text-align:center">Contact Me</h2>
        <form name="contact" action="ContactForm.php" method="post">
            <p>Your name:
                <input type="text" name="Sender" value="<?php echo $Sender; ?>"/></p>
            <p>Your E-mail:
                <input type="text" name="Email" value="<?php echo $Email; ?>" /></p>
            <p>Subject: 
                <input type="text" name="Subject" value="<?php echo $Subject; ?>" /></p>
            <p>Message:<br />
                <textarea name="Message"><?php echo $Message; ?></textarea></p>
            <p><input type="reset" value="Clear Form" />&nbsp; &nbsp;
                <input type="submit" name="Submit" value="Send Form" /></p>
        </form>
        <?php 
        $ShowForm = TRUE;
        $errorCount = 0;
        $Sender = "";
        $Email = "";
        $Subject = "";
        $Message = "";

        if (isset($_POST['Submit'])) {
            $Sender = validateInput($_POST['Sender'],"Your name");
            $Email = validateEmail($_POST['Email'],"Your E-mail");
            $Subject = validateInput($_POST['Subject'],"Subject");
            $Message = validateInput($_POST['Message'],"Message");
            if ($errorCount==0)
                $ShowForm = FALSE;
            else   
                $ShowForm = TRUE;
        }

        if ($ShowForm == TRUE) {
            if ($errorCount>0) {
                echo "<p>Please re-enter the form information below.</p>\n";
                displayForm($Sender, $Email, $Subject, $Message);
            }
        }
        else {
            $SenderAddress = "$Sender <$Email>";
            $Headers = "From: $SenderAddress\nCC: $SenderAddress\n";

            $result = mail("recipient@example.com", $Subject, $Message, $Headers);
            if ($result)
                echo "<p>Your message has been sent. Thank you, " . $Sender . ".</p>\n";
            else 
                echo "<p>There was an error sending your message, " . $Sender . ".</p>\n";
        }
    }
    ?>
    <p></p>
</body>
</html>