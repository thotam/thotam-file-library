<?php

namespace Thotam\ThotamFileLibrary\Http\Controllers;

use League\Flysystem\Util;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Thotam\ThotamFileLibrary\Models\FilePondUpload;

class FilePondUploadController extends Controller
{
    /**
     * create_upload
     *
     * @param  mixed $request
     * @return void
     */
    public function create_upload(Request $request)
    {
        $FilePondUpload = FilePondUpload::create([
            'status' => 1,
            'active' => true,
        ]);

        return response($FilePondUpload->id, 200)->header('Content-Type', 'text/plain');
    }

    /**
     * patch_upload
     *
     * @param  mixed $request
     * @return void
     */
    public function patch_upload(Request $request)
    {
        //Check Patch
        if (!is_numeric($request->patch)) {
            $response = array('message' => 'ID Upload không hợp lệ');
            return response(json_encode($response), 400)->header('Content-Type', 'application/json');
        }

        //Get and check File in database
        $FilePondUpload = Cache::remember($request->patch . 'FilePondUpload', 60 * 60, function () use ($request) {
            return FilePondUpload::find($request->patch);
        });

        if (!!!$FilePondUpload) {
            $response = array('message' => 'Không tìm thấy file Upload');
            return response(json_encode($response), 404)->header('Content-Type', 'application/json');
        }

        //Do save chunk
        if ($request->header('upload-offset') == 0) {
            $hash = Str::random(30);
            $meta = Str::replace('/', '_', '-meta'.base64_encode($FilePondUpload->id . '_' . $request->header('upload-name')).'-');
            $extension = '.tt';

            $name = $hash.$meta.$extension;

            $FilePondUpload->update([
                'status' => 2,
                'livewire_patch' => $name,
                'full_patch' => Util::normalizeRelativePath(config('livewire.temporary_file_upload.directory') ?: 'livewire-tmp') . '/' . $name,
                'name' => $name,
            ]);

            Storage::disk('local')->put($FilePondUpload->full_patch, $request->getContent());
            Cache::forget($request->patch . 'FilePondUpload');
        } else {
            $upload = fopen(Storage::disk('local')->path($FilePondUpload->full_patch), 'a');
            fwrite($upload, $request->getContent());
            fclose($upload);
        }

        //Check if done
        if ($request->header('upload-offset') + $request->header('content-length') == $request->header('upload-length')) {
            $FilePondUpload->update([
                'status' => 3,
            ]);

            $response = array(
                'id' => $FilePondUpload->id,
                'status' => 2,
            );
            return response(json_encode($response), 200)->header('Content-Type', 'application/json');
        }

        //The server is waiting for the next chunk to be sent.
        $response = array(
            'id' => $FilePondUpload->id,
            'status' => 1,
            'uploadted' => $request->header('upload-offset') . " to " . ($request->header('upload-offset') + $request->header('content-length')),
        );
        return response(json_encode($response), 201)->header('Content-Type', 'application/json');
    }

    /**
     * delete_upload
     *
     * @param  mixed $request
     * @return void
     */
    public function delete_upload(Request $request)
    {
        //Check Patch
        if (!is_numeric($request->getContent())) {
            $response = array('message' => 'ID Upload không hợp lệ');
            return response(json_encode($response), 400)->header('Content-Type', 'application/json');
        }

        //Get and check File in database
        $FilePondUpload = FilePondUpload::find($request->getContent());

        if (!!!$FilePondUpload) {
            $response = array('message' => 'Không tìm thấy file Upload');
            return response(json_encode($response), 404)->header('Content-Type', 'application/json');
        }

        //Delete File
        Storage::disk('local')->delete($FilePondUpload->full_patch);

        //Update Database Status
        $FilePondUpload->update([
            'status' => 0,
        ]);

        return response($FilePondUpload->id . " Deleted", 200)->header('Content-Type', 'text/plain');
    }
}
