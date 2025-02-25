<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Services\VideoService;
use App\Models\ExploreVideo;

class FetchYoutubeVideos extends Command
{
    protected $videoService;

    public function __construct(VideoService $videoService)
    {
        parent::__construct(); 
        $this->videoService = $videoService;
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-youtube-videos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and update YouTube video data for the explore tab';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $videos = ExploreVideo::all();
        $apiKey = config('services.youtube.api_key');

        foreach ($videos as $video) {
            $this->info("Fetching data for: {$video->youtubeVideo_id}");

            $response = Http::get('https://www.googleapis.com/youtube/v3/videos', [
                'id' => $video->youtubeVideo_id,
                'part' => 'snippet',
                'key' => $apiKey,
            ]);

            if ($response->successful()) {
                $videoData = $response->json();
                if (!empty($videoData['items'])) {
                    $video->video_resource = $this->videoService->formatExploreVideo($videoData['items'][0]);
                    $video->updated_at = now();
                    $video->save();
                }
            }
        }

        $this->info('YouTube videos update process completed.');
    }

}
