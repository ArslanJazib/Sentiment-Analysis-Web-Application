<?php
namespace App\Http\Controllers;

use App\Models\Tweets;
use App\Models\Topics;
use TwitterStreamingApi;
use Illuminate\Http\Request;
use App\Models\Website_Users;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\TwitterAPIExchange;
use Symfony\Component\Process\Exception\ProcessFailedException;

class Search_controller extends Controller
{
    protected $twitter;
    protected $tweets_model;
    protected $topics_model;
    protected $webUsers_model;

    public function __construct()
    {
        /** Set access tokens here - see: https://dev.twitter.com/apps/ **/
        $settings = array(
            'oauth_access_token' => "1089143318380986373-fxmqHcDgZJ0GUNLiwCslHJckcU55VR",
            'oauth_access_token_secret' => "ws7iluXMr15eQykR1ur771v87K2p4mPbxzhMutVr6AI73",
            'consumer_key' => "hqdSn31cvUUMEZZwqJreGDsaZ",
            'consumer_secret' => "ZWJEHxuJB9DKw4HEDTZ4tMM0h4BpFTEXMNZBb3aIsvt8aMbxJP"
        );
        // Twitter API Authentication
        $this->twitter = new TwitterAPIExchange($settings);
        // tweets table Model initialization
        $this->tweets_model = new Tweets();
        // topics table Model initialization
        $this->topics_model = new Topics();
        // website_users table Model initialization
        $this->webUsers_model = new Website_Users();
    }

    public function index()
    {
        return view('Search_view');
    }

    public function search_processor(Request $request)
    {
        // Form validation rules
        $validator = Validator::make($request->all(), [
            'searchRequest' => ['required', 'alpha_num'],
        ]);

        // If anything input is not matching the rules reload the page and display errors
        if ($validator->fails()) {
            return redirect(url('/Search'))
                ->withErrors($validator)
                ->withInput();
        } else {
            // Data is sent from the form via POST method
            $search = $request->get('searchRequest');
            $mode = $request->get('modeChoice');
            // We are using # along with the search item
            $search = "#" . $search;
            // Twitter Search API URL
            $url = "https://api.twitter.com/1.1/search/tweets.json";
            // Request parameters are via GET method to Twitter API
            $requestMethod = "GET";
            if ($mode === "Start-Up") {
                $search_startup = $search . " #StartUp";
                // Search Parameter with language as English and total count as 10
                $getfield = "?q=$search_startup&lang=en&count=10";
            } else {
                // Search Parameter with language as English and total count as 10
                $getfield = "?q=$search&lang=en&count=10";
            }

            // Request is sent to Twitter API
            $tweets = array();
            for ($i = 0; $i < 1; $i++) {
                $tweets_results = json_decode($this->twitter->setGetfield($getfield)
                    ->buildOauth($url, $requestMethod)
                    ->performRequest());
                if (isset($tweets_results->search_metadata->next_results)) {
                    // Get max_id
                    $max_id = preg_replace('/.*?max_id=([\d]+)&.*/', '$1', $tweets_results->search_metadata->next_results);
                    // Add max_id to
                    $getfield = $getfield . "&max_id=" . $max_id;
                    $tweets[] = $tweets_results;

                } else {
                    $tweets[] = $tweets_results;
                    break;
                }
            }

            // This function will insert data in the web user & topic table
            $this->insert_user_topic_data($request, $search);

            foreach ($tweets as $tweets_ten) {
                foreach ($tweets_ten as $hundred_tweets) {
                    foreach ($hundred_tweets as $tweet) {
                        if (isset($tweet->text)) {
                            // This process executes the python script to pre-process a tweet
                            $preprocessor_process = new Process(['python', 'assets/python/Preprocessor.py', json_encode(substr($tweet->text, strpos($tweet->text, ":") + 1))]);
                            $preprocessor_process->run();
                            // Executes after the command finishes
                            if (!$preprocessor_process->isSuccessful()) {
                                throw new ProcessFailedException($preprocessor_process);
                            }
                            // Pre-processed Tweet
                            $preprocessed_tweet = ($preprocessor_process->getOutput());
                            // This process executes the python script to analyze sentiment
                            $sentimentAnalysis_process = new Process(['python', 'assets/python/SentimentAnalyzer.py', ($preprocessed_tweet)]);
                            $sentimentAnalysis_process->setTimeout(null);
                            $sentimentAnalysis_process->setIdleTimeout(null);
                            $sentimentAnalysis_process->run();
                            // Executes after the command finishes
                            if (!$sentimentAnalysis_process->isSuccessful()) {
                                throw new ProcessFailedException($sentimentAnalysis_process);
                            }

                            // This function will insert data in the tweets table
                            $this->insert_tweets_data($tweet, $preprocessor_process, $sentimentAnalysis_process);
                        }
                    }
                }
            }
        }
    }

    public function insert_user_topic_data($request, $search)
    {
        // Using the max value to increment the web user id and the topic id
        $user_counter = $this->webUsers_model::max('web_userId');
        $topic_counter = $this->topics_model::max('topic_id');

        // Inserting data in the website_users table
        $this->webUsers_model->insert([
            [
                'web_userId' => $user_counter + 1,
                'user_ip' => $request->ip(),
            ],
        ]);

        // Inserting data in the tweets table
        $this->topics_model->insert([
            [
                'topic_id' => $topic_counter + 1,
                'web_userId' => $user_counter + 1,
                'topic' => $search,
            ],
        ]);
    }

    public function insert_tweets_data($tweet, $preprocessor_process, $sentimentAnalysis_process)
    {
        // Using the max value to increment the web user id, topic id
        $user_counter = $this->webUsers_model::max('web_userId');
        $topic_counter = $this->topics_model::max('topic_id');
        $tweets_counter = $this->tweets_model::max('id');

        // Inserting data in the website_users table
        $this->tweets_model->insert([
            [
                'id' => $tweets_counter + 1,
                'web_userId' => $user_counter + 1,
                'topic_id' => $topic_counter + 1,
                'twitter_userId' => $tweet->user->name,
                'twitter_user_screenname' => $tweet->user->screen_name,
                'tweet_text' => json_decode($preprocessor_process->getOutput(), true),
                'sentiment' => json_decode($sentimentAnalysis_process->getOutput(), true),
            ],
        ]);
    }

    public function visualize_data(Request $request)
    {
        $negatives = $this->tweets_model->select('sentiment')
            ->where('sentiment', '=', 0)
            ->get();

        $positives = $this->tweets_model->select('sentiment')
            ->where('sentiment', '=', 1)
            ->get();

        $sentiment = array("total_positives" => count($positives), "total_negatives" => count($negatives), "topic" => $request->get('searchRequest'));

        return view('Visualization_view', ['sentiment' => $sentiment]);
    }
}

?>
