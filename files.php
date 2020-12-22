<?php
$ftp_server = "ftphost";
$ftp_user = "ftpuser";
$ftp_password = "ftppassword";
$ftp_conn = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");
$login = ftp_login($ftp_conn, $ftp_user, $ftp_password);

ftp_pasv($ftp_conn, true);

$result_string = '';

$ftpdir = '/public_html/output/';
$locdir = 'ddd';
function ftp_putAll($conn_id, $src_dir, $dst_dir) {
    $d = dir($src_dir);
    while($file = $d->read()) { // do this for each file in the directory
        if ($file != "." && $file != "..") { // to prevent an infinite loop
            echo $dst_dir."/".$file;
            if (is_dir($src_dir."/".$file)) { // do the following if it is a directory
                echo "<br>pwd: ".ftp_pwd($conn_id);
                if (!@ftp_chdir($conn_id, $dst_dir."/".$file)) {
                    ftp_mkdir($conn_id, $dst_dir."/".$file); // create directories that do not yet exist
                }
                ftp_putAll($conn_id, $src_dir."/".$file, $dst_dir."/".$file); // recursive part
            } else {
                $upload = @ftp_put($conn_id, $dst_dir."/".$file, $src_dir."/".$file, FTP_BINARY); // put the files
                if($upload){
                    echo "<br>Successfully uploaded".$dst_dir."/".$file;
                } else {
                    echo "<br>Upload failed".$dst_dir."/".$file;
                }

            }
        }
    }
    $d->close();
}
ftp_putAll($ftp_conn, $locdir, $ftpdir);
//echo $result_string;
echo 'Completed';
// close connection
ftp_close($ftp_conn);
return;
