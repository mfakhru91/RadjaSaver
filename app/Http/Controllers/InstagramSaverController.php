<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use InstagramScraper\Instagram;
use PHPUnit\Util\Json;

class InstagramSaverController extends Controller
{
    public function GetMedia(Request $request)
    {
        $instagram = new \InstagramScraper\Instagram(new \GuzzleHttp\Client());
        $media = $instagram->getMediaByUrl($request->get('data'));
        $account = $media->getOwner();
        $type = $media->getType();
        if ($type == 'video') {
            $mediaUrl = $media->getVideoStandardResolutionUrl();
        }elseif($type == 'sidecar'){
            $sideCar = $media->getSidecarMedias();
            $mediaUrl = [];
            foreach ($sideCar as $data ) {
                array_push($mediaUrl,$data->getImageHighResolutionUrl());
            }
        } 
        else {
            $mediaUrl = $media->getImageHighResolutionUrl();
        }
        
        $response = [
            'success' => true,
            'data' => [
                'userImage' => $account->getUsername(),
                'userProfil' => $account->getProfilePicUrl(),
                'useFollowers' => $account->getFollowedByCount(),
                'type' => $media->getType(),
                'url' => $mediaUrl
            ]
        ];
        return response()->json($response, 200);
    }
}
