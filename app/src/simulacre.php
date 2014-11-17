<?php
    /**
     * This file is the bootstrap for the Simulacre application.
     * It checks that all the parameters are correctly passed, instanciate the
     */
    include_once 'simulacre-autoload.php';

    use Simulacre\Simulacre;

    if (count($argv) == 1) {
        fwrite(STDERR, "You must pass a project configuration file as an argument\n");
        exit(0);
    }

    $path          = realpath('../projects/');
    $projectConfig = $path . '/' . $argv[1] . '.php';

    if (!file_exists($projectConfig)) {
        fwrite(STDERR, "The file " . $projectConfig . " does not exists\n");
        exit(0);
    }
    $config = include $projectConfig;

    try {
        $startTime = time();
        $simulacre = new Simulacre($config);
        fwrite(STDOUT, "Project started\n");

        $simulacre->runProject();

        $elapsedTime = time() - $startTime;
        fwrite(STDOUT, "Project finished in " . $elapsedTime . " seconds\n");
    } catch (Exception $e) {
        //fwrite(STDOUT, "Some errors occurred");
        //TODO log errors
        fwrite(STDERR,$e->getMessage());
    }




