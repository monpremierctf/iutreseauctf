<?php
    // Forked from: https://wiki.owasp.org/index.php/PHP_CSRF_Guard
    //
    // Usage:
    // Just add this line at the top of you .php file
    /* <?php require_once 'csrfguard.php'; ?> */
    // Output is redirected to a buffer, 
    // Invisible token fied is added to each <form>
    // Auto process of $_POST data to validate token.


$CSRFGUARD_MAXTOKENINSESSION=5; // Max opened tab in browser with Form 
$CSRFGUARD_DEBUG=false;
$CSRFGUARD_THROWEXCEPTION=true;
$CSRFGUARD_MSG_INVALIDTOKEN=""; //"Invalid CSRF token."
$CSRFGUARD_MSG_VALIDTOKEN=""; //"Valid CSRF token."

function store_in_session($key, $value)
{
    global $CSRFGUARD_MAXTOKENINSESSION;
    if (isset($_SESSION)) {
        if (!isset($_SESSION['csrftokens'] )){
            $_SESSION['csrftokens']=[];
        }
        if (count($_SESSION['csrftokens'] )>$CSRFGUARD_MAXTOKENINSESSION){
            array_shift($_SESSION['csrftokens']);
        }
        $_SESSION['csrftokens'][$key] = $value;
    }
}
function unset_session($key)
{
    $_SESSION['csrftokens'][$key] = ' ';
    unset($_SESSION['csrftokens'][$key]);
}

function get_from_session($key)
{

    if (isset($_SESSION['csrftokens'][$key])) {
        return $_SESSION['csrftokens'][$key];
    } else {
        return false;
    }
}

function csrfguard_generate_token($unique_form_name)
{
    $token = base64_encode(random_bytes(64)); // PHP 7, or via paragonie/random_compat
    store_in_session($unique_form_name, $token);
    return $token;
}

function csrfguard_validate_token($unique_form_name, $token_value)
{
    global $CSRFGUARD_DEBUG;
    if ($CSRFGUARD_DEBUG) { 
        echo ("Validate token<br/>"); 
        var_dump($_SESSION);
        echo ("token name=$unique_form_name<br/>");
    }
    $token = get_from_session($unique_form_name);
    if ($CSRFGUARD_DEBUG) { 
        echo ("token =$token<br/>");
    }
    if (!is_string($token_value)) {
        return false;
    }
    if ($CSRFGUARD_DEBUG) { 
        var_dump($token);
        var_dump($token_value);
    }
    $result = hash_equals($token, $token_value);
    unset_session($unique_form_name);
    return $result;
}

function csrfguard_replace_forms($form_data_html)
{
    $count = preg_match_all("/<form(.*?)>(.*?)<\\/form>/is", $form_data_html, $matches, PREG_SET_ORDER);
    if (is_array($matches)) {
        foreach ($matches as $m) {
            if (strpos($m[1], "nocsrf") !== false) {
                continue;
            }
            $name = "CSRFGuard_" . mt_rand(0, mt_getrandmax());
            $token = csrfguard_generate_token($name);
            $form_data_html = str_replace(
                $m[0],
                "<form{$m[1]}>
<input type='hidden' name='CSRFName' value='{$name}' />
<input type='hidden' name='CSRFToken' value='{$token}' />{$m[2]}</form>",
                $form_data_html
            );
        }
    }
    return $form_data_html;
}

function csrfguard_inject()
{
    $data = ob_get_clean();
    $data = csrfguard_replace_forms($data);
    echo $data;
}
function csrfguard_start()
{
    global $CSRFGUARD_THROWEXCEPTION;
    global $CSRFGUARD_MSG_INVALIDTOKEN;
    global $CSRFGUARD_MSG_VALIDTOKEN;

    if (count($_POST)) {
        if (!isset($_POST['CSRFName']) or !isset($_POST['CSRFToken'])) {
            trigger_error("No CSRFName found, probable invalid request.", E_USER_ERROR);
        }
        $name = $_POST['CSRFName'];
        $token = $_POST['CSRFToken'];
        if (!csrfguard_validate_token($name, $token)) {
            if ($CSRFGUARD_THROWEXCEPTION) {
                throw new Exception("Invalid CSRF token.");
            }
            if ($CSRFGUARD_MSG_INVALIDTOKEN != "") { echo $CSRFGUARD_MSG_INVALIDTOKEN; }
        } else {
            if ($CSRFGUARD_MSG_VALIDTOKEN != "") { echo $CSRFGUARD_MSG_VALIDTOKEN; }
        }
    }
    ob_start();
    /* adding double quotes for "csrfguard_inject" to prevent:
          Notice: Use of undefined constant csrfguard_inject - assumed 'csrfguard_inject' */
    register_shutdown_function("csrfguard_inject");
}

csrfguard_start();
