<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "jesus.cervi@gmail.com" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "0472a3" );

?>
<?php
/**
 * GNU Library or Lesser General Public License version 2.0 (LGPLv2)
*/

# main
# ------------------------------------------------------
error_reporting( E_ERROR ) ;
phpfmg_admin_main();
# ------------------------------------------------------




function phpfmg_admin_main(){
    $mod  = isset($_REQUEST['mod'])  ? $_REQUEST['mod']  : '';
    $func = isset($_REQUEST['func']) ? $_REQUEST['func'] : '';
    $function = "phpfmg_{$mod}_{$func}";
    if( !function_exists($function) ){
        phpfmg_admin_default();
        exit;
    };

    // no login required modules
    $public_modules   = false !== strpos('|captcha|', "|{$mod}|", "|ajax|");
    $public_functions = false !== strpos('|phpfmg_ajax_submit||phpfmg_mail_request_password||phpfmg_filman_download||phpfmg_image_processing||phpfmg_dd_lookup|', "|{$function}|") ;   
    if( $public_modules || $public_functions ) { 
        $function();
        exit;
    };
    
    return phpfmg_user_isLogin() ? $function() : phpfmg_admin_default();
}

function phpfmg_ajax_submit(){
    $phpfmg_send = phpfmg_sendmail( $GLOBALS['form_mail'] );
    $isHideForm  = isset($phpfmg_send['isHideForm']) ? $phpfmg_send['isHideForm'] : false;

    $response = array(
        'ok' => $isHideForm,
        'error_fields' => isset($phpfmg_send['error']) ? $phpfmg_send['error']['fields'] : '',
        'OneEntry' => isset($GLOBALS['OneEntry']) ? $GLOBALS['OneEntry'] : '',
    );
    
    @header("Content-Type:text/html; charset=$charset");
    echo "<html><body><script>
    var response = " . json_encode( $response ) . ";
    try{
        parent.fmgHandler.onResponse( response );
    }catch(E){};
    \n\n";
    echo "\n\n</script></body></html>";

}


function phpfmg_admin_default(){
    if( phpfmg_user_login() ){
        phpfmg_admin_panel();
    };
}



function phpfmg_admin_panel()
{    
    phpfmg_admin_header();
    phpfmg_writable_check();
?>    
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td valign=top style="padding-left:280px;">

<style type="text/css">
    .fmg_title{
        font-size: 16px;
        font-weight: bold;
        padding: 10px;
    }
    
    .fmg_sep{
        width:32px;
    }
    
    .fmg_text{
        line-height: 150%;
        vertical-align: top;
        padding-left:28px;
    }

</style>

<script type="text/javascript">
    function deleteAll(n){
        if( confirm("Are you sure you want to delete?" ) ){
            location.href = "admin.php?mod=log&func=delete&file=" + n ;
        };
        return false ;
    }
</script>


<div class="fmg_title">
    1. Email Traffics
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=1">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=1">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_EMAILS_LOGFILE) ){
            echo '<a href="#" onclick="return deleteAll(1);">delete all</a>';
        };
    ?>
</div>


<div class="fmg_title">
    2. Form Data
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=2">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=2">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_SAVE_FILE) ){
            echo '<a href="#" onclick="return deleteAll(2);">delete all</a>';
        };
    ?>
</div>

<div class="fmg_title">
    3. Form Generator
</div>
<div class="fmg_text">
    <a href="http://www.formmail-maker.com/generator.php" onclick="document.frmFormMail.submit(); return false;" title="<?php echo htmlspecialchars(PHPFMG_SUBJECT);?>">Edit Form</a> &nbsp;&nbsp;
    <a href="http://www.formmail-maker.com/generator.php" >New Form</a>
</div>
    <form name="frmFormMail" action='http://www.formmail-maker.com/generator.php' method='post' enctype='multipart/form-data'>
    <input type="hidden" name="uuid" value="<?php echo PHPFMG_ID; ?>">
    <input type="hidden" name="external_ini" value="<?php echo function_exists('phpfmg_formini') ?  phpfmg_formini() : ""; ?>">
    </form>

		</td>
	</tr>
</table>

<?php
    phpfmg_admin_footer();
}



function phpfmg_admin_header( $title = '' ){
    header( "Content-Type: text/html; charset=" . PHPFMG_CHARSET );
?>
<html>
<head>
    <title><?php echo '' == $title ? '' : $title . ' | ' ; ?>PHP FormMail Admin Panel </title>
    <meta name="keywords" content="PHP FormMail Generator, PHP HTML form, send html email with attachment, PHP web form,  Free Form, Form Builder, Form Creator, phpFormMailGen, Customized Web Forms, phpFormMailGenerator,formmail.php, formmail.pl, formMail Generator, ASP Formmail, ASP form, PHP Form, Generator, phpFormGen, phpFormGenerator, anti-spam, web hosting">
    <meta name="description" content="PHP formMail Generator - A tool to ceate ready-to-use web forms in a flash. Validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. ">
    <meta name="generator" content="PHP Mail Form Generator, phpfmg.sourceforge.net">

    <style type='text/css'>
    body, td, label, div, span{
        font-family : Verdana, Arial, Helvetica, sans-serif;
        font-size : 12px;
    }
    </style>
</head>
<body  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">

<table cellspacing=0 cellpadding=0 border=0 width="100%">
    <td nowrap align=center style="background-color:#024e7b;padding:10px;font-size:18px;color:#ffffff;font-weight:bold;width:250px;" >
        Form Admin Panel
    </td>
    <td style="padding-left:30px;background-color:#86BC1B;width:100%;font-weight:bold;" >
        &nbsp;
<?php
    if( phpfmg_user_isLogin() ){
        echo '<a href="admin.php" style="color:#ffffff;">Main Menu</a> &nbsp;&nbsp;' ;
        echo '<a href="admin.php?mod=user&func=logout" style="color:#ffffff;">Logout</a>' ;
    }; 
?>
    </td>
</table>

<div style="padding-top:28px;">

<?php
    
}


function phpfmg_admin_footer(){
?>

</div>

<div style="color:#cccccc;text-decoration:none;padding:18px;font-weight:bold;">
	:: <a href="http://phpfmg.sourceforge.net" target="_blank" title="Free Mailform Maker: Create read-to-use Web Forms in a flash. Including validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. " style="color:#cccccc;font-weight:bold;text-decoration:none;">PHP FormMail Generator</a> ::
</div>

</body>
</html>
<?php
}


function phpfmg_image_processing(){
    $img = new phpfmgImage();
    $img->out_processing_gif();
}


# phpfmg module : captcha
# ------------------------------------------------------
function phpfmg_captcha_get(){
    $img = new phpfmgImage();
    $img->out();
    //$_SESSION[PHPFMG_ID.'fmgCaptchCode'] = $img->text ;
    $_SESSION[ phpfmg_captcha_name() ] = $img->text ;
}



function phpfmg_captcha_generate_images(){
    for( $i = 0; $i < 50; $i ++ ){
        $file = "$i.png";
        $img = new phpfmgImage();
        $img->out($file);
        $data = base64_encode( file_get_contents($file) );
        echo "'{$img->text}' => '{$data}',\n" ;
        unlink( $file );
    };
}


