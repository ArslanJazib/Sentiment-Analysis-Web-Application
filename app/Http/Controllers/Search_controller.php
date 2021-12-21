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
    // Used for twitter api
    protected $twitter;
    // Used for working with tweets table
    protected $tweets_model;
    // Used for working with topics table
    protected $topics_model;
    // Used for working with web users table
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
                $search = $search . " #StartUp";
                // Search Parameter with language as English and total count as 10
                $getfield = "?q=$search&lang=en&count=3";
            } else {
                // Search Parameter with language as English and total count as 10
                $getfield = "?q=$search&lang=en&count=3";
            }

            // This function will insert data in the web user & topic table
            $this->insert_user_topic_data($request, $search);

            $userId = $this->webUsers_model->select('web_userId')
                ->where('user_ip', '=', $request->ip())
                ->get()
                ->toArray();
            $userId=$userId[0]['web_userId'];

            $topicId = $this->topics_model->select('topic_id')
                ->where('web_userId', '=', $userId)
                ->get()
                ->toArray();
            $topicId=$topicId[0]['topic_id'];

            // Request is sent to Twitter API
            $tweets = json_decode($this->twitter->setGetfield($getfield)
                ->buildOauth($url, $requestMethod)
                ->performRequest());
            //Twitter API Exception

            foreach ($tweets as $tweets_obj) {
                foreach ($tweets_obj as $tweet) {
                    if (isset($tweet->text)) {
                        // To get the day the tweet was posted on
                        $day = date('D', strtotime($tweet->created_at));
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
                        $this->insert_tweets_data($userId, $topicId, $tweet, $preprocessor_process, $sentimentAnalysis_process,$day);
                    }
                }
            }
        }
        //In case we want to put multiple requests to get higher number of tweets
        //            $tweets = array();
        //            for ($i = 0; $i < 1; $i++) {
        //                $tweets_results = json_decode($this->twitter->setGetfield($getfield)
        //                    ->buildOauth($url, $requestMethod)
        //                    ->performRequest());
        //                if (isset($tweets_results->search_metadata->next_results)) {
        //                    // Get max_id
        //                    $max_id = preg_replace('/.*?max_id=([\d]+)&.*/', '$1', $tweets_results->search_metadata->next_results);
        //                    // Add max_id to
        //                    $getfield = $getfield . "&max_id=" . $max_id;
        //                    $tweets[] = $tweets_results;
        //
        //                } else {
        //                    $tweets[] = $tweets_results;
        //                    break;
        //                }
        //            }
    }

    public function check_user($user_ip)
    {
        // Checking if the same IP exists in the table or not
        $user = $this->webUsers_model->select('user_ip')
            ->where('user_ip', '=', $user_ip)
            ->get();
        return $user->count();
    }

    public function insert_user_topic_data($request, $search)
    {
        $topic = null;
        $userId=null;
        if ($this->check_user($request->ip()) > 0) {
            $userId = $this->webUsers_model->select('web_userId')
                ->where('user_ip', '=', $request->ip())
                ->get()
                ->toArray();
            $userId=$userId[0]['web_userId'];

            $topic = $this->topics_model->select('topic')
                ->where('web_userId', '=', $userId)
                ->get()
                ->toArray();
        } else {
            $user_counter = $this->webUsers_model::max('web_userId');
            $userId=$user_counter+1;
            // Inserting data in the website_users table
            $this->webUsers_model->insert([
                [
                    'web_userId' => $user_counter + 1,
                    'user_ip' => $request->ip(),
                ],
            ]);
        }

        if (!(isset($topic))) {
            $topic_counter = $this->topics_model::max('topic_id');
            // Inserting data in the topics table
            $this->topics_model->insert([
                [
                    'topic_id' => $topic_counter + 1,
                    'web_userId' => $userId,
                    'topic' => $search,
                ],
            ]);
        } else {
            $flag=false;
            foreach ($topic as $current_topic){
                if($current_topic["topic"]==$search){
                    $flag=true;
                    break;
                }
            }
            if($flag!=true){
                $topic_counter = $this->topics_model::max('topic_id');
                // Inserting data in the topics table
                $this->topics_model->insert([
                    [
                        'topic_id' => $topic_counter + 1,
                        'web_userId' => $userId,
                        'topic' => $search,
                    ],
                ]);
            }
        }
    }

    public function insert_tweets_data($userId, $topicId, $tweet, $preprocessor_process, $sentimentAnalysis_process,$day)
    {
        // Using the max value to increment the tweet
        $tweets_counter = $this->tweets_model::max('id');

        // Inserting data in the website_users table
        $this->tweets_model->insert([
            [
                'id' => $tweets_counter + 1,
                'web_userId' => $userId,
                'topic_id' => $topicId,
                'twitter_userId' => $tweet->user->name,
                'twitter_user_screenname' => $tweet->user->screen_name,
                'tweet_text' => json_decode($preprocessor_process->getOutput(), true),
                'sentiment' => json_decode($sentimentAnalysis_process->getOutput(), true),
                'day' => $day,
            ],
        ]);
    }

    public function visualize_data(Request $request)
    {
        $userId = $this->webUsers_model->select('web_userId')
            ->where('user_ip', '=', $request->ip())
            ->get()
            ->toArray();
        $userId=$userId[0]['web_userId'];

        $topics = $this->topics_model->select('topic_id','topic')
            ->where('web_userId', '=', $userId)
            ->get()
            ->toArray();

        $topicId=null;
        foreach ($topics as $current_topic){
            if ($request->get('modeChoice') === "Start-Up") {
                if($current_topic["topic"]=="#".$request->get('searchRequest')." #StartUp"){
                    $topicId=$current_topic["topic_id"];
                    break;
                }
            } else {
                if($current_topic["topic"]=="#".$request->get('searchRequest')){
                    $topicId=$current_topic["topic_id"];
                    break;
                }
            }
        }
        $negatives = $this->tweets_model->select('sentiment')
            ->where('sentiment', '=', 0)
            ->where('topic_id', '=', $topicId)
            ->where('web_userId', '=', $userId)
            ->get();

        $positives = $this->tweets_model->select('sentiment')
            ->where('sentiment', '=', 1)
            ->where('topic_id', '=', $topicId)
            ->where('web_userId', '=', $userId)
            ->get();

//        $days=array("Mon","Tue","Wed","Thu","Fri","Sat","Sun");
//        $day_Sentiment=array("Mon"=>0,"Tue"=>0,"Wed"=>0,"Thu"=>0,"Fri"=>0,"Sat"=>0,"Sun"=>0);
//        foreach ($days as $day){
//            $daypositives = count($this->tweets_model->select('*')
//                ->where('sentiment', '=', 1)
//                ->where('topic_id', '=', $topicId)
//                ->where('web_userId', '=', $userId)
//                ->where('day', '=', $day)
//                ->get());
//            $daynegatives = count($this->tweets_model->select('*')
//                ->where('sentiment', '=', 0)
//                ->where('topic_id', '=', $topicId)
//                ->where('web_userId', '=', $userId)
//                ->where('day', '=', $day)
//                ->get());
//            if($daypositives>$daynegatives){
//                $day_Sentiment[$day]=1;
//            }
//
//        }
        if($request->get('modeChoice')=="Start-Up"){
            $topic_input = $request->get('searchRequest') . " & StartUp";
        }
        else{
            $topic_input=$request->get('searchRequest');
        }
//        $sentiment = array("daySentiment"=>$day_Sentiment,"previousTopics"=>$topics,"total_positives" => count($positives), "total_negatives" => count($negatives), "topic" => $topic_input, "mode" => $request->get('modeChoice'));

        $sentiment = array("previousTopics"=>$topics,"total_positives" => count($positives), "total_negatives" => count($negatives), "topic" => $topic_input, "mode" => $request->get('modeChoice'));
        return view('Visualization_view', ['sentiment' => $sentiment]);
    }

    public function topic_visualize_data(Request $request)
    {
        $userId = $this->webUsers_model->select('web_userId')
            ->where('user_ip', '=', $request->ip())
            ->get()
            ->toArray();
        $userId=$userId[0]['web_userId'];

        $topics = $this->topics_model->select('topic','topic_id')
            ->where('web_userId', '=', $userId)
            ->get()
            ->toArray();

        $topicId=$request->get('topicChoice');
        $topic = $this->topics_model->select('topic')
            ->where('topic_id', '=', $topicId)
            ->get()
            ->toArray();
        $topic=$topic[0]['topic'];
        $topic=ltrim($topic, $topic[0]);

        $negatives = $this->tweets_model->select('sentiment')
            ->where('sentiment', '=', 0)
            ->where('topic_id', '=', $topicId)
            ->where('web_userId', '=', $userId)
            ->get();

        $positives = $this->tweets_model->select('sentiment')
            ->where('sentiment', '=', 1)
            ->where('topic_id', '=', $topicId)
            ->where('web_userId', '=', $userId)
            ->get();

//        $days=array("Mon","Tue","Wed","Thu","Fri","Sat","Sun");
//        $day_Sentiment=array("Mon"=>0,"Tue"=>0,"Wed"=>0,"Thu"=>0,"Fri"=>0,"Sat"=>0,"Sun"=>0);
//        foreach ($days as $day){
//            $daypositives = count($this->tweets_model->select('*')
//                ->where('sentiment', '=', 1)
//                ->where('topic_id', '=', $topicId)
//                ->where('web_userId', '=', $userId)
//                ->where('day', '=', $day)
//                ->get());
//            $daynegatives = count($this->tweets_model->select('*')
//                ->where('sentiment', '=', 0)
//                ->where('topic_id', '=', $topicId)
//                ->where('web_userId', '=', $userId)
//                ->where('day', '=', $day)
//                ->get());
//            if($daypositives>$daynegatives){
//                $day_Sentiment[$day]=1;
//            }
//            elseif ($daypositives<$daynegatives){
//                $day_Sentiment[$day]=0;
//            }
//            else{
//                $day_Sentiment[$day]=1;
//            }
//        }

        if($request->get('modeChoice')=="Start-Up"){
            $topic_input = $request->get('searchRequest') . " & StartUp";
        }
        else{
            $topic_input=$request->get('searchRequest');
        }

//        $sentiment = array("daySentiment"=>$day_Sentiment,"previousTopics"=>$topics,"total_positives" => count($positives), "total_negatives" => count($negatives), "topic" => $topic, "mode" => $request->get('modeChoice'));
        $sentiment = array("previousTopics"=>$topics,"total_positives" => count($positives), "total_negatives" => count($negatives), "topic" => $topic, "mode" => $request->get('modeChoice'));

        return view('Visualization_view', ['sentiment' => $sentiment]);
    }

}

?>
