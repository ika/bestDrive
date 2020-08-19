<?php

// export.php

if (!defined('MULTIDB_MODULE') || !USER_AUTHENTICATED) {
    die("UNAUTHORIZED ACCESS");
} else {

    $status = 'error';
    $msg = 'ERROR: Contact Software Developer';

    $export = new Export();
    $export->exportTyres();

    $title = "Article\tSeg\tBrand\tInch\tSize\tLi\tSi\tDesign\tDescription\tSSR\tNet\tRRP\tO/H\n";

    $content = '';
    foreach ($export->data as $val) {
        $content .= "{$val["article"]}" . "\t";
        $content .= "{$val["seg"]}" . "\t";
        $content .= "{$val["brand"]}" . "\t";
        $content .= "{$val["inch"]}" . "\t";
        $content .= "{$val["size"]}" . "\t";
        $content .= "{$val["li"]}" . "\t";
        $content .= "{$val["si"]}" . "\t";
        $content .= "{$val["design"]}" . "\t";
        $content .= "{$val["descr"]}" . "\t";
        $content .= "{$val["ssr"]}" . "\t";
        $content .= str_replace(',','',$val["net"]) . "\t";
        $content .= str_replace(',','',$val["rrp"]) . "\t";
        $content .= "{$val["onhand"]}" . "\t";
        $content .= "\n";
    }

    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename=export.csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo $title;
    echo $content;
}
?>

