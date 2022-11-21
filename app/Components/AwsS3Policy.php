<?php
/**
 * Created by PhpStorm.
 * User: konst
 * Date: 1/8/16
 * Time: 5:29 PM
 */

namespace App\Components;


use Aws\S3\S3Client;

class AwsS3Policy
{

    /**
     * Duration of the policy in minutes
     */
    const POLICY_DURATION = 60;
    protected $readCredentials;
    protected $writeCredentials;
    protected $bucket;
    protected $region;

    public function __construct($bucket = null, $region = null, $readUser = null, $writeUser = null)
    {
        $defaults = $this->getDefaults();
        $this->bucket = ($bucket === null ? $defaults['bucket'] : $bucket);
        $this->region = ($region === null ? $defaults['region'] : $region);
        $this->readCredentials = ($readUser === null ? $defaults['readUser'] : $readUser);
        $this->writeCredentials = ($writeUser === null ? $defaults['writeUser'] : $writeUser);
    }

    protected function getDefaults()
    {
        $readUser = [
            'key' => env('S3_READ_USER_KEY'),
            'secret' => env('S3_READ_USER_SECRET')
        ];
        $writeUser = [
            'key' => env('S3_WRITE_USER_KEY'),
            'secret' => env('S3_WRITE_USER_SECRET')
        ];
        $bucket = env('S3_BUCKET');
        if (env('S3_REGION')) {
            $region = env('S3_REGION');
        }
        return compact('readUser', 'writeUser', 'bucket', 'region');
    }

    /**
     * @param array $config Field with the file name is required
     *
     * @return array
     */
    public function getUploadPolicyWithSignature($config = [])
    {
        $policy = $this->createUploadPolicy($config);
        $signature = $this->generateSignature(base64_encode($policy), $this->writeCredentials);
        return [
            'policy' => base64_encode($policy),
            'signature' => base64_encode($signature)
        ];
    }

    /**
     * @param array $config
     *
     * @return string
     */
    public function createUploadPolicy($config = [])
    {
        $config = array_merge([
            'key' => '',
            'Content-Type' => '',
            'size' => [0, 1024 * 1024 * 20] // 20 MB
        ], $config);
        $expirationDate = new \DateTime('+' . self::POLICY_DURATION . ' minutes', new \DateTimeZone('UTC'));
        $conditions = [
            ['acl' => 'private'],
            ['bucket' => $this->bucket]
        ];
        foreach ($config as $field => $rule) {
            if ($field == 'size') {
                $conditions[] = ['content-length-range', $rule[0], $rule[1]];
                continue;
            }
            $conditions[] = ['starts-with', '$' . $field, $rule];
        }
        if(isset($config['success_action_status'])) {
            $conditions[] = ['success_action_status' => $config['success_action_status']];
        }
        $policy = [
            'expiration' => $expirationDate->format('Y-m-d\TH:i:s\Z'),
            'conditions' => $conditions
        ];
        return json_encode($policy);
    }

    /**
     * @param                $payload
     * @param                $credentials
     *
     * @return string
     */
    public function generateSignature($payload, $credentials)
    {
        return hash_hmac(
            'sha1',
            $payload,
            $credentials['secret'],
            true
        );
    }

    /**
     * @param string $object   Key of the object in S3
     * @param int    $lifetime How long the link should be valid. 20 minutes by default
     *
     * @return string
     */
    public function getSignedUrl($object, $lifetime = 20)
    {
        $s3Client = $this->createClient($this->readCredentials, $this->region);
        $cmd = $s3Client->getCommand('GetObject', [
            'Bucket' => $this->bucket,
            'Key' => $object,
            'ResponseContentType' => 'application/octet-stream'
        ]);
        $request = $s3Client->createPresignedRequest($cmd, "+$lifetime minutes");
        return (string)$request->getUri();
    }

    public function delete($keyName)
    {
        $s3Client = new S3Client([
            'region' => env('S3_REGION'),
            'version' => 'latest',
            'credentials' => $this->writeCredentials
        ]);

        $s3Client->deleteObject(array(
            'Bucket' => $this->bucket,
            'Key'    => $keyName
        ));
    }

    protected function createClient($credentials, $region)
    {
        return new S3Client([
            'region' => $region,
            'version' => 'latest',
            'credentials' => $credentials
        ]);
    }
}