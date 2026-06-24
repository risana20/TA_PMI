<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = \Illuminate\Http\Request::create('/test', 'POST', ['nama' => 'Budi']);
$validator = validator($request->all(), ['nama' => 'required', 'foto' => 'nullable|image']);
$data = $validator->validate();
var_dump(array_key_exists('foto', $data));
var_dump($data);
