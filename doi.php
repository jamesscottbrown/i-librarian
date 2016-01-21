<?php

include_once 'data.php';
include_once 'functions.php';

$baseURL = 'http://' . $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'])[0];

// If DOI not in URL, redirect to I, Librarian index page
if (array_key_exists('doi', $_GET)){
    $doi = $_GET["doi"];
} else {
    $url =  str_replace('doi.php', '', $baseURL);
    exit('<script type="text/javascript">window.location ="' . $url  . '"</script>;');
}

// If URL includes ?redirect=false, redirect to publisher regardless of whether paper is in library
if (array_key_exists('redirect', $_GET) and strtolower($_GET["redirect"]) != "false"){
    redirectToPublisher($doi);
}

// Otherwise redirect to the pdfviewer if paper is in library, and publisher site if not
database_connect(IL_DATABASE_PATH, 'library');
$stmt = $dbHandle->prepare("SELECT ID FROM library WHERE DOI like ?");
$stmt->bindValue(1, "$doi%", PDO::PARAM_STR);
$stmt->execute();
$row = $stmt->fetch();

if ($row){
    $url = str_replace('doi.php', 'pdfviewer.php', $baseURL) . "?file=" . $row[0] . ".pdf";
    print '<script type="text/javascript">window.location ="' . $url . '"</script>;';
} else {
    redirectToPublisher($doi);
}


function redirectToPublisher($doi){
    $url = "http://38.100.138.163/" . urldecode($doi);
    print '<script type="text/javascript">window.location ="' . $url . '"</script>;';
}

?>