function phpfmg_dd_lookup(){
    $paraOk = ( isset($_REQUEST['n']) && isset($_REQUEST['lookup']) && isset($_REQUEST['field_name']) );
    if( !$paraOk )
        return;
        
    $base64 = phpfmg_dependent_dropdown_data();
    $data = @unserialize( base64_decode($base64) );
    if( !is_array($data) ){
        return ;
    };
    
    
    foreach( $data as $field ){
        if( $field['name'] == $_REQUEST['field_name'] ){
            $nColumn = intval($_REQUEST['n']);
            $lookup  = $_REQUEST['lookup']; // $lookup is an array
            $dd      = new DependantDropdown(); 
            echo $dd->lookupFieldColumn( $field, $nColumn, $lookup );
            return;
        };
    };
    
    return;
}


function phpfmg_filman_download(){
    if( !isset($_REQUEST['filelink']) )
        return ;
        
    $info =  @unserialize(base64_decode($_REQUEST['filelink']));
    if( !isset($info['recordID']) ){
        return ;
    };
    
    $file = PHPFMG_SAVE_ATTACHMENTS_DIR . $info['recordID'] . '-' . $info['filename'];
    phpfmg_util_download( $file, $info['filename'] );
}


class phpfmgDataManager
{
    var $dataFile = '';
    var $columns = '';
    var $records = '';
    
    function phpfmgDataManager(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }
    
    function parseFile(){
        $fp = @fopen($this->dataFile, 'rb');
        if( !$fp ) return false;
        
        $i = 0 ;
        $phpExitLine = 1; // first line is php code
        $colsLine = 2 ; // second line is column headers
        $this->columns = array();
        $this->records = array();
        $sep = chr(0x09);
        while( !feof($fp) ) { 
            $line = fgets($fp);
            $line = trim($line);
            if( empty($line) ) continue;
            $line = $this->line2display($line);
            $i ++ ;
            switch( $i ){
                case $phpExitLine:
                    continue;
                    break;
                case $colsLine :
                    $this->columns = explode($sep,$line);
                    break;
                default:
                    $this->records[] = explode( $sep, phpfmg_data2record( $line, false ) );
            };
        }; 
        fclose ($fp);
    }
    
    function displayRecords(){
        $this->parseFile();
        echo "<table border=1 style='width=95%;border-collapse: collapse;border-color:#cccccc;' >";
        echo "<tr><td>&nbsp;</td><td><b>" . join( "</b></td><td>&nbsp;<b>", $this->columns ) . "</b></td></tr>\n";
        $i = 1;
        foreach( $this->records as $r ){
            echo "<tr><td align=right>{$i}&nbsp;</td><td>" . join( "</td><td>&nbsp;", $r ) . "</td></tr>\n";
            $i++;
        };
        echo "</table>\n";
    }
    
    function line2display( $line ){
        $line = str_replace( array('"' . chr(0x09) . '"', '""'),  array(chr(0x09),'"'),  $line );
        $line = substr( $line, 1, -1 ); // chop first " and last "
        return $line;
    }
    
}
# end of class



# ------------------------------------------------------
class phpfmgImage
{
    var $im = null;
    var $width = 73 ;
    var $height = 33 ;
    var $text = '' ; 
    var $line_distance = 8;
    var $text_len = 4 ;

    function phpfmgImage( $text = '', $len = 4 ){
        $this->text_len = $len ;
        $this->text = '' == $text ? $this->uniqid( $this->text_len ) : $text ;
        $this->text = strtoupper( substr( $this->text, 0, $this->text_len ) );
    }
    
    function create(){
        $this->im = imagecreate( $this->width, $this->height );
        $bgcolor   = imagecolorallocate($this->im, 255, 255, 255);
        $textcolor = imagecolorallocate($this->im, 0, 0, 0);
        $this->drawLines();
        imagestring($this->im, 5, 20, 9, $this->text, $textcolor);
    }
    
    function drawLines(){
        $linecolor = imagecolorallocate($this->im, 210, 210, 210);
    
        //vertical lines
        for($x = 0; $x < $this->width; $x += $this->line_distance) {
          imageline($this->im, $x, 0, $x, $this->height, $linecolor);
        };
    
        //horizontal lines
        for($y = 0; $y < $this->height; $y += $this->line_distance) {
          imageline($this->im, 0, $y, $this->width, $y, $linecolor);
        };
    }
    
    function out( $filename = '' ){
        if( function_exists('imageline') ){
            $this->create();
            if( '' == $filename ) header("Content-type: image/png");
            ( '' == $filename ) ? imagepng( $this->im ) : imagepng( $this->im, $filename );
            imagedestroy( $this->im ); 
        }else{
            $this->out_predefined_image(); 
        };
    }

    function uniqid( $len = 0 ){
        $md5 = md5( uniqid(rand()) );
        return $len > 0 ? substr($md5,0,$len) : $md5 ;
    }
    
    function out_predefined_image(){
        header("Content-type: image/png");
        $data = $this->getImage(); 
        echo base64_decode($data);
    }
    
