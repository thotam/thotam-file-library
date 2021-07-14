<?php

namespace Thotam\ThotamFileLibrary\Http\Controllers;

use Auth;
use Response;
use Google_Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Thotam\ThotamFileLibrary\ThotamVideoStream;
use Thotam\ThotamFileLibrary\Models\FileLibrary;

class FileLibraryController extends Controller
{
    /**
     * view
     *
     * @return void
     */
    public function view($id)
    {
        $file = FileLibrary::find($id);
        if (!!$file) {
            if ($file->drive == 'google') {
                return redirect("https://drive.google.com/open?id=" . $file->google_id);
            } else {
                return response(Storage::disk('public')->get($file->local_path))->header('Content-Type', $file->mime_type);
            }
        } else {
            return view('thotam-file-library::errors.dynamic', [
                'error_code' => '404',
                'error_description' => 'Không tìm thấy file này',
                'title' => 'FileLibrary',
            ]);
        }
    }

    /**
     * embed
     *
     * @return void
     */
    public function embed($id)
    {
        $file = FileLibrary::find($id);
        if (!!$file) {
            if ($file->drive == 'google') {
                return response(Storage::disk('google')->get($file->google_display_path))->header('Content-Type', $file->mime_type);
            } else {
                return response(Storage::disk('public')->get($file->local_path))->header('Content-Type', $file->mime_type);
            }
        } else {
            return view('thotam-file-library::errors.dynamic', [
                'error_code' => '404',
                'error_description' => 'Không tìm thấy file này',
                'title' => 'FileLibrary',
            ]);
        }
    }

    /**
     * mail_image
     *
     * @return void
     */
    public function mail_image($id)
    {
        $file = FileLibrary::find($id);
        if (!!$file) {
            if ($file->drive == 'google') {
                $client = new Google_Client();
                $config = config()["filesystems"]["disks"]["google"];
                $client->setClientId($config['clientId']);
                $client->setClientSecret($config['clientSecret']);
                $client->refreshToken($config['refreshToken']);
                $service = new \Google_Service_Drive($client);
                $request = $service->files->get(
                    $file->google_id, array('fields' => 'thumbnailLink')
                );

                if (!!$request->thumbnailLink) {
                    return redirect(str_replace("=s220","", $request->thumbnailLink));
                } else {
                    return response(Storage::disk('google')->get($file->google_display_path))->header('Content-Type', $file->mime_type);
                }
            } else {
                return response(Storage::disk('public')->get($file->local_path))->header('Content-Type', $file->mime_type);
            }
        } else {
            return view('thotam-file-library::errors.dynamic', [
                'error_code' => '404',
                'error_description' => 'Không tìm thấy file này',
                'title' => 'FileLibrary',
            ]);
        }
    }

    /**
     * image
     *
     * @return void
     */
    public function image($id)
    {
        $file = FileLibrary::find($id);
        if (!!$file) {
            if ($file->drive == 'google') {
                $client = new Google_Client();
                $config = config()["filesystems"]["disks"]["google"];
                $client->setClientId($config['clientId']);
                $client->setClientSecret($config['clientSecret']);
                $client->refreshToken($config['refreshToken']);
                $service = new \Google_Service_Drive($client);
                $request = $service->files->get(
                    $file->google_id, array('fields' => 'thumbnailLink')
                );

                if (!!$request->thumbnailLink) {
                    return redirect(str_replace("=s220","", $request->thumbnailLink));
                } else {
                    return response(Storage::disk('google')->get($file->google_display_path))->header('Content-Type', $file->mime_type);
                }
            } else {
                return response(Storage::disk('public')->get($file->local_path))->header('Content-Type', $file->mime_type);
            }
        } else {
            return view('thotam-file-library::errors.dynamic', [
                'error_code' => '404',
                'error_description' => 'Không tìm thấy file này',
                'title' => 'FileLibrary',
            ]);
        }
    }

