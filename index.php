<?php

require_once('TwitterAPIExchange.php');

/** Set access tokens here - `984trwer-98756r44: https://dev.twitter.com/apps/ **/




/** Perform a GET request and echo the response **/
/** Note: Set the GET field BEFORE calling buildOauth(); **/

class SimpleTwitter {

    private $screen_name;
    private $user_id;
    private $limit;

    private $settings;


    public function __construct(){
        $this->setApiInfo();
    }


    private function setApiInfo() {
        $this->settings = array(
            'oauth_access_token'        => '1397083766-AlSJKhl9RwsvVbVtqYQG9o4J2TKIKhhqWBQd20x',
            'oauth_access_token_secret' => 'Vgs0z76VmmU8AyuU7YZVjtdoUz3rvdatVW9rQn9xAtAOn',
            'consumer_key'              => '616wbg9OfXdPQviidFbkfG8wF',
            'consumer_secret'           => 'neItvosic8UN9zXndDCwvDXCiPbmz9PuX6UdqwR2FvlYZm3xhh'
        );

        $this->screen_name = 'sorwar_hossain5';
        $this->user_id = 1397083766;
        $this->limit = 1;
    }



    private function fetchRecentTweents(){

        $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';

        $getfield = '?screen_name=' . $this->screen_name . '&count=' . $this->limit;
        $requestMethod = 'GET';
        $twitter = new TwitterAPIExchange($this->settings);
        $query = $twitter->setGetfield($getfield)
                     ->buildOauth($url, $requestMethod)
                     ->performRequest(true, array(CURLOPT_CAINFO => dirname(__FILE__) . '/cacert.pem'));
        
        $timelines = json_decode($query);

        // return the last tweet
        return $timelines;

    }



    private function fetchTwitterProfile(){
        $url = 'https://api.twitter.com/1.1/users/show.json';
        $getfield = '?screen_name=' . $this->screen_name . '&user_id=' . $this->user_id;
        $requestMethod = 'GET';

        $twitter = new TwitterAPIExchange($this->settings);

        $query = $twitter->setGetfield($getfield)
                     ->buildOauth($url, $requestMethod)
                     ->performRequest(true, array(CURLOPT_CAINFO => dirname(__FILE__) . '/cacert.pem'));
        $profile = json_decode($query);

        // return the profile information
        return $profile;
    }



    public function getTwitterWidget(){
        $profile = $this->fetchTwitterProfile();

        $tweets = $this->fetchRecentTweents();

        $output = '<div class="twitter_widget_wrapper">';
            $output .= '<img src="'. $profile->profile_image_url_https .'" alt="">';
            $output .= '<h4>' . $profile->name . '</h4>';
            $output .= '<h5><a href="https://twitter.com/'. $profile->screen_name .'">@' . $profile->screen_name . '</a></h5>';

            foreach ($tweets as $tweet) {
                $output .= '<div class="recent_tweet">';
                    $output .= '<p>'. $tweet->text .'</p>';
                    $output .= '<small>'. $this->formatTweetTime($tweet->created_at) .'</small>';
                $output .= '</div>';
            }
            
        $output .= '</div>';

        return $output;
    }






    private function formatTweetTime( $time ) {
        // Get current timestamp.
        $now = strtotime( 'now' );
     
        // Get timestamp when tweet created.
        $created = strtotime( $time );
     
        // Get difference.
        $difference = $now - $created;
     
        // Calculate different time values.
        $minute = 60;
        $hour = $minute * 60;
        $day = $hour * 24;
        $week = $day * 7;
     
        if ( is_numeric( $difference ) && $difference > 0 ) {
     
            // If less than 3 seconds.
            if ( $difference < 3 ) {
                return __( 'Just now', 'twitter_tweets_widget' );
            }
     
            // If less than minute.
            if ( $difference < $minute ) {
                return floor( $difference ) . ' ' . __( 'seconds ago', 'twitter_tweets_widget' );;
            }
     
            // If less than 2 minutes.
            if ( $difference < $minute * 2 ) {
                return __( '1 minute ago', 'twitter_tweets_widget' );
            }
     
            // If less than hour.
            if ( $difference < $hour ) {
                return floor( $difference / $minute ) . ' ' . __( 'minutes ago', 'twitter_tweets_widget' );
            }
     
            // If less than 2 hours.
            if ( $difference < $hour * 2 ) {
                return __( '1 hour ago', 'twitter_tweets_widget' );
            }
     
            // If less than day.
            if ( $difference < $day ) {
                return floor( $difference / $hour ) . ' ' . __( 'hours ago', 'twitter_tweets_widget' );
            }
     
            // If more than day, but less than 2 days.
            if ( $difference > $day && $difference < $day * 2 ) {
                return __( 'yesterday', 'twitter_tweets_widget' );;
            }
     
            // If less than year.
            if ( $difference < $day * 365 ) {
                return floor( $difference / $day ) . ' ' . __( 'days ago', 'twitter_tweets_widget' );
            }
     
            // Else return more than a year.
            return __( 'over a year ago', 'twitter_tweets_widget' );
        }
    }



}



$twitter = new SimpleTwitter();


echo $twitter->getTwitterWidget();