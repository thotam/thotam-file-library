<?php

namespace Thotam\ThotamFileLibrary\Services;

use Google_Client;
use Google_Service_YouTube;
use Thotam\ThotamFileLibrary\Services\Traits\ClientTraits;

class Youtube
{
    use ClientTraits;

    public $service;

	public $client, $videoId, $snippet;

	public function __construct($refreshToken = NULL, $clientSecret = NULL, $clientId = NULL)
	{
		$this->client = new Google_Client();
        $this->setClientSecret($clientSecret);
        $this->setClientId($clientId);
        $this->refreshToken($refreshToken);
		$this->service = new Google_Service_YouTube($this->client);
	}

    /**
     * @param $data
     * @param $privacyStatus
     * @param null $id
     * @return \Google_Service_YouTube_Video
     */
    private function getVideo($data, $privacyStatus, $id = null)
    {
        // Setup the Snippet
        $snippet = new \Google_Service_YouTube_VideoSnippet();

        if (array_key_exists('title', $data))       $snippet->setTitle($data['title']);
        if (array_key_exists('description', $data)) $snippet->setDescription($data['description']);
        if (array_key_exists('tags', $data))        $snippet->setTags($data['tags']);
        if (array_key_exists('category_id', $data)) $snippet->setCategoryId($data['category_id']);

        // Set the Privacy Status
        $status = new \Google_Service_YouTube_VideoStatus();
        $status->privacyStatus = $privacyStatus;

        // Set the Snippet & Status
        $video = new \Google_Service_YouTube_Video();
        if ($id)
        {
            $video->setId($id);
        }

        $video->setSnippet($snippet);
        $video->setStatus($status);

        return $video;
    }

    /**
     * Upload the video to YouTube
     *
     * @param  string $path
     * @param  array $data
     * @param  string $privacyStatus
     * @return self
     */
    public function upload($path, array $data = [], $privacyStatus = 'unlisted')
    {
        $video = $this->getVideo($data, $privacyStatus);

        // Set the Chunk Size
        $chunkSize = config('thotam-file-library.youtube.chunkSize');

        // Set the defer to true
        $this->client->setDefer(true);

        // Build the request
        $insert = $this->service->videos->insert('status,snippet', $video);

        // Upload
        $media = new \Google_Http_MediaFileUpload(
            $this->client,
            $insert,
            'video/*',
            null,
            true,
            $chunkSize
        );

        // Set the Filesize
        $media->setFileSize(filesize($path));

        // Read the file and upload in chunks
        $status = false;
        $handle = fopen($path, "rb");

        while (!$status && !feof($handle)) {
            $chunk = fread($handle, $chunkSize);
            $status = $media->nextChunk($chunk);
        }

        fclose($handle);

        $this->client->setDefer(false);

        // Set ID of the Uploaded Video
        $this->videoId = $status['id'];

        // Set the Snippet from Uploaded Video
        $this->snippet = $status['snippet'];

        return $this;
    }
}
