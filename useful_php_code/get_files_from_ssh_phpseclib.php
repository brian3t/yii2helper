<?php
/**
 * Pull files from external SFTP, such as Ability (provider for cohesive)
 * This is called by a cronjob every 5 minutes
 * initiated by ticket kra-4038
 */
require __DIR__ . '/vendor/autoload.php';

use EDIAccelerator\Config\ConfigFactory;
use Illuminate\Database\Capsule\Manager as Capsule;
use phpseclib\Net\SFTP;
use Symfony\Component\Console\Output\ConsoleOutput;

const INTERVAL = 10;#minutes before we pull sftp
//const INTERVAL = 250;//ttodo debugging
const FOLDERS_TO_PULL = ['remits', 'claims', 'payments', 'ucrn', 'patient_type', 'additional_info'];
const SFTP_CONNECTION_TIMEOUT = 60;

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

    $extern_sftp = new SFTP($source_sftp_credentials['host'], $source_sftp_credentials['port'], SFTP_CONNECTION_TIMEOUT);
    /** @var $extern_sftp SFTP */
    if (! $extern_sftp instanceof SFTP) {
        echo "Cannot initiate connection - client $client_id";
        continue;
    }
    if (! $extern_sftp->login($source_sftp_credentials['username'], $source_sftp_credentials['password'])) {
        echo "failed logging in: $client_id " . $e->getMessage();
        continue;
    }

    $last_pulled_log = '';
    $subdirs = $extern_sftp->nlist($remote_root_dir);
    foreach ($subdirs as $subdir) {
        if (! in_array($subdir, FOLDERS_TO_PULL)) {
            continue;
        }
        echo "Subdir: $subdir\n";
        $files = [];
        $localDir = "/encrypted/sftp/$client_code/incoming/$subdir/";
        //gathering files to prepare for copying
        foreach ($extern_sftp->nlist("$remote_root_dir/$subdir") as $file)
            if ($file == '.' or $file == '..') continue;
            else $files[] = $file;
        foreach ($files as $file) {//now copy all files
            $last_pulled_log .= "Copying file: $file\n";
            $remote_file_path = "/$remote_root_dir/$subdir/{$file}";

            if (! $extern_sftp->get($remote_file_path, $localDir . $file)) {
                $last_pulled_log .= "Cannot pull remote file: $remote_file_path";
                continue;
            }
            chown($localDir . $file, $client_code);
            chgrp($localDir . $file, 'sftpusers');
        }
    }//end listing subdirs
    $files_count = count($files);

    $db->update("UPDATE extern_sftp_jobs SET last_pulled_ts = CURRENT_TIMESTAMP, last_pulled_status = 'success', last_pulled_files_count = $files_count, last_pulled_log = '$last_pulled_log'
    WHERE id = $extern_sftp_job_id");
    echo $last_pulled_log;
}
