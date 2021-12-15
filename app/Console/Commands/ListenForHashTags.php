<?php

namespace App\Console\Commands;

use App\Models\Tweets;
use App\Models\Topics;
use TwitterStreamingApi;
use App\Models\Website_Users;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;


class ListenForHashTags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twitter:listen-for-hash-tags {search_query} {search_mode}';
    protected int $counter = 1;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen for hashtags being used on Twitter';

    /**
     * Execute the console command.
     *
     * @return mixed
     */


    public function handle()
    {
        // User Input
        $search = $this->argument('search_query');
        $mode = $this->argument('search_mode');
        $tweets_model = new Tweets();
        if ($mode === "General") {
            TwitterStreamingApi::publicStream()
                ->whenHears($search, function (array $tweet) use ($tweets_model) {
                    $process = new Process(['python', 'assets/python/Preprocessor.py', json_encode(substr($tweet['text'], strpos($tweet['text'], ":") + 1))]);
                    $process->run();
                    // executes after the command finishes
                    if (!$process->isSuccessful()) {
                        throw new ProcessFailedException($process);
                    }
                    $tweets_model->insert([
                        [
                            'id' => $this->counter,
                            'web_userId' => 1,
                            'topic_id' => 2,
                            'twitter_userId' => "{$tweet['user']['name']}",
                            'twitter_user_screenname' => "{$tweet['user']['screen_name']}",
                            'tweet_text' => json_decode($process->getOutput(), true),
                        ],
                    ]);
                    $this->counter = $this->counter + 1;
                    if ($this->counter > 10) {
                        exit();
                    }
                }
                )->startListening();
        } else {
            TwitterStreamingApi::publicStream()->whenHears($search . " #StartUp", function (array $tweet) use ($tweets_model) {
                // Preprocessing Process
                $preprocessing_process = new Process(['python', 'public/assets/python_code/Get_Sentiment.py', json_encode(substr($tweet['text'], strpos($tweet['text'], ":") + 1))]);
                $preprocessing_process->run();
                // executes after the command finishes
                if (!$preprocessing_process->isSuccessful()) {
                    throw new ProcessFailedException($preprocessing_process);
                }

                $tweets_model->insert([
                    [
                        'id' => $this->counter,
                        'web_userId' => 1,
                        'topic_id' => 2,
                        'twitter_userId' => "{$tweet['user']['name']}",
                        'twitter_user_screenname' => "{$tweet['user']['screen_name']}",
                        'tweet_text' => json_decode($preprocessing_process->getOutput(), true),
                    ],
                ]);
                $this->counter = $this->counter + 1;
                if ($this->counter > 10) {
                    exit();
                }
            }
            )->startListening();
        }

    }
}
