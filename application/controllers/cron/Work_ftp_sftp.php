<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use phpseclib3\Net\SFTP;

class Work_ftp_sftp extends CI_Controller {
	
    public function __construct() {
        parent::__construct();
        if(!$this->input->is_cli_request()) {
            die('Request is not permited.');
        }
    }
    
    public function work_in_ftp() {
        echo '=========================================' . PHP_EOL;
        echo 'CRON: ' . __METHOD__ . PHP_EOL;
        echo '=========================================' . PHP_EOL;
        echo '==== INICIO ACCIONES EN FTP ===='.PHP_EOL;
        try {
            $name_file = 'miarchivo';
            $date = date('YmdHis');
            $nombre_archivo_local = $name_file . '_' . $date . '.txt';
            $nombre_archivo_ftp = $name_file . '_' . $date .'.txt';
            
            $file = fopen(TEMP_PROJECT.$nombre_archivo_local, 'w');
            fwrite($file, 'Archivo creado en la fecha: '.$date);
            fclose($file);
            
            if(file_exists(TEMP_PROJECT.$nombre_archivo_local)) {
                //INICIANDO E INSTANCIANDO FTP
                $this->load->library('ftp');
                $config['hostname'] = HOST_FTP;
                $config['username'] = USER_FTP;
                $config['password'] = PASS_FTP;
                $config['port'] = PORT_FTP;
                $config['passive'] = TRUE;
                $config['debug'] = FALSE;
                
                
                //CONECTANDO A FTP
                $this->ftp->connect($config);
                
                echo '==== CREANDO CARPETA PARA SUBIR ARCHIVOS ====' . PHP_EOL;
                //CREANDO DIRECTORIO EN FTP
                $this->ftp->mkdir(DIR_FTP, 0777);
                
                //CREANDO DIRECTORIO EN FTP
                $this->ftp->mkdir(DIR_OLD_FTP, 0777);
                
                echo '==== INICIO MOVER ARCHIVOS ANTIGUOS ====' . PHP_EOL;
                //OBTENER LISTANDO DE ARCHIVOS EN FTP
                $list_files = $this->ftp->list_files(DIR_FTP);
                if(count($list_files) > 0 && isset($list_files) && $list_files != FALSE) {
                    foreach($list_files as $lfile) {
                        $array_file = explode(".", $lfile);
                        if(isset($array_file[1]) && $array_file[1] == "txt") {
                            //MOVER ARCHIVO EN FTP
                            $this->ftp->move(DIR_FTP . $lfile, DIR_OLD_FTP . $lfile, 'auto');
                        }
                    }
                }
                echo '==== FIN MOVER ARCHIVOS ANTIGUOS ====' . PHP_EOL;
                
                echo '==== INICIO SUBIR ARCHIVO ====' . PHP_EOL;
                //SUBIR ARCHIVO EN FTP
                $this->ftp->upload(TEMP_PROJECT.$nombre_archivo_local, DIR_FTP.$nombre_archivo_ftp, 'auto');
                unlink(TEMP_PROJECT.$nombre_archivo_local);
                echo '==== FIN SUBIR ARCHIVO ====' . PHP_EOL;
                
                echo '==== INICIO ELIMINAR ARCHIVO ====' . PHP_EOL;
                //OBTENER LISTANDO DE ARCHIVOS EN FTP
                $list_files_old = $this->ftp->list_files(DIR_OLD_FTP);
                $date_file_old = new \DateTime(date("Y-m-d H:i:s"));
                $date_delete = $date_file_old->sub(new \DateInterval('PT7M'));
                if(count($list_files_old) > 0 && isset($list_files_old) && $list_files_old != FALSE) {
                    foreach($list_files_old as $lfile) {
                        $array_file = explode(".", $lfile);
                        if(isset($array_file[1]) && ($array_file[1] == "txt")) {
                            $array_file_name = explode("_", $array_file[0]);
                            $s_date_file = end($array_file_name);
                            $date_file = new \DateTime($s_date_file);
                            if($date_file->format('YmdHis') < $date_delete->format('YmdHis')) {
                                //ELIMINAR ARCHIVO EN FTP
                                $this->ftp->delete_file(DIR_OLD_FTP . $lfile);
                            }
                        }
                    }
                }
                echo '==== FIN ELIMINAR ARCHIVO ===='. PHP_EOL;
                
                //CERRAR CONEXION EN FTP
                $this->ftp->close();
            }else {
                throw new \Exception('NO EXISTE ARCHIVO');
            }
        }catch(\Exception $e) {
            echo '==== ERROR: ' . $e->getMessage() . ' ====' . PHP_EOL;
        }
        echo '==== FIN ACCIONES EN FTP ===='.PHP_EOL;
    }
    
