<?php

error_reporting(E_ALL); ini_set('display_errors', 1);

require_once(CONTEST_LIB_DIR . 'aweber/src/aweber_api/aweber_api.php');

class Contest_AweberSubscription
{
    private $adapter = NULL;

    private $consumerKey = NULL;
    private $consumerSecret = NULL;

    private $requestToken = NULL;
    private $tokenSecret = NULL;

    private $verifier = NULL;

    private $accessToken = NULL;
    private $accessSecret = NULL;

    private $accountId = '1167417';
    private $listId = NULL;

    /**
     * Contest_AweberSubscription constructor.
     * Initialize class variables and
     * store Aweber Core Class object in class variable
     *
     * Call function to set up adapter for requests
     */
    public function __construct($userId)
    {
        $this->consumerKey = get_user_meta($userId, CONTEST_PLUGIN_NAME.'_aweber_consumer_key', true);//'Az7hZzcPuiNxSUNAi90s4Xgn';
        $this->consumerSecret = get_user_meta($userId, CONTEST_PLUGIN_NAME.'_aweber_consumer_secret', true);//'IgxXOkZqQd4jNHe7nYaKVdtPt2K6kP8RqqjQEx3D';

        $this->requestToken = get_user_meta($userId, CONTEST_PLUGIN_NAME.'_aweber_request_token', true);//'Aqah3yCdJbs3g18coB1MIOBz';
        $this->tokenSecret = get_user_meta($userId, CONTEST_PLUGIN_NAME.'_aweber_token_secret', true);//'0344919M1LEWXXH9gvqP5dt5wIY3zNLiEK2Jt95i';

        $this->verifier = get_user_meta($userId, CONTEST_PLUGIN_NAME.'_aweber_verifier', true);//'92n9eo';

        $this->accessToken = get_user_meta($userId, CONTEST_PLUGIN_NAME.'_aweber_access_token', true);//'AgM9htyZ12fu78jBrF0RdLD2';
        $this->accessSecret = get_user_meta($userId, CONTEST_PLUGIN_NAME.'_aweber_access_secret', true);//'iaq3ASwOarhuAQkVyuyJytDxyt3NjlQ9YuDKKzi0';

        $this->accountId = get_user_meta($userId, CONTEST_PLUGIN_NAME.'_aweber_account_id', true);//'iaq3ASwOarhuAQkVyuyJytDxyt3NjlQ9YuDKKzi0';
        $this->listId = get_user_meta($userId, CONTEST_PLUGIN_NAME.'_aweber_list_id', true);//'iaq3ASwOarhuAQkVyuyJytDxyt3NjlQ9YuDKKzi0';

        $this->application = new AWeberAPI($this->consumerKey, $this->consumerSecret);
        $this->setup_auth_call_adapter();
    }


    /**
     * Function to get access tokens
     *
     * Require all other tokens and keys
     * @return array access token and access secret
     */
    public function connectToAWeberAccount()
    {
        $requestToken = $this->requestToken;
        $tokenSecret = $this->tokenSecret;

        $code = $this->verifier;

        $this->application->adapter->debug = true;

        $this->application->user->requestToken = $requestToken;
        $this->application->user->tokenSecret = $tokenSecret;
        $this->application->user->verifier = $code;

        list($accessToken, $accessSecret) = $this->application->getAccessToken();

        return array($accessToken, $accessSecret);
    }

    /**
     * This function is used to set up adapters for request
     */
    private function setup_auth_call_adapter()
    {
        $serviceProvider = new AWeberServiceProvider();
        $adapter = new OAuthApplication($serviceProvider);
        $adapter->consumerKey = $this->consumerKey;
        $adapter->consumerSecret = $this->consumerSecret;
        $this->adapter = $adapter;

        $user = new OAuthUser();
        $user->accessToken = $this->accessToken;
        $user->tokenSecret = $this->accessSecret;
        $this->adapter->user = $user;
    }

    /**
     * Make request call to Aweber to find if provided email user exists in aweber account or not
     *
     * @param string $email
     * @return bool
     */
    public function findSubscriber($email)
    {
        $params = array_merge(array('email' => $email), array('ws.op' => 'findSubscribers'));

        $url = "/accounts/{$this->accountId}";

        $data = $this->adapter->request('GET', $url, $params);

        if (!empty($data) && !empty($data['entries'])) {
            return $data['entries'][0];
        }
        return false;
    }

    /**
     * Make request call to Aweber to find if list exists exists in aweber account with provided name
     *
     * @param $listName
     * @return mixed
     */
    public function findList($listName)
    {
        $url = "/accounts/{$this->accountId}/lists";
        try {
            $params = array_merge(array('ws.op' => 'find'), array('name'=>$listName));

            $data = $this->adapter->request('GET', $url, $params);

            if (!empty($data) && !empty($data['entries'])) {
                return $data['entries'][0];
            }
            return false;
        } catch (Exception $exc) {
            print $exc;
        }
    }

    public function addSubscriber($subscriber)
    {
        if (!$this->findSubscriber($subscriber['email'])) {
            $url = "/accounts/{$this->accountId}/lists/{$this->listId}/subscribers";
            try {
                $params = array_merge(array('ws.op' => 'create'), $subscriber);
                $data = $this->adapter->request('POST', $url, $params, array('return' => 'headers'));
            } catch (Exception $exc) {
                print $exc;
            }
        }
    }
}