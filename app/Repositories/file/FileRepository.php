<?php

namespace App\Repositories\file;


use App\Models\File;
use App\Models\FileHistory;
use App\Models\Group;
use App\Models\User;
use App\Models\UserGroup;
use App\Repositories\group\IGroupRepository;
use App\Repositories\file\IFileRepository;
use App\Traits\ResponseTrait;
use App\Traits\UploadFileTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FileRepository implements IFileRepository
{

    public File $file;
    public Request $request;
    public function __construct(File $file,Request $request)
    {
        $this->File = $file;
        $this->Request = $request;
    }


    public function create(Request $request){
//        public function downloadFile(Request $request)
//        {
//            $filePath = $request->input('file_path');
//            return Storage::download($filePath);
//        }
    }


    public function removeFile(Request $request){

        $fileId = $request->id;
        $file = File::find($fileId);

        if (!$file) {
            return $this->returnError("V02", "File not found.");
        }

        //عم بحذف الفايل من الخادم عندي
        if (file_exists($file->path.'/'.$file->name)) {
            unlink($file->path.'/'.$file->name);
        }
        $file->delete();
    }




    public function index(){
        $user = auth()->user();
        if($user->role==1){
            $files = DB::table('files')
                ->join('groups', 'files.group_id', '=', 'groups.id')
                ->select('files.name','files.status')
                ->get();
            return $files;
        }
        $files = DB::table('files')
            ->join('groups', 'files.group_id', '=', 'groups.id')
            ->join('user_groups','user_groups.group_id','=','groups.id')
            ->select('files.name','files.status')
            ->where('user_groups.user_id','=',$user->id)
            ->get();
        return $files;
    }



    public function search($filter){
        if($filter != "null"){

            $filterResult = File::where("name", "like","%$filter%")->get();
        }
        if($filterResult)
        {
            return $filterResult;
        }
    }

    public function file_his(Request $request)
    {
        $file_his = FileHistory::where('file_id',$request->id)
            ->orderByDesc('id')->get();
        return $file_his;
    }

    public function user_his(Request $request)
    {
        $file_his = FileHistory::where('user_id',$request->id)
            ->orderByDesc('id')->get();
        return $file_his;
    }


}
