<?php

namespace App\Repositories\file;

use Illuminate\Http\Request;

interface IFileRepository
{
    public function create(Request $request);
    public function removeFile(Request $request);
    public function index();
    public function search($filter);
}
