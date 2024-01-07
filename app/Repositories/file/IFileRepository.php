<?php

namespace App\Repositories\file;

use Illuminate\Http\Request;

interface IFileRepository
{
    public function create(Request $request);
    public function file_his(Request $request);
    public function user_his(Request $request);
    public function removeFile(Request $request);
    public function index(Request $request);
    public function my_checked();
    public function search($filter);
}
