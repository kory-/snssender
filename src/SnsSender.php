<?php namespace Socialgrid\SnsSender;

use Aws\Sns\SnsClient;

use Illuminate\Contracts\Foundation\Application;
use Exception;
use InvalidArgumentException;

class SnsSender
{
    /**
     * The Laravel application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /** @var string $region */
    private $region;
    /** @var string $access_key */
	private $accessKey;
	/** @var string $secret_key */
	private $secretKey;
    /** @var string $secret_key */
	private $version;

    /** AWS SNS sender **/
    private $client;

    /**
     * construct
     *
     * @param string region
     * @param string accessKey
     * @param string secretKey
     */
    public function __construct($app = null)
    {
        if (!$app) {
            $app = app();
        }
        $this->app = $app;
        $this->region = $this->app['config']->get('snssender.region');
        $this->accessKey = $this->app['config']->get('snssender.credentials.key');
        $this->secretKey = $this->app['config']->get('snssender.credentials.secret');
        $this->version = $this->app['config']->get('snssender.version');

        if(empty($this->region)) {
            throw new InvalidArgumentException('Must define region');
        }

        if(empty($this->accessKey) || empty($this->secretKey)) {
			throw new InvalidArgumentException('Must define Amazon access key and secret key');
		}

        $this->client = SnsClient::factory([
                'region' => $this->region,
                'version' => $this->version,
                'credentials' => [
                    'key' => $this->accessKey,
                    'secret' => $this->secretKey,
                ]
            ]);
    }

    /**
	 * Publish a message to a topic
	 *
	 * @link http://docs.amazonwebservices.com/sns/latest/api/API_Publish.html
	 * @param string $targetArn
	 * @param string $message
	 * @param string $subject [optional] Used when sending emails
	 * @param string $messageStructure [optional] Used when you want to send a different message for each protocol.If you set MessageStructure to json, the value of the Message parameter must: be a syntactically valid JSON object; and contain at least a top-level JSON key of "default" with a value that is a string.
	 * @return string
	 * @throws InvalidArgumentException
	 */
	public function publish($targetArn, $message, $subject = '', $messageStructure = '') {
		if(empty($targetArn) || empty($message)) {
			throw new InvalidArgumentException('Must supply a TopicARN and Message to publish to a topic');
		}
		$params = array(
			'TargetArn' => $targetArn,
			'Message' => $message
		);
		if(!empty($subject)) {
			$params['Subject'] = $subject;
		}
		if(!empty($messageStructure)) {
			$params['MessageStructure'] = $messageStructure;
		}

        return $this->client->publish($params);
	}

    /**
	 * Create Platform endpoint
	 *
	 * @link http://docs.aws.amazon.com/sns/latest/api/API_CreatePlatformEndpoint.html
	 * @param string $platformApplicationArn
	 * @param string $token
	 * @param string $userData
	 * @param array $ttributes
	 * @return string
	 * @throws InvalidArgumentException
	 */
	public function createPlatformEndpoint($platformApplicationArn, $token, $userData, $attributes) {
		if(empty($platformApplicationArn) || empty($token) || empty($userData)) {
			throw new InvalidArgumentException('Must supply a PlatformApplicationArn,Token & UserData to create platform endpoint');
		}
        $result = $this->client->createPlatformEndpoint([
            'PlatformApplicationArn' => $platformApplicationArn,
            'Token' => $token,
            'CustomUserData' => $userData,
            'Attributes' => $attributes,
        ]);
		return $result;
	}

    /**
	 * Delete endpoint
	 *
	 * @link http://docs.aws.amazon.com/sns/latest/api/API_DeleteEndpoint.html
	 * @param string $deviceArn
	 *
	 * @return bool
	 * @throws InvalidArgumentException
	 */
	public function deleteEndpoint($deviceArn) {
		if(empty($deviceArn)) {
			throw new InvalidArgumentException('Must supply a DeviceARN to remove platform endpoint');
		}
		$result = $this->client->DeleteEndpoint([
			'EndpointArn' => $deviceArn,
        ]);
		return true;
	}


    // Publish to many endpoints the same notifications
    public function publishToEndpoints(
        $endpoints,
        $alert,
        $data,
        $providers = ["GCM","APNS"],
        $default = "")
    {
        foreach ($endpoints as $endpoint)
        {
            try {
                $this->publishToEndpoint($endpoint, $alert, $data, $providers, $default);
            }
            catch (\Exception $e) {
                print "ERROR publish to '$endpoint': ".$e->getMessage()."\n";
            }
        }
    }

}
