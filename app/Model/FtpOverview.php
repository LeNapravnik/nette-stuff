<?php

declare(strict_types=1);

namespace App\Model;

use Nette;
use Nette\Neon\Neon;
use Nette\Utils\FileSystem;

/** Model 
 * @package App\Model
 */

class FtpOverview {

    /** @var array ftp credentials from local.neon file*/
    private $ftp;

    public function __construct(array $ftp){
        $this->ftp = $ftp;
    }
 
    /**
     * Connects and logs in to FTP server
     * @return FTP/Connection   instance of FTP/Connection
     */
    public function connectFtp(){
        $ftpCredentials = $this->ftp;
        // connect and login to FTP server
        $ftp_server = $ftpCredentials["host"];
        $ftp_conn = ftp_connect($ftp_server, 21, 180) or die("Nelze připojit k $ftp_server");
        $login = ftp_login($ftp_conn, $ftpCredentials["login"], $ftpCredentials["pass"]);
        $mode = ftp_pasv($ftp_conn, TRUE);

        return $ftp_conn;
    }

    /**
     * Returns list of folders and files of given directory
     * @param string    $directory_name folder name - current folder is default
     * @return array    $file_list      list of folders and files in given folder
     */
    public function getDirFiles($directory){
        // connect and login to FTP server
        $ftp_conn = $this->connectFtp();

        if($directory === NULL){
            $directory = ".";
        }

        // get list of folders and files
        $file_list = ftp_mlsd($ftp_conn, $directory);

        // close connection
        ftp_close($ftp_conn);

        return $file_list;
    }

    /**
     * Downloads file of given name from FTP server from given directory
     * @param string    $file_name name of file to get from FTP
     * @param string    $directory name of directory
     */
    public function getFile(string $file_name, $prev_directory = NULL){
        // connect and login to FTP server
        $ftp_conn = $this->connectFtp();

        FileSystem::createDir('../user_ftp_data');
        // save to user_ftp_data
        $dir = '../user_ftp_data/'. $file_name;

        if(!$prev_directory){
            $file_path = $file_name;
        }
        else{
            $file_path = $prev_directory . "/" . $file_name;
        }
        
        // try to download $server_file and save to $local_file
        ftp_get($ftp_conn, $dir, $file_path, FTP_ASCII);
        
        // close connection
        ftp_close($ftp_conn);
    }
}