    // Use predefined captcha random images if web server doens't have GD graphics library installed  
    function getImage(){
        $images = array(
			'DF99' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QgNEQx1CGaY6IIkFTBFpYHR0CAhAFmsVaWBtCHQQwS0GdlLU0qlhKzOjosKQ3AdSxxASMBVdL5BsQBdjbAhAtQOLW0IDgCrQ3DxQ4UdFiMV9AOspzZgJmFwdAAAAAElFTkSuQmCC',
			'24A1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WAMYWhmmADGSmMgUhqkMoUCMJBbQyhDK6OgQiqK7ldGVFSSD7L5pS5cuXRW1FMV9ASKtSOrAkNFBNNQ1FFWMFWgiujoRLGKhoWCx0IBBEH5UhFjcBwByMsvqy+jRVAAAAABJRU5ErkJggg==',
			'9337' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7WANYQxhDGUNDkMREpoi0sjY6NIggiQW0MgBFAtDFoKII902buips1dRVK7OQ3MfqClbXimIzROcUZDEBiFgAA4ZbHB2wuBlFbKDCj4oQi/sAy5LMYqAe2FEAAAAASUVORK5CYII=',
			'3AD2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7RAMYAlhDGaY6IIkFTGEMYW10CAhAVtnK2sraEOgggiw2RaTRtSGgQQTJfSujpq1MXRUFhEjug6hrdEAxTzQUKNaK4ppWsLopDChuAYoB3YLqZqBYKGNoyCAIPypCLO4DAP4FzeBHBV9KAAAAAElFTkSuQmCC',
			'0FF2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7GB1EQ11DA6Y6IImxBog0sDYwBAQgiYlMAYkxOoggiQW0gtU1iCC5L2rp1LCloUAayX1QdY0OmHpbGTDsYJjCgMUtqG4GuyU0ZBCEHxUhFvcBAAhJyya8Hy2DAAAAAElFTkSuQmCC',
			'7DC8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QkNFQxhCHaY6IIu2irQyOgQEBKCKNbo2CDqIIItNAYkxwNRB3BQ1bWXqqlVTs5Dcx+iAog4MWRtAYowo5ok0YNoR0IDploAGLG4eoPCjIsTiPgDzA8zj/vSA9wAAAABJRU5ErkJggg==',
			'A222' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGaY6IImxBrC2Mjo6BAQgiYlMEWl0bQh0EEESC2hlaHRoCGgQQXJf1NJVS1etzFoVheQ+oLopDGC1CL2hoQwBYFEU8xgdwKIoYqwNYFEUMdFQ19DA0JBBEH5UhFjcBwAWfsw2VhZmWgAAAABJRU5ErkJggg==',
			'67C2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeElEQVR4nM2QMQqAMAxF06G7Qr1POrhnaAc9TQr2BtUbuPSUxi1FR4XmD4HHhzwC9TEMPeUXP0tTxIg7KuYKJI9EpBhtkGYe0WnGkO3dV37LWo9T9qr8QgGSXtI3KBsUlqFhli0PBRoXx0ZcWme5GH0MHfzvw7z4XebRzJEa/bmLAAAAAElFTkSuQmCC',
			'0464' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nM3QMQ6AIAwF0DL0BngfGNhrYhdOU4feAI7gwillLMqo0f7tJ01fCu02An/KKz4XQIFByHRIUF0Mu+18AUYJajtSl1CgkPHlo09tORsfqVeMMYy7CydZeRtvKHbJxaLdMnQz81f/ezAT3wniDszb16Nb+AAAAABJRU5ErkJggg==',
			'7675' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nM2QsQ2AMAwEncIbmH1MQW8kQpFpnMIbABtQkClJ6QhKkPLfnWzp9FAeUeipv/jFGBaMcxRPDQ105ubSKD/YRgp5nNj7pWMt55WS8ws8GGyg5H5RKbO0jCobObBnomioINKw6qywcwf7fdgXvxvPBssukdlqDgAAAABJRU5ErkJggg==',
			'7DB0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QkNFQ1hDGVpRRFtFWlkbHaY6oIo1ujYEBAQgi00BijU6Oogguy9q2srU0JVZ05Dcx+iAog4MWRtA5gWiiIk0YNoR0IDploAGLG4eoPCjIsTiPgDO3s2e3E6zUgAAAABJRU5ErkJggg==',
			'9924' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGRoCkMREprC2Mjo6NCKLBbSKNLoCSXQxh4aAKQFI7ps2denSrJVZUVFI7mN1ZQx0aGV0QNbL0MrQ6DCFMTQESUyglaXRIQCLWxxQxUBuZg0NQBEbqPCjIsTiPgDEms1OO4WXxQAAAABJRU5ErkJggg==',
			'9105' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nM2QIQ6AMBAEt+I8og9qBX4RJ+hravhByw8wfSXgDoqEhFs32WQnh9Zdxp/yiZ8QRHFKw3xxhLpge1yELsYbAyVPYzB+a21pa3NKxk/Gs8fs7fLSs+Fg54a/uBx+Clo/oSgKavjB/17Mg98OskrIs13E0iIAAAAASUVORK5CYII=',
			'38DF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7RAMYQ1hDGUNDkMQCprC2sjY6OqCobBVpdG0IRBUDqUOIgZ20Mmpl2NJVkaFZyO5DVYfbPCxi2NwCdTOq3gEKPypCLO4DABRLymnfDDcpAAAAAElFTkSuQmCC',
			'A99B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUMdkMRYA1hbGR0dHQKQxESmiDS6NgQ6iCCJBbRCxAKQ3Be1dOnSzMzI0Cwk9wW0MgY6hASimBcaytDogGEeS6MjhhimW4DmYbh5oMKPihCL+wC+/8wbIFvTOgAAAABJRU5ErkJggg==',
			'AFC7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB1EQx1CHUNDkMRYA0SA4gENIkhiIlNEGlgbBFDEAlpBYkAayX1RS6eGLV21amUWkvug6lqR7Q0NBYtNYcAwTyAAXYzRIdABXYwh1BFFbKDCj4oQi/sAKw7MLsO1ZgkAAAAASUVORK5CYII=',
			'52E9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDHaY6IIkFNLC2sjYwBASgiIk0ujYwOoggiQUGMCCLgZ0UNm3V0qWhq6LCkN3XyjAFaN5UZL1AsQCgWAOyWEArowNQDMUOEaBOdLewBoiGuqK5eaDCj4oQi/sAX2TLZcAdZXkAAAAASUVORK5CYII=',
			'89FF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDA0NDkMREprC2sjYwOiCrC2gVaXRFExOZgiIGdtLSqKVLU0NXhmYhuU9kCmMgut6AVgYM8wJaWbDYgekWsJvRxAYq/KgIsbgPAA/tyYOkwS6AAAAAAElFTkSuQmCC',
			'B5A5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nM2QsQ2AMAwEnSIbmH1CQf+RcEGmMQUbmBFoMiUpHaAECX93etknU72N0p/yiZ9gELIgcAzGShKS72FjDePYM+M5ap6S85OyH0ddSnF+MFonhXK3rzG5Mm69nDpmcYsKeD9BaHexpx/878U8+J3eT833NZHjhgAAAABJRU5ErkJggg==',
			'2EB3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7WANEQ1lDGUIdkMREpog0sDY6OgQgiQW0AsUaAhpEkHWDxBodGgKQ3TdtatjS0FVLs5DdF4CiDgwZHTDNY23AFBNpwHRLaCimmwcq/KgIsbgPAL44zKeEenMMAAAAAElFTkSuQmCC',
			'5BCB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QkNEQxhCHUMdkMQCGkRaGR0CHQJQxRpdGwQdRJDEAgNEWlkbGGHqwE4KmzY1bOmqlaFZyO5rRVEHEwOax4hiXkArph0iUzDdwhqA6eaBCj8qQizuAwDWd8vIs5PsPgAAAABJRU5ErkJggg==',
			'9574' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WANEQ1lDAxoCkMREpogAyYBGZLGAVrBYK5pYCEOjw5QAJPdNmzp16aqlq6KikNzH6gpSxeiArJehFSgWwBgagiQm0CrS6OjAgOYW1lbWBlQx1gDGEHSxgQo/KkIs7gMAkKXN1IZnCU4AAAAASUVORK5CYII=',
			'FAF4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QkMZAlhDAxoCkMQCGhhDWBsYGlHFWFuBYq2oYiKNrg0MUwKQ3BcaNW1lauiqqCgk90HUMTqg6hUNBYqFhmCa14DFDoJiAxV+VIRY3AcAugjPVOM94IAAAAAASUVORK5CYII=',
			'BC1D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QgMYQxmmMIY6IIkFTGFtdAhhdAhAFmsVaXAEiomgqAPypsDFwE4KjZq2atW0lVnTkNyHpg5uHjYxB3QxkFumoLoF5GbGUEcUNw9U+FERYnEfANS/zMbPQQ2OAAAAAElFTkSuQmCC',
			'4BFE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpI37poiGsIYGhgYgi4WItLI2MDogq2MMEWl0RRNjnYKiDuykadOmhi0NXRmaheS+gCmY5oWGYprHMAWrGIZesJsbGFHdPFDhRz2IxX0AuM7Jfn3TIs8AAAAASUVORK5CYII=',
			'042F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB0YWhlCGUNDkMRYAximMjo6OiCrE5nCEMraEIgiFtDK6MqAEAM7KWrp0qWrVmaGZiG5L6BVpJWhlRFNr2iowxRGdDtaGQJQxYBuAepEFQO5mTUU1S0DFX5UhFjcBwA4hcgTnnVksgAAAABJRU5ErkJggg==',
			'B260' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7QgMYQxhCGVqRxQKmsLYyOjpMdUAWaxVpdG1wCAhAUccAFGN0EEFyX2jUqqVLp67MmobkPqC6KayOjjB1UPMYAlgbAtHEGB1YGwLQ7GBtQHdLaIBoqAOamwcq/KgIsbgPALvkzWnj4BMCAAAAAElFTkSuQmCC',
			'406E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpI37pjAEMIQyhgYgi4UwhjA6Ojogq2MMYW1lbUAVY50i0ujawAgTAztp2rRpK1OnrgzNQnJfAEgdmnmhoSC9gQ6obgHZgS6G6Rasbh6o8KMexOI+AGrRyVBm6Mv+AAAAAElFTkSuQmCC',
			'0527' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeUlEQVR4nM2QsQ3DMAwEn4U2YPahN2AAqckImoIuuIEyggtryqiLCLtMYPMBFgcQPDz6YQx3yl/8SB4FhUqeWFI2WsR4YtzYkmlg6pzHHvn6vbb31ve618lPHas4HOF2sIaG+GMVhSK4JCchic6UU3kGdlV/P8yJ3wfaYcrcWcWiQwAAAABJRU5ErkJggg==',
			'74E9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkMZWllDHaY6IIu2MkxlbWAICEAVC2VtYHQQQRabwuiKJAZxU9TSpUtDV0WFIbkPqKIVaN5UZL2sDaKhrkAaWQzIBqlDsSMAIobiFrAYupsHKPyoCLG4DwAVOMqyoyk4/QAAAABJRU5ErkJggg==',
			'B1E8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7QgMYAlhDHaY6IIkFTGEMYG1gCAhAFmtlBYoxOoigqGNAVgd2UmjUqqiloaumZiG5D00d1DwGTPOwiWHRGwp0MbqbByr8qAixuA8Ah8HK2h+zagUAAAAASUVORK5CYII=',
			'7C0F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QkMZQxmmMIaGIIu2sjY6hDI6oKhsFWlwdHREFZsi0sDaEAgTg7gpatqqpasiQ7OQ3MfogKIODFkbMMVEGjDtCGjAdEtAA9jNqG4ZoPCjIsTiPgBOS8nbG6X6OgAAAABJRU5ErkJggg==',
			'42DB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpI37pjCGsIYyhjogi4WwtrI2OjoEIIkxhog0ujYEOoggibFOYQCLBSC5b9q0VUuXrooMzUJyX8AUhimsCHVgGBrKEMCKZh7QLQ6YYqwN6G5hmCIa6oru5oEKP+pBLO4DAMhHy+4eDM8ZAAAAAElFTkSuQmCC',
			'B416' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QgMYWhmmMEx1QBILAPIZQhgCApDFWhlCGUMYHQRQ1DG6MkxhdEB2X2jU0qWrpq1MzUJyX8AUEaAdjGjmiYY6APWKoNoBUocqNgXsPhS9IDczhjqguHmgwo+KEIv7ADLczFoxheRXAAAAAElFTkSuQmCC',
			'E87F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDA0NDkMQCGlhbGRoCHRhQxEQaHTDEgOoaHWFiYCeFRq0MW7V0ZWgWkvvA6qYwYpoXgCnm6IAuxtrK2oAqBnYzmthAhR8VIRb3AQAMFMrvc5vhHAAAAABJRU5ErkJggg==',
			'A32C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7GB1YQxhCGaYGIImxBoi0Mjo6BIggiYlMYWh0bQh0YEESC2hlaGUAiiG7L2rpqrBVKzOzkN0HVtfK6IBsb2goQ6PDFFQxoLpGhwBGNDtEQDpR3BLQyhrCGhqA4uaBCj8qQizuAwB4qssLl10GWAAAAABJRU5ErkJggg==',
			'2AE6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WAMYAlhDHaY6IImJTGEMYW1gCAhAEgtoZW1lbWB0EEDW3SrS6AoUQ3HftGkrU0NXpmYhuy8ArA7FPEYH0VCQXhFktzRAzEMWEwGLobolNBQohubmgQo/KkIs7gMAqMrLR6zLTGgAAAAASUVORK5CYII=',
			'691C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WAMYQximMEwNQBITmcLayhDCECCCJBbQItLoGMLowIIs1iDS6DCF0QHZfZFRS5dmTVuZhey+kCmMgUjqIHpbGRoxxVjAYsh2gN0yBdUtIDczhjqguHmgwo+KEIv7APkGy0sLSVsmAAAAAElFTkSuQmCC',
			'10C0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7GB0YAhhCHVqRxVgdGEMYHQKmOiCJiTqwtrI2CAQEoOgVaXQFkUjuW5k1bWUqiERyH5o6PGLY7MDilhBMNw9U+FERYnEfAMJqyLO6wYw3AAAAAElFTkSuQmCC',
			'FD28' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QkNFQxhCGaY6IIkFNIi0Mjo6BASgijW6NgQ6iKCJOTQEwNSBnRQaNW1l1sqsqVlI7gOra2XAMM9hCiOmeQEYYq2MDuh6RUNYQwNQ3DxQ4UdFiMV9AIPWzgUtHe5xAAAAAElFTkSuQmCC',
			'D73D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QgNEQx1DGUMdkMQCpjA0ujY6OgQgi7UyNDo0BDqIoIoBRR1hYmAnRS1dNW3V1JVZ05DcB1QXgKQOKsbowIBhHmsDhtgUkQZWNLeEBog0MKK5eaDCj4oQi/sATeTNvXgrDGYAAAAASUVORK5CYII=',
			'7CF6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkMZQ1lDA6Y6IIu2sja6NjAEBKCIiTS4NjA6CCCLTRFpYAWKobgvatqqpaErU7OQ3MfoAFaHYh5rA0SvCJKYSAPEDmSxgAZMtwQ0AN3cwIDq5gEKPypCLO4DADEcy3jfMvUzAAAAAElFTkSuQmCC',
			'0B0C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7GB1EQximMEwNQBJjDRBpZQhlCBBBEhOZItLo6OjowIIkFtAq0sraEOiA7L6opVPDlq6KzEJ2H5o6mFijK5oYNjuwuQWbmwcq/KgIsbgPAHGqytUKBd+ZAAAAAElFTkSuQmCC',
			'B92C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7QgMYQxhCGaYGIIkFTGFtZXR0CBBBFmsVaXRtCHRgQVEn0ugAFEN2X2jU0qVZKzOzkN0XMIUx0KGV0YEBxTyGRocp6GIsjQ4BjGh2sIJ0orgF5GbW0AAUNw9U+FERYnEfAIcXzFwpKVhdAAAAAElFTkSuQmCC',
			'7390' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkNZQxhCGVpRRFtFWhkdHaY6oIgxNLo2BAQEIItNYWhlbQh0EEF2X9SqsJWZkVnTkNzH6ADUHQJXB4asDQyNDg2oYkB2oyOaHQENmG4JaMDi5gEKPypCLO4DAHO1y735wzHAAAAAAElFTkSuQmCC',
			'385B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7RAMYQ1hDHUMdkMQCprC2sjYwOgQgq2wVaXQFiokgi4HUTYWrAztpZdTKsKWZmaFZyO4DqmNoCMQwzwEoJoJhB6oYyC2Mjo4oekFuZghlRHHzQIUfFSEW9wEAx5/LFpZ6ZEoAAAAASUVORK5CYII=',
			'6418' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WAMYWhmmMEx1QBITAfIZQhgCApDEAloYQhlDGB1EkMUaGF2BemHqwE6KjFq6dNW0VVOzkNwXMkWkFUkdRG+raKjDFDTzWkFuQRUDugVDL8jNjKEOKG4eqPCjIsTiPgCBFMvIzPqD7wAAAABJRU5ErkJggg==',
			'2F53' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WANEQ11DHUIdkMREpog0sDYwOgQgiQW0gsSAcsi6QWJTgXLI7ps2NWxpZtbSLGT3BYB0BTQgm8foABFDNo+1AWQHqpgIEDI6OqK4JTQUqCKUAcXNAxV+VIRY3AcAJr3MJHwDfYgAAAAASUVORK5CYII=',
			'3BA3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7RANEQximMIQ6IIkFTBFpZQhldAhAVtkq0ujo6NAggiwGVMfaENAQgOS+lVFTw5auilqahew+VHVw81xDA1DNA4k1oIoFgPUGorgF5GageShuHqjwoyLE4j4A67LN1U9ADJoAAAAASUVORK5CYII=',
			'53B0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QkNYQ1hDGVqRxQIaRFpZGx2mOqCIMTS6NgQEBCCJBQYwANU5OogguS9s2qqwpaErs6Yhu68VRR1MDGheIIpYQCumHSJTMN3CGoDp5oEKPypCLO4DAGLdzQ+fo7C2AAAAAElFTkSuQmCC',
			'A904' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB0YQximMDQEIImxBrC2MoQyNCKLiUwRaXR0dGhFFgtoFWl0bQiYEoDkvqilS5emroqKikJyX0ArY6BrQ6ADst5QoPlAsdAQFPNYQHY0oNoBdguaGKabByr8qAixuA8A2F/OnDrPLK8AAAAASUVORK5CYII=',
			'0827' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUNDkMRYA1hbGR0dGkSQxESmiDS6NgSgiAW0srYCSSBEuC9q6cqwVSuzVmYhuQ+sDgRR9Io0OkxhmMKAZodDAEMAA7pbHBgd0N3MGhqIIjZQ4UdFiMV9AL3SysLBAG45AAAAAElFTkSuQmCC',
			'B55A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QgNEQ1lDHVqRxQKmiDSwNjBMdUAWawWLBQSgqgthncroIILkvtCoqUuXZmZmTUNyX8AUhkaHhkCYOqh5YLHQEFQ7Gl3R1U1hbWV0dEQRCw1gDGEIZUQRG6jwoyLE4j4AbTXNCw7gCrgAAAAASUVORK5CYII=',
			'2DE6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7WANEQ1hDHaY6IImJTBFpZW1gCAhAEgtoFWl0bWB0EEDWDRVDcd+0aStTQ1emZiG7LwCsDsU8RgeIXhFktzRgiok0YLolNBTTzQMVflSEWNwHADqZy3JD3RkHAAAAAElFTkSuQmCC',
			'FD33' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAUUlEQVR4nGNYhQEaGAYTpIn7QkNFQxhDGUIdkMQCGkRaWRsdHQJQxRodQCS6GFgU4b7QqGkrs6auWpqF5D40dfjNwxTD4hZMNw9U+FERYnEfAP+20Bp3MRCRAAAAAElFTkSuQmCC',
			'66EB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDHUMdkMREprC2sjYwOgQgiQW0iDSCxESQxRpEGpDUgZ0UGTUtbGnoytAsJPeFTBHFNK9VpNEV3TwsYtjcgs3NAxV+VIRY3AcAhA/K7cjxCUUAAAAASUVORK5CYII=',
			'61C7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WAMYAhhCHUNDkMREpjAGMDoENIggiQW0sAawNgigijUwAMXANNx9kVGropauWrUyC8l9IVPA6lqR7Q1oBYtNwRQTCGBAcQsD0C2BDqhuZg0FuhlFbKDCj4oQi/sAWebJsN+C4Z8AAAAASUVORK5CYII=',
			'212B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGUMdkMREpjAGMDo6OgQgiQW0sgawNgQ6iCDrbgXqBYoFILtv2qqoVSszQ7OQ3Qeyo5URxTxGB6DYFEYU81jBKlHFgGygCKre0FBWIAxEcfNAhR8VIRb3AQDVYcfUH5fMHAAAAABJRU5ErkJggg==',
			'AF5B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7GB1EQ11DHUMdkMRYA0QaWIEyAUhiIlMgYiJIYgGtQLGpcHVgJ0UtnRq2NDMzNAvJfSB1DA2BKOaFhkLEMMzDIsbo6IiiF2xeKCOKmwcq/KgIsbgPAE7yy79EsES4AAAAAElFTkSuQmCC',
			'CD70' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WENEQ1hDA1qRxURaRYD8gKkOSGIBjSKNDg0BAQHIYg1AsUZHBxEk90WtmrYya+nKrGlI7gOrm8IIU4cQC0ATA9rh6MCAYgfILawNDChuAbu5gQHFzQMVflSEWNwHADf8zZY7wqY6AAAAAElFTkSuQmCC',
			'2CA1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WAMYQxmmMLQii4lMYW10CGWYiiwW0CrS4OgIFEXWDRRjBcqguG/atFVLV0UtRXFfAIo6MGR0AIqFooqxNog0uKKpA6pqRBcLDWUMBZoXGjAIwo+KEIv7ANEJzPVm0uUKAAAAAElFTkSuQmCC',
			'B7D5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nM2QsQ3AIAwETcEGZB+noDeFizCNKdiAZIM0TBmXRqRMJPzd6fU6Gfp0AivlFz+mjSM7JsOoQYllR9ujqkzSyBpULymi8ePcr7sfORs/7ZEXkjDsOZyZF93DgbUgviBZPyZlDCcu8L8P8+L3AEhrzefDj7U4AAAAAElFTkSuQmCC',
			'3AE4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7RAMYAlhDHRoCkMQCpjCGsDYwNCKLMbSytgLFWlHEpog0ugLJACT3rYyatjI1dFVUFLL7wOoYHVDNEw0FioWGoIiBzUNzC6aYaABQDM3NAxV+VIRY3AcA0RPNzMsHrCIAAAAASUVORK5CYII=',
			'7ECB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QkNFQxlCHUMdkEVbRRoYHQIdAtDEWBsEHUSQxaaAxBhh6iBuipoatnTVytAsJPcxOqCoA0PWBogYsnkiDZh2BDRguiWgAYubByj8qAixuA8AAIXKh14B+RAAAAAASUVORK5CYII=',
			'8D6A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WANEQxhCGVqRxUSmiLQyOjpMdUASC2gVaXRtcAgIQFUHFGN0EEFy39KoaStTp67MmobkPrA6R0eYOiTzAkNDMMVQ1EHcgqoX4mZGFLGBCj8qQizuAwDXU8yXXzpOTgAAAABJRU5ErkJggg==',
			'7F7F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7QkNFQ11DA0NDkEVbRYBkoAMDIbEpQLFGR5gYxE1RU8NWLV0ZmoXkPkYHoLopjCh6WRuAYgGoYiJAyOiAKhYAFGNtICw2UOFHRYjFfQAUq8lpQV5+nAAAAABJRU5ErkJggg==',
			'0BED' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDHUMdkMRYA0RaWYEyAUhiIlNEGl2BYiJIYgGtEHUiSO6LWjo1bGnoyqxpSO5DUwcTwzAPmx3Y3ILNzQMVflSEWNwHANTQylboQ8qWAAAAAElFTkSuQmCC',
			'1227' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUNDkMRYHVhbGR0dGkSQxEQdRBpdGwJQxBgdGBodgGIBSO5bmbVqKZAAUgj3AdVNYWgFQlS9AUDRKWhuAYkGoIqxQsSR3RIiGuoaGogiNlDhR0WIxX0AkL/ILPhG/kMAAAAASUVORK5CYII=',
			'03B0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7GB1YQ1hDGVqRxVgDRFpZGx2mOiCJiUxhaHRtCAgIQBILaGUAqnN0EEFyX9TSVWFLQ1dmTUNyH5o6mBjQvEAUMWx2YHMLNjcPVPhREWJxHwBM98xXw5g9qAAAAABJRU5ErkJggg==',
			'C647' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WEMYQxgaHUNDkMREWllbGVodGkSQxAIaRRoZpqKJgXiBDkAa4b6oVdPCVmZmrcxCcl9Ag2gra6NDKwOq3kbX0IApDGh2ODQ6BDCgu6XR0QGLm1HEBir8qAixuA8AhQnNF/54iuIAAAAASUVORK5CYII=',
			'C2A4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nM2QsQ3AIAwETeENyD5Q0BvJNJ7GFGzACjRMGUogKRMl/u5elk+GfhmFP+UVP2TDUEFpYrZggQR5ZpRt9t6VhSnkoFRp8pPeW+siMvmNvqJGt+0Spph4uWEcjmZz0Z0hHyls7Kv/PZgbvxNwYc79YhZo/gAAAABJRU5ErkJggg==',
			'69DD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDGUMdkMREprC2sjY6OgQgiQW0iDS6NgQ6iCCLNaCIgZ0UGbV0aeqqyKxpSO4LmcIYiKG3lQHTvFYWDDFsbsHm5oEKPypCLO4DAN/PzMBoNKOSAAAAAElFTkSuQmCC',
			'8154' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WAMYAlhDHRoCkMREpjAGsDYwNCKLBbSygsRaUdUB9U5lmBKA5L6lUauilmZmRUUhuQ+kjqEh0AHVPLBYaAiaGCvQJeh2MDqiug/oklCGUAYUsYEKPypCLO4DAIPey7LeUWOAAAAAAElFTkSuQmCC',
			'DE58' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QgNEQ1lDHaY6IIkFTBFpYG1gCAhAFmsFiTE6iKCLTYWrAzspaunUsKWZWVOzkNwHUgckMcxjaAjENA9dDOgWRkcHFL0gNzOEMqC4eaDCj4oQi/sA0MzNeowro1QAAAAASUVORK5CYII=',
			'9220' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeklEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGVqRxUSmsLYyOjpMdUASC2gVaXRtCAgIQBFjaHRoCHQQQXLftKmrlq5amZk1Dcl9rK4MUxhaGWHqILCVIYBhCqqYAFANUBTFDqBbGoCiKG5hDRANdQ0NQHHzQIUfFSEW9wEAC57LFTKUyFkAAAAASUVORK5CYII=',
			'9112' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WAMYAhimMEx1QBITmcIYwBDCEBCAJBbQyhrAGMLoIIIiBtbbIILkvmlTV0WtmrYKSCDcx+oKVteIbAcDRG8rslsEIGJTGFDcAhYLQHUzayhjqGNoyCAIPypCLO4DAIrpyTmk4EddAAAAAElFTkSuQmCC',
			'70CF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QkMZAhhCHUNDkEVbGUMYHQIdUFS2srayNgiiik0RaXRtYISJQdwUNW1l6qqVoVlI7mN0QFEHhqwNmGIiDZh2BDRguiWgAexmVLcMUPhREWJxHwAOqsjbM721nwAAAABJRU5ErkJggg==',
			'6807' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WAMYQximMIaGIImJTGFtZQgF0khiAS0ijY6ODqhiDaytrEAyAMl9kVErw5auilqZheS+kClgda3I9ga0ijS6NgRMQRcD2hHAgOEWRgcsbkYRG6jwoyLE4j4AU9/MHEW3NYYAAAAASUVORK5CYII=',
			'693D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WAMYQxhDGUMdkMREprC2sjY6OgQgiQW0iDQ6NAQ6iCCLNQDFgOpEkNwXGbV0adbUlVnTkNwXMoUxEEkdRG8rA6Z5rSwYYtjcgs3NAxV+VIRY3AcA4kjMwahA1XgAAAAASUVORK5CYII=',
			'2AAE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WAMYAhimMIYGIImJTGEMYQhldEBWF9DK2sro6IgixtAq0ujaEAgTg7hp2rSVqasiQ7OQ3ReAog4MGR1EQ11DUcVYGzDViWARCw0Fi6G4eaDCj4oQi/sA/JXK73E+EFMAAAAASUVORK5CYII=',
			'7443' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkMZWhkaHUIdkEVbGaYytDo6BKCKhTJMdWgQQRabwujKEOjQEIDsvqilS1dmZi3NQnIfo4NIK2sjXB0YsjaIhrqGBqCYJ9IAdguKWABYDNUtEDE0Nw9Q+FERYnEfAD/EzTR7Gs7HAAAAAElFTkSuQmCC',
			'B43B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QgMYWhlDGUMdkMQCpjBMZW10dAhAFmtlCGVoCHQQQVHH6MqAUAd2UmjU0qWrpq4MzUJyX8AUkVYGDPNEgXaimdfK0IppB0MruluwuXmgwo+KEIv7AKJXzWQMsG3DAAAAAElFTkSuQmCC',
			'DB57' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QgNEQ1hDHUNDkMQCpoi0sgJpEWSxVpFGV0yxVtapQBrJfVFLp4YtzcxamYXkPpA6EMmAZp4D0CZ0MdeGgAAGNLcwOjo6oLuZIZQRRWygwo+KEIv7AHW2zcfrkY1zAAAAAElFTkSuQmCC',
			'36CA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7RAMYQxhCHVqRxQKmsLYyOgRMdUBW2SrSyNogEBCALDZFpIG1gdFBBMl9K6OmhS1dtTJrGrL7poi2IqmDm+fawBgagiEmiKIO4pZAFDGImx1RzRug8KMixOI+ANDfyviFrkOVAAAAAElFTkSuQmCC',
			'CED0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7WENEQ1lDGVqRxURaRRpYGx2mOiCJBTQCxRoCAgKQxRpAYoEOIkjui1o1NWzpqsisaUjuQ1OHWwyLHdjcgs3NAxV+VIRY3AcARRzNFN4io9MAAAAASUVORK5CYII=',
			'B6B5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDGUMDkMQCprC2sjY6OiCrC2gVaWRtCEQVmyLSAFTn6oDkvtCoaWFLQ1dGRSG5L2CKKNA8hwYRNPNcGwKwiAU6iGC4xSEA2X0QNzNMdRgE4UdFiMV9ALtvzazXUQWkAAAAAElFTkSuQmCC',
			'2D17' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WANEQximMIaGIImJTBFpZQgB0khiAa0ijY5oYgxAMYcpQDlk902btjJr2qqVWcjuCwCra0W2l9EBLDYFxS0NYLEAZDGRBqBbpjA6IIuFhoqGMIY6oogNVPhREWJxHwCzAcujAuzw/wAAAABJRU5ErkJggg==',
			'19DA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDGVqRxVgdWFtZGx2mOiCJiTqINLo2BAQEoOgFiQU6iCC5b2XW0qWpqyKzpiG5D2hHIJI6qBgDSG9oCIoYSyOmOpBbHFHERENAbmZEERuo8KMixOI+AGERyc9C23FoAAAAAElFTkSuQmCC',
			'7D3F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QkNFQxhDGUNDkEVbRVpZGx0dGFDFGh0aAlHFpgDFEOogboqatjJr6srQLCT3MTqgqAND1gZM80SwiAU0YLoloAHsZlS3DFD4URFicR8AB8zLQKpgz2YAAAAASUVORK5CYII=',
			'BFEB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAUElEQVR4nGNYhQEaGAYTpIn7QgNEQ11DHUMdkMQCpog0sDYwOgQgi7VCxERwqwM7KTRqatjS0JWhWUjuI9o8wnZA3QwUQ3PzQIUfFSEW9wEAO6PMMFzIJGIAAAAASUVORK5CYII=',
			'114B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7GB0YAhgaHUMdkMRYHRgDGFodHQKQxEQdWAMYpjo6iKDrDYSrAztpZdaqqJWZmaFZSO4DqWNtRDUPLBYaiGleIxY70PSKhrCGort5oMKPihCL+wC4fscTBrlAggAAAABJRU5ErkJggg==',
			'6A8E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGUMDkMREpjCGMDo6OiCrC2hhbWVtCEQVaxBpdESoAzspMmrayqzQlaFZSO4LmYKiDqK3VTTUFd28VpFGdDERLHpZA0QaHdDcPFDhR0WIxX0AGfHK2oUSuSoAAAAASUVORK5CYII=',
			'0FB3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7GB1EQ11DGUIdkMRYA0QaWBsdHQKQxESmAMUaAhpEkMQCWkHqHBoCkNwXtXRq2NLQVUuzkNyHpg4hhmYeNjuwuYXRASiG5uaBCj8qQizuAwBWq80gaXZMiwAAAABJRU5ErkJggg==',
			'96FC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDA6YGIImJTGFtZW1gCBBBEgtoFWlkbWB0YEEVawCJIbtv2tRpYUtDV2Yhu4/VVbQVSR0EAs1zRRMTgIoh24HNLWA3NzCguHmgwo+KEIv7AIUMyf2+c3R+AAAAAElFTkSuQmCC',
			'C093' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WEMYAhhCGUIdkMREWhlDGB0dHQKQxAIaWVtZGwIaRJDFGkQaXYFkAJL7olZNW5mZGbU0C8l9IHUOIXB1CDF084B2MKKJYXMLNjcPVPhREWJxHwAEqMzhYp3AIgAAAABJRU5ErkJggg==',
			'19ED' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDHUMdkMRYHVhbWYEyAUhiog4ija5AMREUvShiYCetzFq6NDV0ZdY0JPcB7QjE1MuAxTwWLGJY3BKC6eaBCj8qQizuAwDGosfi2BYZ+AAAAABJRU5ErkJggg==',
			'6E41' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7WANEQxkaHVqRxUSmiDQwtDpMRRYLaAGKTXUIRRFrAIoFwvWCnRQZNTVsZWbWUmT3hQDNY0WzI6AVKBYagCGG1S1oYlA3hwYMgvCjIsTiPgAtPs0QCokLewAAAABJRU5ErkJggg==',
			'3E38' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7RANEQxlDGaY6IIkFTBFpYG10CAhAVtkqAiQDHUSQxYDqGBDqwE5aGTU1bNXUVVOzkN2Hqg63eVjEsLkFm5sHKvyoCLG4DwC3qsydRHMcOAAAAABJRU5ErkJggg==',
			'449D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpI37pjC0MoQyhjogi4UwTGV0dHQIQBJjDGEIZW0IdBBBEmOdwuiKJAZ20rRpS5euzIzMmobkvoApIq0MIah6Q0NFgXaiioHcwohNDM0tWN08UOFHPYjFfQDBcspnUcXJHAAAAABJRU5ErkJggg==',
			'59A5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeElEQVR4nGNYhQEaGAYTpIn7QkMYQximMIYGIIkFNLC2MoQyOjCgiIk0Ojo6oogFBog0ujYEujoguS9s2tKlqasio6KQ3dfKGOgKMgHZ5laGRtdQVLGAVhaQeQ7IYiJTWFtZGwICkN3HGsAYAhSb6jAIwo+KEIv7AJbezMHGXTjOAAAAAElFTkSuQmCC'        
        );
        $this->text = array_rand( $images );
        return $images[ $this->text ] ;    
    }
    
