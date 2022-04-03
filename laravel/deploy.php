<?php

namespace Deployer;

use Deployer\Exception\Exception;

require 'recipe/laravel.php';
set('ssh_multiplexing', true);
set('writable_mode', 'chmod');
set('keep_releases', 3);

#set('application', '');
#set('deploy_path', '~/{{application}}');
#set('repository', 'https://github.com/Duplexmedia/gebaeudehuelle.git');
set('bin/php', 'php');
set('default_timeout', null);

host('arbeitsbericht.tannhaeuser-cie.de')
    ->setLabels(['stage' => 'master'])
    ->setRemoteUser('arbeitsbarbeitsbericht')
    ->setDeployPath('/web/production');


// Return release path.
set('release_path', function () {
    $releaseExists = test('[ -h {{deploy_path}}/release ]');
    if ($releaseExists) {
        //$link = run("readlink {{deploy_path}}/release");
        $link = run('ls -l {{deploy_path}}/release | awk -F"-> " \'{print $2}\'');
        return substr($link, 0, 1) === '/' ? $link : get('deploy_path') . '/' . $link;
    } else {
        throw new Exception(parse('The "release_path" ({{deploy_path}}/release) does not exist.'));
    }
});

task('opcache:clear', function () {
    $output = run('cd {{release_path}} && {{bin/php}} artisan opcache:clear');
    writeln($output);
    writeln('<info>' . $output . '</info>');
});

$exclude = [
    '.git',
    '/storage/',
    '/vendor/',
    '/node_modules/',
    '.github',
    'deploy.php',
    '_ide_helper.php',
    '.editorconfig',
    '.env.example',
    '.gitattributes',
    '.phpstorm.meta.php',
];

set('upload_exclude', $exclude);

task('upload', function () {
    $options = [];

    foreach (get('upload_exclude') as $item) {
        $options[] = sprintf("--exclude='%s'", $item);
    }

    upload(
        __DIR__.'/',
        '{{release_path}}/',
        ['options' => $options]
    );
});

after('deploy:failed', 'deploy:unlock');

task('artisan:migrate:once', function () {
    run('cd {{release_path}} && {{bin/php}} artisan migrate --force');
})->once();

task('artisan:optimize:cache:http', function () {
    run('cd {{release_path}} && {{bin/php}} artisan optimize:cache:http');
})->once();

desc('Deploy the application');
task('deploy', [
    'deploy:info',
    'deploy:setup',
    'deploy:lock',
    'deploy:release',
    'upload',
    'deploy:shared',
    'deploy:vendors',
    'deploy:writable',
    'artisan:storage:link',
    'artisan:optimize:cache:http',
    'artisan:migrate:once',
    'opcache:clear',
    'deploy:symlink',
    'deploy:unlock',
    'deploy:cleanup',
]);
