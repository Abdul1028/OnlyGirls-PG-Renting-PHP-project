
<!DOCTYPE HTML>
<HTML>
<?PHP
session_start();
session_destroy();
if (isset($_SESSION["uname"])) {
    echo "hello";
}
header("Location: \\frontend\\index.php");
?>

</HTML>