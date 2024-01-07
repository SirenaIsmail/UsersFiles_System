<?php

namespace App\Repositories\group;


use App\Models\File;
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

    public function my_groups()
    {
        $user = auth()->user();
        $groups = Group::with('usergroups')->where('user_id',$user->id)->get();
        return $groups;
    }

    public function membership_groups()
    {
        $user = auth()->user();
        $groups = Group::with('groupfiles')->join('user_groups', 'groups.id', '=', 'user_groups.group_id')
            ->where('user_groups.user_id',$user->id)
           ->select('groups.id', 'groups.name')
            ->orderBy('user_groups.id')->get();
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

    public function update(Request $request)
    {
        $user = auth()->user();
        $group = Group::find($request->group);
        $group->update([
            'name' => $request->name,
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

    public function removeMember(Request $request){
        $user = auth()->user();
        $member = UserGroup::where('group_id',$request->group)->where('user_id',$request->user)->first();
        $member->delete();
    }

    public function user_group()
    {
        $user = auth()->user();
        $groups = Group::where('user_id',$user->id)->get();
        return $groups;
    }

    public function groupUsers(Request $request){
        $group = Group::find($request->group);
        $users = User::join('user_groups', 'users.id', '=', 'user_groups.user_id')
            ->where('user_groups.group_id',$group->id)
            ->orderBy('users.id')->get();
        return $users;
    }

    public function deleteGroup($request){
        $group = Group::find($request->group);
        $members = UserGroup::where('group_id',$group->id)->get();
        $files = File::where('group_id',$group->id)->get();
        foreach ($members as $member){
            $member->delete();
        }
        foreach ($files as $file){
            $file->delete();
        }
        $group->delete();
    }


}
