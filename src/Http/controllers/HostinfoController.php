<?php

namespace Somarkn99\HostInfo\Http\Controllers;

class HostInfoController extends Controller
{
    public function isShellEnabled()
    {
        /*Check if shell_exec() is enabled on this server*/
        if (function_exists('shell_exec') && !in_array('shell_exec', array_map('trim', explode(', ', ini_get('disable_functions'))))) {
            /*If enabled, check if shell_exec() actually have execution power*/
            $returnVal = shell_exec('cat /proc/cpuinfo');
            if (!empty($returnVal)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function get_location($ip_address)
    {
        /*Getting user IP address details with geoplugin.net*/
        $addr_details = @unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$ip_address));
        return $addr_details;
    }

    public function server_os()
    {
        $os_detail = php_uname();
        $just_os_name = explode(" ", trim($os_detail));
        $server_os = $just_os_name[0];
        return $server_os;
    }

    public function get_hostName()
    {
        return trim(gethostbyname(gethostname()));
    }

    public function check_cpu_count()
    {

            if ($this->isShellEnabled()) {
                $cpu_count = shell_exec('cat /proc/cpuinfo |grep "physical id" | sort | uniq | wc -l');
            } else {
                $cpu_count = 'ERROR EXEC096T';
            }

        return $cpu_count;
    }

    public function check_total_ram()
    {
            if ($this->isShellEnabled()) {
                $total_ram = shell_exec("grep -w 'MemTotal' /proc/meminfo | grep -o -E '[0-9]+'");
            } else {
                $total_ram = 'ERROR EXEC096T';
            }
        return trim($total_ram);
    }

    public function check_ram()
    {
        (is_numeric($this->check_total_ram()) ? $this->format_filesize_kB($this->check_total_ram()) : $this->check_total_ram());
    }

    public function format_filesize_kB($kiloBytes)
    {
        if (($kiloBytes / pow(1024, 4)) > 1) {
            return $kiloBytes / pow(1024, 4) . ' ' .'PB';
        } elseif (($kiloBytes / pow(1024, 3)) > 1) {
            return $kiloBytes / pow(1024, 3) . ' ' . 'TB';
        } elseif (($kiloBytes / pow(1024, 2)) > 1) {
            return $kiloBytes / pow(1024, 2) . ' ' . 'GB';
        } elseif (($kiloBytes / 1024) > 1) {
            return $kiloBytes / 1024 . ' ' .'MB';
        } elseif ($kiloBytes >= 0) {
            return ($kiloBytes / 1) . ' ' . 'KB';
        } else {
            return 'Unknown';
        }
    }

    public function format_filesize($bytes)
    {
        if (($bytes / pow(1024, 5)) > 1) {
            return (($bytes / pow(1024, 5))) . ' ' . 'PB';
        } elseif (($bytes / pow(1024, 4)) > 1) {
            return (($bytes / pow(1024, 4))) . ' ' . 'TB';
        } elseif (($bytes / pow(1024, 3)) > 1) {
            return (($bytes / pow(1024, 3))) . ' ' . 'GB';
        } elseif (($bytes / pow(1024, 2)) > 1) {
            return (($bytes / pow(1024, 2))) . ' ' . 'MB';
        } elseif ($bytes / 1024 > 1) {
            return ($bytes / 1024) . ' ' .'KB';
        } elseif ($bytes >= 0) {
            return ($bytes) . ' ' . 'bytes';
        } else {
            return 'Unknown';
        }
    }


    public function format_php_size($size)
    {
        if (!is_numeric($size)) {
            if (strpos($size, 'M') !== false) {
                $size = intval($size) * 1024 * 1024;
            } elseif (strpos($size, 'K') !== false) {
                $size = intval($size) * 1024;
            } elseif (strpos($size, 'G') !== false) {
                $size = intval($size) * 1024 * 1024 * 1024;
            }
        }
        return is_numeric($size) ? $this->format_filesize($size) : $size;
    }


    public function php_max_upload_size()
    {
            if (ini_get('upload_max_filesize')) {
                $php_max_upload_size = ini_get('upload_max_filesize');
                $php_max_upload_size = $this->format_php_size($php_max_upload_size);
            } else {
                $php_max_upload_size = 'N/A';
            }

        return $php_max_upload_size;
    }

    public function php_max_execution_time()
    {
        if (ini_get('max_execution_time')) {
            $max_execute = ini_get('max_execution_time');
        } else {
            $max_execute = 'N/A';
        }
        return $max_execute;
    }

    public function php_short_tag()
    {
        if (ini_get('short_open_tag')) {
            $short_tag = 'On';
        } else {
            $short_tag = 'Off';
        }
        return $short_tag;
    }

    public function check_limit()
    {
        $memory_limit = ini_get('memory_limit');
        if (preg_match('/^(\d+)(.)$/', $memory_limit, $matches)) {
            if ($matches[2] == 'G') {
                $memory_limit = $matches[1] . ' ' . 'GB'; // nnnG -> nnn GB
            } else if ($matches[2] == 'M') {
                $memory_limit = $matches[1] . ' ' . 'MB'; // nnnM -> nnn MB
            } else if ($matches[2] == 'K') {
                $memory_limit = $matches[1] . ' ' . 'KB'; // nnnK -> nnn KB
            } else if ($matches[2] == 'T') {
                $memory_limit = $matches[1] . ' ' . 'TB'; // nnnT -> nnn TB
            } else if ($matches[2] == 'P') {
                $memory_limit = $matches[1] . ' ' . 'PB'; // nnnP -> nnn PB
            }
        }
        return $memory_limit;
    }


    public function index()
    {
        $server_info  = [];
        $server_info['Server_OS'] = $this->server_os();

        $server_info['Server_Software'] = $_SERVER['SERVER_SOFTWARE'] ?? '';
        $server_info['Server_IP'] = $_SERVER['SERVER_ADDR'] ?? $_SERVER['REMOTE_ADDR'];
        $server_info['Server_Port'] = $_SERVER['SERVER_PORT'] ?? '';

        $addr_details = $this->get_location($server_info['Server_IP']  ?? '');
        $server_info['Server_Country'] = $addr_details['geoplugin_countryName'] ?? '';
        $server_info['Server_City'] = $addr_details['geoplugin_city'] ?? '';

        $server_info['Server_Hostname'] = $this->get_hostName();
        $server_info['Document Root'] = $_SERVER['DOCUMENT_ROOT'] ?? '';

        $server_info['Total_CPUs'] = $this->check_cpu_count();
        $server_info['Total_Ram'] = $this->check_ram();
        $server_info['PHP_Version'] = phpversion();

        $server_info['PHP_Max_Upload_Size'] = $this->php_max_upload_size();
        $server_info['PHP_Max_Execution_Time'] = $this->php_max_execution_time();
        $server_info['PHP_Short_Tag'] = $this->php_short_tag();
        $server_info['PHP_Memory_Limit'] = $this->check_limit();

        return $server_info;
    }
}
