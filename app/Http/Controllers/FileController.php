<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Repositories\file\IFileRepository;
use App\Repositories\group\IGroupRepository;
use App\Traits\ResponseTrait;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileController extends Controller
{
    use ResponseTrait;
    public $group;

    public function __construct(IFileRepository $fileRepository)
    {
        $this->FileRepository = $fileRepository;
    }


    public function upload(Request $request): JsonResponse
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'group' => 'required',
            'file' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->returnError("V00", $validator->errors());
        }
        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $file->move(public_path('Files'), $fileName);
        File::create([
            'name' => $fileName,
            'path' => public_path('Files'),
            'user_id' => $user->id,
            'group_id' => $request->group
        ]);
        return $this->returnSuccess("D00", "file uploaded successfully..");
    }




    public function download(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->returnError("V00", $validator->errors());
        }

        $path = public_path('Files/' . $request->name);

        if (!file_exists($path)) {
            abort(404, 'File not found');
        }

        $response = new BinaryFileResponse($path);
        $response->setContentDisposition(
            'attachment',
            $request->name,
            iconv('UTF-8', 'ASCII//TRANSLIT', $request->name)
        );

        return $response;
    }




    public function removeFile(Request $request):JsonResponse{

        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:files,id,user_id,' . $user->id,
        ]);

        if ($validator->fails()) {
            return $this->returnError("V01", $validator->errors());
        }

        $this->FileRepository->removeFile($request);

        return $this->returnSuccess("D01", "File deleted successfully.");
    }




    public function index(): JsonResponse{
        $files = $this->FileRepository->index();
        return $this->returnData('files',$files,"","");
    }

    public function search($filter): JsonResponse{
        $filterResult = $this->FileRepository->search($filter);
        return $this->returnData('files',$filterResult,"","");
    }



    public function bulkCheckIn(Request $request){

        $ids = $request->input('ids', []);
        if (!is_array($ids)) {
            $ids = explode(',', $ids);
        }
        $lockedFiles = File::whereIn('id', $ids)->where('status', 1)->get();

        if ($lockedFiles->isNotEmpty()) {
            return response()->json([
                "message" => "one or more files are locked.",
            ]);
        }
        File::whereIn('id', $ids)->update(['status' => 1]);

        return response()->json([
            "message" => "You checked in one or more files successfully.",
        ]);

    }


    public function checkOut(Request $request){
        $file = File::find($request->id);
        if ($file->status == 1) {
            $file->status = 0;
            $file->save();
            return response()->json([
                "message" => "you checked out successfully.",
            ]);
        } else {
            return response()->json([
                "message" => "check out failed.",
            ]);
        }
    }
}
