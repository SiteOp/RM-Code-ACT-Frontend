<?php
/**
 * @version    CVS: 1.1.0
 * @package    Com_Act
 * @author     Richard Gebhard <gebhard@site-optimierer.de>
 * @copyright  2019 Richard Gebhard
 * @license    GNU General Public License Version 2 oder später; siehe LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\User\User;

$app    = Factory::getApplication();
$user   = Factory::getUser();

// Send Mail
$mail = Factory::getMailer();
$config = JFactory::getConfig();
$sender = array(
    $config->get('mailfrom'),
    $config->get('fromname') 
);

// Empfänger aus der Joomla Konfiguration
$recipient = $config->get('mailfrom');
$mail->addRecipient($recipient);

// Input from Form
$jinput = Factory::getApplication()->input;
$comment = $jinput->post->get('comment', '', 'HTML');

// Link zur Route
$routeLink = ( Route::_('index.php?option=com_act&view=route&id='  . $this->item->id, false, 1));
?>

 
<?php // Ist das Formular abgesendet und hat nur einen Stern ?>
<?php if (isset($_POST["submit"]) && ($jinput->post->get('stars') == 1)) : ?>

    <?php
    $body  = "<html><body style='margin-top: 50px'>";
    $body .= "<table style='border: 1px #666 solid; width: 660px; margin: 0 auto; padding: 15px; font-size: 18px;' cellpadding='5' cellspacing='5'>";
    $body .=    "<tr><td colspan='2'>Es wurde eine Bewertung mit nur einem Stern abgegeben.</td></tr>";
    $body .=    "<tr><td><strong>Datum:</strong> </td> <td>" . Factory::getDate()->Format('d.m.Y - H:i:s')." Uhr</td></tr>";
    $body .=    "<tr><td><strong>Route:</strong> </td> <td><a href='".$routeLink."'>".$this->item->name."</a></td></tr>";
    $body .=    "<tr><td><strong>User:</strong>  </td> <td>" . $user->name . "</td></tr>";
    if (!empty($comment))
    {
    $body .=    "<tr> <td colspan='2'><strong>Kommentar:</strong></td></tr>";
    $body .=    "<tr> <td>" .  stripslashes($comment) . "</td></tr>";
    }
    else
    {
        $body .=    "<tr> <td  colspan='2'>Kommentar wurde keiner verfasst.</td></tr>";
    }
    $body .= "</table>";
    $body .= "</body></html>";

    $mail->isHTML(true);
    $mail->Encoding = 'base64';
    $mail->setSubject('Kommentar mit nur einem Stern');
    $mail->setBody($body);

    $send = $mail->Send();
    ?>

<?php endif; ?>






