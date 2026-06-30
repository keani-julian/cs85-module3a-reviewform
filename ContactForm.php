<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Contact Me</title>
</head>
<body>
    <h1></h1>
    <?php
    // Start of the PHP — set up the helper functions first

    // validateInput() — cleans up a regular text field and hands back a tidy version
    function validateInput($data, $fieldName) {
        // global grabs the shared error count so this function can add to it
        global $errorCount;
        // If the box was left blank...
        if (empty($data)) {
            // ...let the user know that field is required
            echo "\"$fieldName\" is a required field.<br />\n";
            // Add one to the error count
            ++$errorCount;
            // Send back an empty string since there was nothing to clean
            $retval = "";
        }
        // Otherwise there's actually something here, so clean it up
        else {
            // Cut off any extra spaces on the ends
            $retval = trim($data);
            // Take out any stray backslashes
            $retval = stripslashes($retval);
        }

        // Give the cleaned-up text back
        return($retval); }

    // validateEmail() — same idea, but makes sure it's a real e-mail address
    function validateEmail($data, $fieldName) {
        // Get the shared error count
        global $errorCount;

        // If the e-mail box is empty...
        if (empty($data)) {
            // ...tell the user it's required
            echo "\"$fieldName\" is a required field.<br />\n";
            // Add to the error count and set the value to empty
            ++$errorCount; $retval = "";
        }
        // Otherwise we've got something to check
        else {
            // Strip out anything that doesn't belong in an e-mail address
            $retval = filter_var($data, FILTER_SANITIZE_EMAIL);
            // Double-check it's actually shaped like a real e-mail
            if (!filter_var($retval, FILTER_VALIDATE_EMAIL)) {
                // If it's not, say it's invalid
                echo "\"$fieldName\" is not a valid e-mail address.<br />\n";
            }
        }
    // Hand the e-mail back
    return($retval);
    }

    // displayForm() — shows the contact form, keeping whatever the user already typed
    function displayForm($Sender, $Email, $Subject, $Message) {
        /* Drop into HTML to print the form. Each box is re-filled with whatever
           the user already typed (name, e-mail, subject, and message) so nothing
           is lost. The form posts back to this same page, there's a button to clear
           it, and the "Send Form" submit button is the one the script checks for. */
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
        // A flag that decides whether to show the form or send the e-mail
        $ShowForm = TRUE;
        // Start the error count fresh
        $errorCount = 0;
        // Start all the fields off empty for the first visit
        $Sender = "";
        $Email = "";
        $Subject = "";
        $Message = "";

        // If the user actually hit submit, check everything
        if (isset($_POST['Submit'])) {
            // Run each field through its check and save the cleaned-up result
            $Sender = validateInput($_POST['Sender'],"Your name");
            $Email = validateEmail($_POST['Email'],"Your E-mail");
            $Subject = validateInput($_POST['Subject'],"Subject");
            $Message = validateInput($_POST['Message'],"Message");
            // If nothing was wrong...
            if ($errorCount==0)
                // ...we don't need the form anymore
                $ShowForm = FALSE;
            // But if there was a problem...
            else
                // ...keep the form up so they can fix it
                $ShowForm = TRUE;
        }

        // If we're still showing the form...
        if ($ShowForm == TRUE) {
            // ...and something was wrong...
            if ($errorCount>0) {
                // ...ask them to re-enter their info
                echo "<p>Please re-enter the form information below.</p>\n";
                // Show the form again with what they already typed
                displayForm($Sender, $Email, $Subject, $Message);
            }
        }
        // Otherwise everything checked out, so send the message
        else {
            // Put together the sender's address for the e-mail
            $SenderAddress = "$Sender <$Email>";
            // Set up the headers — the CC sends a copy back to the sender
            $Headers = "From: $SenderAddress\nCC: $SenderAddress\n";

            // Try to actually send it
            $result = mail("recipient@example.com", $Subject, $Message, $Headers);
            // If it went through...
            if ($result)
                // ...thank them
                echo "<p>Your message has been sent. Thank you, " . $Sender . ".</p>\n";
            // If it didn't...
            else
                // ...let them know something went wrong
                echo "<p>There was an error sending your message, " . $Sender . ".</p>\n";
        }
    // End of displayForm()
    }
    // Close the PHP script enclosed above and go back to plain HTML
    ?>
    <p></p>
</body>
</html>

<?php
/* REFLECTION

   1. What does each function do?
   validateInput() takes a text field, makes sure it isn't blank, and cleans
   it up before handing it back. 
   validateEmail() does the same kind of check but also makes sure the address 
   looks like a real e-mail. 
   displayForm() shows the contact form and keeps whatever the user already typed 
   so they don't have to start over.

   2. How is user input protected?
   empty() catches blank required fields before anything is sent.
   trim() removes extra whitespace and stripslashes() removes stray backslashes from the text fields.
   filter_var() with SANITIZE_EMAIL strips illegal characters from the e-mail, 
   and VALIDATE_EMAIL confirms it's a properly formed address.
   Anything that fails bumps the error count, which stops the form from submitting until it's fixed.

   3. What were the most confusing parts?
   Keeping track of where PHP stops and HTML starts around the form was
   tricky, and so was making sure all the curly braces lined up so every
   function and if/else closed where it was supposed to.

   4. What could be improved?
   Right now displayForm() is doing a lot. It shows the form and also handles
   the sending logic. It may be cleaner to keep that function just for showing
   the form and move the rest into the main part of the page. It could also be
   safer to escape the values before showing them again.

   5. Why send a copy of the form to the sender?
   The CC gives the sender their own copy, so they have a record of what they
   sent and proof that the message actually went out.
*/
?>