    public function work_in_sftp() {
        echo '=========================================' . PHP_EOL;
        echo 'CRON: ' . __METHOD__ . PHP_EOL;
        echo '=========================================' . PHP_EOL;
        echo '==== INICIO ACCIONES EN SFTP ===='. PHP_EOL;
        try {
            $name_file = 'miarchivo';
            $date = date('YmdHis');
            $nombre_archivo_local = $name_file . '_' . $date . '.txt';
            $nombre_archivo_sftp = $name_file . '_' . $date .'.txt';
            
            $file = fopen(TEMP_PROJECT.$nombre_archivo_local, 'w');
            fwrite($file, 'Archivo creado en la fecha: '.$date);
            fclose($file);
            if(file_exists(TEMP_PROJECT.$nombre_archivo_local)) {
                $ruta = DIR_SFTP;
                $ruta_old = DIR_OLD_SFTP;
                //INICIANDO E INSTANCIANDO SFTP
                $sftp = new SFTP(HOST_SFTP);
                
                //CONECTANDO A SFTP
                if(!$sftp->login(USER_SFTP, PASS_SFTP)) {
                    throw new \Exception('FALLO LOGIN SFTP');
                }
                
                //VERIFICAR SI ES UN DIRECTORIO EN SFTP
                if(!$sftp->is_dir($ruta)) {
                    echo '==== CREANDO CARPETA PARA SUBIR ARCHIVOS ====' . PHP_EOL;
                    //CREANDO DIRECTORIO EN SFTP
                    $sftp->mkdir($ruta, 0777);
                }
                
                //POSICIONAR E IR A UN DIRECTORIO DEL SFTP
                $sftp->chdir($ruta);
                
                //VERIFICAR SI ES UN DIRECTORIO EN SFTP
                if(!$sftp->is_dir($ruta_old)) {
                    echo '==== CREANDO CARPETA PARA MOVER ARCHIVOS ANTIGUOS ====' . PHP_EOL;
                    //CREANDO DIRECTORIO EN SFTP
                    $sftp->mkdir($ruta_old, 0777);
                }
                
                echo '==== INICIO MOVER ARCHIVOS ANTIGUOS ====' . PHP_EOL;
                //OBTENER LISTANDO DE ARCHIVOS EN SFTP
                $list_files = $sftp->nlist();
                if(count($list_files) > 0 && isset($list_files) && $list_files != FALSE) {
                    foreach($list_files as $lfile) {
                        //VERIFICAR SI ES ARCHIVO EN SFTP
                        $is_file = $sftp->is_file($lfile);
                        //VERIFICAR SI EXISTE ARCHIVO EN SFTP
                        $file_exists = $sftp->file_exists($lfile);
                        if($is_file && $file_exists) {
                            $array_file = explode(".", $lfile);
                            if(isset($array_file[1]) && $array_file[1] == "txt") {
                                $nombre_archivo_old = $ruta_old . $lfile;
                                //MOVER ARCHIVO EN SFTP
                                if(!$sftp->rename($lfile, $nombre_archivo_old)) {
                                    echo '==== NO SE MOVIO ARCHIVO '. $lfile .' ====' . PHP_EOL;
                                }else {
                                    echo '==== SE MOVIO ARCHIVO '. $lfile .' ====' . PHP_EOL;
                                }
                            }
                        }
                    }
                }
                echo '==== FIN MOVER ARCHIVOS ANTIGUOS ====' . PHP_EOL;
                
                echo '==== INICIO SUBIR ARCHIVO ====' . PHP_EOL;
                //SUBIR ARCHIVO EN SFTP
                if(!$sftp->put($nombre_archivo_sftp, TEMP_PROJECT.$nombre_archivo_local, SFTP::SOURCE_LOCAL_FILE)) {
                    //VOLVER AL DIRECTORIO PADRE EN SFTP
                    $sftp->chdir('..');
                    throw new \Exception('NO SE SUBIO ARCHIVO A SFTP');
                }else {
                    //VOLVER AL DIRECTORIO PADRE EN SFTP
                    $sftp->chdir('..');
                    echo '==== SE SUBIO ARCHIVO A SFTP ====' . PHP_EOL;
                }
                unlink(TEMP_PROJECT.$nombre_archivo_local);
                echo '==== FIN SUBIR ARCHIVO ====' . PHP_EOL;
                
                echo '==== INICIO ELIMINAR ARCHIVO ====' . PHP_EOL;
                //POSICIONAR E IR A UN DIRECTORIO DEL SFTP
                $sftp->chdir($ruta_old);
                //OBTENER LISTANDO DE ARCHIVOS EN SFTP
                $list_files_old = $sftp->nlist();
                $date_file_old = new \DateTime(date("Y-m-d H:i:s"));
                $date_delete = $date_file_old->sub(new \DateInterval('PT7M'));
                if(count($list_files_old) > 0 && isset($list_files_old) && $list_files_old != FALSE) {
                    foreach($list_files_old as $lfile) {
                        //VERIFICAR SI ES ARCHIVO EN SFTP
                        $is_file = $sftp->is_file($lfile);
                        //VERIFICAR SI EXISTE ARCHIVO EN SFTP
                        $file_exists = $sftp->file_exists($lfile);
                        if($is_file && $file_exists) {
                            $array_file = explode(".", $lfile);
                            if(isset($array_file[1]) && ($array_file[1] == "txt")) {
                                $array_file_name = explode("_", $array_file[0]);
                                $s_date_file = end($array_file_name);
                                $date_file = new \DateTime($s_date_file);
                                if($date_file->format('YmdHis') < $date_delete->format('YmdHis')) {
                                    //ELIMINAR ARCHIVO EN SFTP
                                    if(!$sftp->delete($lfile, false)) {
                                        echo '==== NO SE ELIMINO ARCHIVO '. $lfile .' ====' . PHP_EOL;
                                    }else {
                                        echo '==== SE ELIMINO ARCHIVO '. $lfile .' ====' . PHP_EOL;
                                    }
                                }
                            }
                        }
                    }
                }
                echo '==== FIN ELIMINAR ARCHIVO ===='. PHP_EOL;
            }else {
                throw new \Exception('NO EXISTE ARCHIVO');
            }
        }catch(\Exception $e) {
            echo '==== ERROR: ' . $e->getMessage() . ' ====' . PHP_EOL;
        }
        echo '==== FIN ACCIONES EN SFTP ====' . PHP_EOL;
    }
}