    function out_processing_gif(){
        $image = dirname(__FILE__) . '/processing.gif';
        $base64_image = "R0lGODlhFAAUALMIAPh2AP+TMsZiALlcAKNOAOp4ANVqAP+PFv///wAAAAAAAAAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQFCgAIACwAAAAAFAAUAAAEUxDJSau9iBDMtebTMEjehgTBJYqkiaLWOlZvGs8WDO6UIPCHw8TnAwWDEuKPcxQml0Ynj2cwYACAS7VqwWItWyuiUJB4s2AxmWxGg9bl6YQtl0cAACH5BAUKAAgALAEAAQASABIAAAROEMkpx6A4W5upENUmEQT2feFIltMJYivbvhnZ3Z1h4FMQIDodz+cL7nDEn5CH8DGZhcLtcMBEoxkqlXKVIgAAibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkphaA4W5upMdUmDQP2feFIltMJYivbvhnZ3V1R4BNBIDodz+cL7nDEn5CH8DGZAMAtEMBEoxkqlXKVIg4HibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpjaE4W5tpKdUmCQL2feFIltMJYivbvhnZ3R0A4NMwIDodz+cL7nDEn5CH8DGZh8ONQMBEoxkqlXKVIgIBibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpS6E4W5spANUmGQb2feFIltMJYivbvhnZ3d1x4JMgIDodz+cL7nDEn5CH8DGZgcBtMMBEoxkqlXKVIggEibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpAaA4W5vpOdUmFQX2feFIltMJYivbvhnZ3V0Q4JNhIDodz+cL7nDEn5CH8DGZBMJNIMBEoxkqlXKVIgYDibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpz6E4W5tpCNUmAQD2feFIltMJYivbvhnZ3R1B4FNRIDodz+cL7nDEn5CH8DGZg8HNYMBEoxkqlXKVIgQCibbK9YLBYvLtHH5K0J0IACH5BAkKAAgALAEAAQASABIAAAROEMkpQ6A4W5spIdUmHQf2feFIltMJYivbvhnZ3d0w4BMAIDodz+cL7nDEn5CH8DGZAsGtUMBEoxkqlXKVIgwGibbK9YLBYvLtHH5K0J0IADs=";
        $binary = is_file($image) ? join("",file($image)) : base64_decode($base64_image); 
        header("Cache-Control: post-check=0, pre-check=0, max-age=0, no-store, no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: image/gif");
        echo $binary;
    }

}
# end of class phpfmgImage
# ------------------------------------------------------
# end of module : captcha


