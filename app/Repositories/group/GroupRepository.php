<?php

namespace App\Repositories\group;


use App\Models\Group;
use App\Models\User;
use App\Models\UserGroup;
use App\Repositories\group\IGroupRepository;
use App\Traits\ResponseTrait;
use App\Traits\UploadFileTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GroupRepository implements IGroupRepository
{
    public Group $Group;
    public Request $Request;

    public function __construct(Group $group ,Request $request)
    {
        $this->Group = $group;
        $this->Request= $request;

    }

    public function index()
    {
        $user = auth()->user();
        $groups = Group::with('usergroups')->get();
        return $groups;
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $group = Group::create([
            'name' => $request->name,
            'user_id' => $user->id,
        ]);
    }

    public function addMembers(Request $request)
    {
        $user = auth()->user();
        $members = $request->users;
        foreach ($members as $member){
            $usr = User::find($member);
            UserGroup::create([
                'group_id' => $request->group,
                'user_id' =>$usr->id,
            ]);
        }
    }

    public function user_group()
    {
        $user = auth()->user();
        $groups = Group::where('user_id',$user->id)->get();
        return $groups;
    }
}
