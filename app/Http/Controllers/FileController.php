<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FileController extends Controller
{
    use ResponseTrait;

    public function upload(Request $request): JsonResponse{
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
        $file->move(public_path('Files'),$fileName);
        File::create([
            'name' =>$fileName,
            'path' =>public_path('Files'),
            'user_id' =>$user->id,
            'group_id' =>$request->group
        ]);
        return $this->returnSuccess("D00","file uploaded successfully..");
    }
}