# module user
# ------------------------------------------------------
function phpfmg_user_isLogin(){
    return ( isset($_SESSION['authenticated']) && true === $_SESSION['authenticated'] );
}


function phpfmg_user_logout(){
    session_destroy();
    header("Location: admin.php");
}

function phpfmg_user_login()
{
    if( phpfmg_user_isLogin() ){
        return true ;
    };
    
    $sErr = "" ;
    if( 'Y' == $_POST['formmail_submit'] ){
        if(
            defined( 'PHPFMG_USER' ) && strtolower(PHPFMG_USER) == strtolower($_POST['Username']) &&
            defined( 'PHPFMG_PW' )   && strtolower(PHPFMG_PW) == strtolower($_POST['Password']) 
        ){
             $_SESSION['authenticated'] = true ;
             return true ;
             
        }else{
            $sErr = 'Login failed. Please try again.';
        }
    };
    
    // show login form 
    phpfmg_admin_header();
?>
<form name="frmFormMail" action="" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:380px;height:260px;">
<fieldset style="padding:18px;" >
<table cellspacing='3' cellpadding='3' border='0' >
	<tr>
		<td class="form_field" valign='top' align='right'>Email :</td>
		<td class="form_text">
            <input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" class='text_box' >
		</td>
	</tr>

	<tr>
		<td class="form_field" valign='top' align='right'>Password :</td>
		<td class="form_text">
            <input type="password" name="Password"  value="" class='text_box'>
		</td>
	</tr>

	<tr><td colspan=3 align='center'>
        <input type='submit' value='Login'><br><br>
        <?php if( $sErr ) echo "<span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
        <a href="admin.php?mod=mail&func=request_password">I forgot my password</a>   
    </td></tr>
</table>
</fieldset>
</div>
<script type="text/javascript">
    document.frmFormMail.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();
}


