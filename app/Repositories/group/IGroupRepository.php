<?php

namespace App\Repositories\group;

use Illuminate\Http\Request;

interface IGroupRepository
{
    public function index();
    public function my_groups();
    public function membership_groups();
    public function store(Request $request);
    public function update(Request $request);
    public function addMembers(Request $request);
    public function removeMember(Request $request);
    public function groupUsers(Request $request);
    public function user_group();
    public function deleteGroup($request);
}
