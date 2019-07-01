<?php
/**
 * Pull files from external SFTP, such as Ability (provider for cohesive)
 * This is called by a cronjob every 5 minutes
 * initiated by ticket kra-4038
 */
require __DIR__ . '/vendor/autoload.php';

use EDIAccelerator\Config\ConfigFactory;
use Illuminate\Database\Capsule\Manager as Capsule;
use Symfony\Component\Console\Output\ConsoleOutput;

const INTERVAL = 10;#minutes before we pull sftp
//const INTERVAL = 250;//ttodo debugging
const FOLDERS_TO_PULL = ['remits', 'claims', 'payments', 'ucrn', 'patient_type', 'additional_info'];

// Add configuration and Connect to database
$capsule = new Capsule();
$config = ConfigFactory::getInstance("dotenv");
$capsule->addConnection([
    'driver' => $config->databaseDriver,
    'host' => $config->databaseHost,
    'database' => $config->databaseDatabase,
    'username' => $config->databaseUsername,
    'password' => $config->databasePassword,
    'charset' => $config->databaseCharset,
    'collation' => $config->databaseCollation,
    'prefix' => $config->databasePrefix,
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$db = $capsule->getConnection();

// Only active Clients with an extern_sftp_jobs
$extern_sftp_jobs = $results = $db->select($db->raw("SELECT extern_jobs.id, client_id, source_sftp_credentials, clients.code FROM
              (SELECT * FROM extern_sftp_jobs WHERE start_time between (DATE_SUB(CURTIME(), INTERVAL " . INTERVAL . " MINUTE)) AND CURTIME()) as extern_jobs
INNER JOIN (SELECT id, code FROM clients) clients ON clients.id = extern_jobs.client_id
"));

if (sizeof($extern_sftp_jobs) === 0) {
    $output = new ConsoleOutput();
    $output->writeln("INFO: In this moment, we don't have extern sftp to pull.");
    exit();
}

foreach ($extern_sftp_jobs as $extern_sftp_job) {
    $extern_sftp_job_id = $extern_sftp_job->id;
    $client_id = $extern_sftp_job->client_id;
    $client_code = $extern_sftp_job->code;
    $source_sftp_credentials = json_decode($extern_sftp_job->source_sftp_credentials, JSON_OBJECT_AS_ARRAY);//e.g. {"host":"sftp.accessrcm.abilitynetwork.com","port":22,"username":"carnegieok","password":"UbDeKSP7kw","root_directory":"outgoing"}
    $remote_root_dir = $source_sftp_credentials['root_directory'];

    $extern_sftp = ssh2_connect($source_sftp_credentials['host'], $source_sftp_credentials['port']);
    if (! is_resource($extern_sftp)) {
        echo "Cannot initiate connection - client $client_id";
        continue;
    }
    if (! ssh2_auth_password($extern_sftp, $source_sftp_credentials['username'], $source_sftp_credentials['password'])) {
        echo "failed logging in: $client_id " . $e->getMessage();
        continue;
    }
    $sftp = ssh2_sftp($extern_sftp);
    $sftp_fd = intval($sftp);

    $handle = opendir("ssh2.sftp://$sftp_fd/$remote_root_dir");
    echo "Directory handle: $handle\n";
    echo "Entries:\n";
    $last_pulled_log = '';
    while (false != ($entry = readdir($handle))) {
        if (! in_array($entry, FOLDERS_TO_PULL)) {
            continue;
        }
        echo "$entry\n";
        $subdir_handle = opendir("ssh2.sftp://$sftp_fd/$remote_root_dir/$entry");
        $files = [];
        $localDir = "/encrypted/sftp/$client_code/incoming/$entry/";
        while ((false != ($file = readdir($subdir_handle)))) {
            if ($file == '.' or $file == '..') continue;
            $files[] = $file;
        }
        foreach ($files as $file) {
            $last_pulled_log .= "Copying file: $file\n";
            $remote_file_path = "ssh2.sftp://{$sftp_fd}/$remote_root_dir/{$entry}/{$file}";
            if (!$remote = @fopen($remote_file_path, 'r'))
            {
                $last_pulled_log .= "Unable to open remote file: $file\n";
                continue;
            }

            if (!$local = @fopen($localDir . $file, 'w'))
            {
                $last_pulled_log .= "Unable to create local file: $file\n";
                continue;
            }

            $read = 0;
            $filesize = filesize($remote_file_path);
            while ($read < $filesize && ($buffer = fread($remote, $filesize - $read)))
            {
                $read += strlen($buffer);
                if (fwrite($local, $buffer) === FALSE)
                {
                    $last_pulled_log .= "Unable to write to local file: $file\n";
                    break;
                }
            }
            fclose($local);
            fclose($remote);
        }

    }
    $files_count = count($files);

    $db->update("UPDATE extern_sftp_jobs SET last_pulled_ts = CURRENT_TIMESTAMP, last_pulled_status = 'success', last_pulled_files_count = $files_count, last_pulled_log = '$last_pulled_log'
    WHERE id = $extern_sftp_job_id");
    echo $last_pulled_log;
}