function phpfmg_mail_request_password(){
    $sErr = '';
    if( $_POST['formmail_submit'] == 'Y' ){
        if( strtoupper(trim($_POST['Username'])) == strtoupper(trim(PHPFMG_USER)) ){
            phpfmg_mail_password();
            exit;
        }else{
            $sErr = "Failed to verify your email.";
        };
    };
    
    $n1 = strpos(PHPFMG_USER,'@');
    $n2 = strrpos(PHPFMG_USER,'.');
    $email = substr(PHPFMG_USER,0,1) . str_repeat('*',$n1-1) . 
            '@' . substr(PHPFMG_USER,$n1+1,1) . str_repeat('*',$n2-$n1-2) . 
            '.' . substr(PHPFMG_USER,$n2+1,1) . str_repeat('*',strlen(PHPFMG_USER)-$n2-2) ;


    phpfmg_admin_header("Request Password of Email Form Admin Panel");
?>
<form name="frmRequestPassword" action="admin.php?mod=mail&func=request_password" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:580px;height:260px;text-align:left;">
<fieldset style="padding:18px;" >
<legend>Request Password</legend>
Enter Email Address <b><?php echo strtoupper($email) ;?></b>:<br />
<input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" style="width:380px;">
<input type='submit' value='Verify'><br>
The password will be sent to this email address. 
<?php if( $sErr ) echo "<br /><br /><span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
</fieldset>
</div>
<script type="text/javascript">
    document.frmRequestPassword.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();    
}


