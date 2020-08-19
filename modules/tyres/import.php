<?php

// import.php

if (!defined('MULTIDB_MODULE') || !USER_AUTHENTICATED) {
    die("UNAUTHORIZED ACCESS");
} else {

    $status = 'error';
    $msg = 'ERROR: Contact Software Developer';

    if (isset($_POST)) {

        $filename = tempnam(DOCUMENT_ROOT . '/tmp', "CSV");

        $json = json_decode($_POST['data'], true);

        $file = "{$json[0]['content']}";

        $file = base64_decode($file);

        if (is_writeable($filename)) {

            if (file_put_contents($filename, $file) !== FALSE) {

                sleep(1);

                if (is_readable($filename)) {

                    $import = new Import();

                    $csvFile = file($filename);

                    $csv = [];
                    $data = [];

                    foreach ($csvFile as $line) {
                        $csv[] = str_getcsv($line);
                    }

                    $nr = 0;
                    foreach ($csv as $ln) {

                        $nr++;

                        foreach ($ln as $k => $v) {

                            switch ($k) {
                                case 0:
                                    $data['seg'] = $v;
                                    break;
                                case 1:
                                    $data['brand'] = $v;
                                    break;
                                case 2:
                                    $data['inch'] = $v;
                                    break;
                                case 3:
                                    $data['size'] = $v;
                                    break;
                                case 4:
                                    $data['li'] = $v;
                                    break;
                                case 5:
                                    $data['si'] = $v;
                                    break;
                                case 6:
                                    $data['design'] = $v;
                                    break;
                                case 8:
                                    $data['article'] = 'BD-' . $v;
                                    break;
                                case 9:
                                    $v = trim(str_replace('#', '', $v));
                                    $data['descr'] = $v;
                                    break;
                                case 11:
                                    $data['ssr'] = $v;
                                    break;
                                case 16: //rrp divided by 2
                                    $p = (trim(str_replace(' ', '', str_replace('R', '', $v))) / 2);
                                    $data['net'] = $p;
                                    break;
                            }
                        }

                        $import->importData($data);
                    }
                }
            }

            $status = 'success';
        }
    }

    unlink($filename);

    error_log('NUMBER OF LINES IMPORTED: ' . $nr);

    $response = json_encode(array("status" => "$status", "message" => "$msg"));

    exit($response);
}

exit();
?>