<?php

namespace App\Repositories\group;

use Illuminate\Http\Request;

interface IGroupRepository
{
    public function index();
    public function store(Request $request);
    public function addMembers(Request $request);
    public function user_group();
}