function phpfmg_mail_password(){
    phpfmg_admin_header();
    if( defined( 'PHPFMG_USER' ) && defined( 'PHPFMG_PW' ) ){
        $body = "Here is the password for your form admin panel:\n\nUsername: " . PHPFMG_USER . "\nPassword: " . PHPFMG_PW . "\n\n" ;
        if( 'html' == PHPFMG_MAIL_TYPE )
            $body = nl2br($body);
        mailAttachments( PHPFMG_USER, "Password for Your Form Admin Panel", $body, PHPFMG_USER, 'You', "You <" . PHPFMG_USER . ">" );
        echo "<center>Your password has been sent.<br><br><a href='admin.php'>Click here to login again</a></center>";
    };   
    phpfmg_admin_footer();
}


function phpfmg_writable_check(){
 
    if( is_writable( dirname(PHPFMG_SAVE_FILE) ) && is_writable( dirname(PHPFMG_EMAILS_LOGFILE) )  ){
        return ;
    };
?>
<style type="text/css">
    .fmg_warning{
        background-color: #F4F6E5;
        border: 1px dashed #ff0000;
        padding: 16px;
        color : black;
        margin: 10px;
        line-height: 180%;
        width:80%;
    }
    
    .fmg_warning_title{
        font-weight: bold;
    }

</style>
<br><br>
<div class="fmg_warning">
    <div class="fmg_warning_title">Your form data or email traffic log is NOT saving.</div>
    The form data (<?php echo PHPFMG_SAVE_FILE ?>) and email traffic log (<?php echo PHPFMG_EMAILS_LOGFILE?>) will be created automatically when the form is submitted. 
    However, the script doesn't have writable permission to create those files. In order to save your valuable information, please set the directory to writable.
     If you don't know how to do it, please ask for help from your web Administrator or Technical Support of your hosting company.   
</div>
<br><br>
<?php
}