    /**
     * googleapis
     *
     * @return void
     */
    public function googleapis($id)
    {
        $file = FileLibrary::find($id);
        if (!!$file) {
            if ($file->drive == 'google') {
                return redirect("https://www.googleapis.com/drive/v3/files/".$file->google_id."?alt=media&key=" . config('thotam-file-library.googleApiKey'));
            } else {
                return response(Storage::disk('public')->get($file->local_path))->header('Content-Type', $file->mime_type);
            }
        } else {
            return view('thotam-file-library::errors.dynamic', [
                'error_code' => '404',
                'error_description' => 'Không tìm thấy file này',
                'title' => 'FileLibrary',
            ]);
        }
    }

    /**
     * download
     *
     * @return void
     */
    public function download($id)
    {
        $file = FileLibrary::find($id);
        if (!!$file) {
            if ($file->drive == 'google') {
                return Storage::disk('google')->download($file->google_display_path);
            } else {
                return Storage::disk('public')->download($file->local_path);
            }
        } else {
            return view('thotam-file-library::errors.dynamic', [
                'error_code' => '404',
                'error_description' => 'Không tìm thấy file này',
                'title' => 'FileLibrary',
            ]);
        }
    }

    /**
     * thumbnail
     *
     * @return void
     */
    public function thumbnail($id)
    {
        $file = FileLibrary::find($id);
        if (!!$file) {
            if ($file->drive == 'google') {
                $client = new Google_Client();
                $config = config()["filesystems"]["disks"]["google"];
                $client->setClientId($config['clientId']);
                $client->setClientSecret($config['clientSecret']);
                $client->refreshToken($config['refreshToken']);
                $service = new \Google_Service_Drive($client);
                $request = $service->files->get(
                    $file->google_id, array('fields' => 'thumbnailLink')
                );

                if (!!$request->thumbnailLink) {
                    return redirect(str_replace("=s220","=s660", $request->thumbnailLink));
                } else {
                    return redirect(Storage::disk('public')->url("Media/thumbnail-default.png"));
                }
            } else {
                return redirect(Storage::disk('public')->url("Media/thumbnail-default.png"));
            }
        } else {
            return view('thotam-file-library::errors.dynamic', [
                'error_code' => '404',
                'error_description' => 'Không tìm thấy file này',
                'title' => 'FileLibrary',
            ]);
        }
    }

    /**
     * stream getObjects
     *
     * @return void
     */
    public function stream($id)
    {
        $file = FileLibrary::find($id);
        if (!!$file) {
            if ($file->drive == 'google') {
                //return redirect(Storage::disk('google')->getAdapter()->getFileObject($file->google_virtual_path)->webContentLink);

                return redirect("https://www.googleapis.com/drive/v3/files/".$file->google_id."?alt=media&key=" . config('thotam-file-library.googleApiKey'));
            } else {
                $stream = new ThotamVideoStream(Storage::disk('public')->path($file->local_path));
                $stream->start();
            }
        } else {
            return view('thotam-file-library::errors.dynamic', [
                'error_code' => '404',
                'error_description' => 'Không tìm thấy file này',
                'title' => 'FileLibrary',
            ]);
        }
    }

    /**
     * video getObjects
     *
     * @return void
     */
    public function video($id)
    {
        $file = FileLibrary::find($id);
        if (!!$file) {
            if (!!$file->vimeo_id) {
                return $file->vimeo_id;

                return redirect("https://www.googleapis.com/drive/v3/files/".$file->google_id."?alt=media&key=" . config('thotam-file-library.googleApiKey'));
            } else {
                $stream = new ThotamVideoStream(Storage::disk('public')->path($file->local_path));
                $stream->start();
            }
        } else {
            return view('thotam-file-library::errors.dynamic', [
                'error_code' => '404',
                'error_description' => 'Không tìm thấy file này',
                'title' => 'FileLibrary',
            ]);
        }
    }

}