function phpfmg_log_view(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    
    phpfmg_admin_header();
   
    $file = $files[$n];
    if( is_file($file) ){
        if( 1== $n ){
            echo "<pre>\n";
            echo join("",file($file) );
            echo "</pre>\n";
        }else{
            $man = new phpfmgDataManager();
            $man->displayRecords();
        };
     

    }else{
        echo "<b>No form data found.</b>";
    };
    phpfmg_admin_footer();
}


function phpfmg_log_download(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );

    $file = $files[$n];
    if( is_file($file) ){
        phpfmg_util_download( $file, PHPFMG_SAVE_FILE == $file ? 'form-data.csv' : 'email-traffics.txt', true, 1 ); // skip the first line
    }else{
        phpfmg_admin_header();
        echo "<b>No email traffic log found.</b>";
        phpfmg_admin_footer();
    };

}


function phpfmg_log_delete(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    phpfmg_admin_header();

    $file = $files[$n];
    if( is_file($file) ){
        echo unlink($file) ? "It has been deleted!" : "Failed to delete!" ;
    };
    phpfmg_admin_footer();
}


function phpfmg_util_download($file, $filename='', $toCSV = false, $skipN = 0 ){
    if (!is_file($file)) return false ;

    set_time_limit(0);


    $buffer = "";
    $i = 0 ;
    $fp = @fopen($file, 'rb');
    while( !feof($fp)) { 
        $i ++ ;
        $line = fgets($fp);
        if($i > $skipN){ // skip lines
            if( $toCSV ){ 
              $line = str_replace( chr(0x09), ',', $line );
              $buffer .= phpfmg_data2record( $line, false );
            }else{
                $buffer .= $line;
            };
        }; 
    }; 
    fclose ($fp);
  

    
    /*
        If the Content-Length is NOT THE SAME SIZE as the real conent output, Windows+IIS might be hung!!
    */
    $len = strlen($buffer);
    $filename = basename( '' == $filename ? $file : $filename );
    $file_extension = strtolower(substr(strrchr($filename,"."),1));

    switch( $file_extension ) {
        case "pdf": $ctype="application/pdf"; break;
        case "exe": $ctype="application/octet-stream"; break;
        case "zip": $ctype="application/zip"; break;
        case "doc": $ctype="application/msword"; break;
        case "xls": $ctype="application/vnd.ms-excel"; break;
        case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
        case "gif": $ctype="image/gif"; break;
        case "png": $ctype="image/png"; break;
        case "jpeg":
        case "jpg": $ctype="image/jpg"; break;
        case "mp3": $ctype="audio/mpeg"; break;
        case "wav": $ctype="audio/x-wav"; break;
        case "mpeg":
        case "mpg":
        case "mpe": $ctype="video/mpeg"; break;
        case "mov": $ctype="video/quicktime"; break;
        case "avi": $ctype="video/x-msvideo"; break;
        //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
        case "php":
        case "htm":
        case "html": 
                $ctype="text/plain"; break;
        default: 
            $ctype="application/x-download";
    }
                                            

    //Begin writing headers
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public"); 
    header("Content-Description: File Transfer");
    //Use the switch-generated Content-Type
    header("Content-Type: $ctype");
    //Force the download
    header("Content-Disposition: attachment; filename=".$filename.";" );
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$len);
    
    while (@ob_end_clean()); // no output buffering !
    flush();
    echo $buffer ;
    
    return true;
 
    
}
?>